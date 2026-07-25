<?php

namespace App\Http\Controllers;

use App\Services\EdgeTts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

/**
 * พร็อกซีเสียงไทยฝั่งเซิร์ฟเวอร์ (Google Translate TTS) + แคชเป็นไฟล์
 *
 * แก้ปัญหาเครื่องผู้ใช้/เบราว์เซอร์ไม่มีเสียงไทย: เซิร์ฟเวอร์ดึงไฟล์ MP3 เสียงไทย
 * มาให้ แล้วเล่นแบบ same-origin (ไม่ติด CORS/autoplay ข้ามโดเมน) ใช้ได้ทั้งหน้าเว็บ
 * และ OBS overlay. ผลลัพธ์ถูกแคชไว้ใช้ซ้ำเพื่อลดการเรียกซ้ำ.
 */
class TtsController extends Controller
{
    private const MAX_CHARS = 600;     // จำกัดความยาวข้อความกันการใช้งานผิด
    private const CHUNK = 150;          // Google จำกัดความยาวต่อครั้ง ~200 ตัว

    public function speak(Request $request, EdgeTts $edge): Response
    {
        $text = trim((string) $request->query('q', ''));
        $lang = preg_replace('/[^a-z\-]/i', '', (string) $request->query('lang', 'lo')) ?: 'lo';
        $rate = max(-100, min(100, (int) $request->query('rate', 0)));   // %
        $pitch = max(-100, min(100, (int) $request->query('pitch', 0))); // Hz

        if ($text === '') {
            return response('', 204);
        }

        $text = mb_substr($text, 0, self::MAX_CHARS);

        // เสียง Edge ที่อนุญาต (ต้องอยู่ใน allowlist เท่านั้น กัน injection)
        $edgeVoice = null;
        $voiceParam = (string) $request->query('voice', '');
        if (str_starts_with($voiceParam, 'edge:')) {
            $candidate = substr($voiceParam, 5);
            if (array_key_exists($candidate, (array) config('newlab.tts.edge_voices', []))) {
                $edgeVoice = $candidate;
            }
        }

        $dir = storage_path('app/tts');
        $hash = sha1(implode('|', [$edgeVoice ?? 'google', $lang, $rate, $pitch, $text]));
        $file = $dir.DIRECTORY_SEPARATOR.$hash.'.mp3';
        $srcFile = $file.'.src';

        if (! is_file($file)) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }

            $audio = '';
            $source = 'google';

            // 1) ลองเสียง Edge neural ก่อน (ถ้าเลือกไว้)
            if ($edgeVoice !== null) {
                $audio = $edge->synth($text, $edgeVoice, $rate, $pitch);
                if ($audio !== '') {
                    $source = 'edge';
                }
            }

            // 2) fallback เสียง Google Translate
            if ($audio === '') {
                $audio = $this->fetch($text, $lang);
                $source = 'google';
            }

            if ($audio === '') {
                return response('', 502); // ดึงเสียงไม่สำเร็จทั้งสองทาง
            }

            @file_put_contents($file, $audio);
            @file_put_contents($srcFile, $source);
        }

        $source = is_file($srcFile) ? (string) file_get_contents($srcFile) : 'google';

        return response()->file($file, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'public, max-age=604800',
            'X-TTS-Source' => $source,
            'Access-Control-Expose-Headers' => 'X-TTS-Source',
        ]);
    }

    /**
     * ดึงเสียง MP3 จาก Google ทีละช่วง แล้วต่อไบต์เข้าด้วยกัน (MP3 เล่นต่อกันได้)
     */
    private function fetch(string $text, string $lang): string
    {
        $audio = '';

        foreach ($this->chunk($text, self::CHUNK) as $chunk) {
            if (trim($chunk) === '') {
                continue;
            }

            try {
                $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://translate.google.com/',
                ])
                    // ปิด SSL verify: PHP บางเครื่อง (เช่น Windows) ไม่มี CA bundle ตั้งไว้
                    ->withOptions(['verify' => false])
                    ->timeout(15)
                    ->get('https://translate.google.com/translate_tts', [
                    'ie' => 'UTF-8',
                    'client' => 'tw-ob',
                    'tl' => $lang,
                    'q' => $chunk,
                ]);

                if ($res->successful() && str_contains((string) $res->header('Content-Type'), 'audio')) {
                    $audio .= $res->body();
                }
            } catch (\Throwable $e) {
                // ข้ามช่วงที่ดึงไม่ได้
            }
        }

        return $audio;
    }

    /**
     * ตัดข้อความเป็นช่วงสั้น ๆ (พยายามตัดที่ช่องว่างก่อน ไม่งั้นตัดตามจำนวนตัวอักษร
     * เพราะภาษาไทยมักไม่มีช่องว่างระหว่างคำ)
     *
     * @return array<int, string>
     */
    private function chunk(string $text, int $max): array
    {
        if (mb_strlen($text) <= $max) {
            return [$text];
        }

        $out = [];
        $cur = '';

        foreach (preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$text] as $word) {
            if (mb_strlen($word) > $max) {
                foreach (mb_str_split($word, $max) as $piece) {
                    if ($cur !== '' && mb_strlen($cur.$piece) > $max) {
                        $out[] = $cur;
                        $cur = '';
                    }
                    $cur .= $piece;
                }

                continue;
            }

            if ($cur !== '' && mb_strlen($cur.$word) > $max) {
                $out[] = $cur;
                $cur = '';
            }
            $cur .= $word;
        }

        if (trim($cur) !== '') {
            $out[] = $cur;
        }

        return $out ?: [$text];
    }
}
