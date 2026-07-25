<?php

namespace App\Services;

use Throwable;

/**
 * เสียงพูด neural ภาษาลาวจาก Microsoft Edge "Read Aloud" (ฟรี ไม่ต้องใช้ API key)
 *
 * คุยกับ WebSocket ของ Microsoft ตรงๆ ด้วย socket ดิบ (ไม่พึ่ง composer เพิ่ม)
 * — ต้องมี openssl. คืนค่าเป็นไบต์ MP3 หรือ '' เมื่อดึงไม่สำเร็จ.
 *
 * หมายเหตุ: นี่คือ endpoint ที่ Edge ใช้จริง (unofficial) — ถ้า Microsoft เปลี่ยน
 * โครง อาจต้องปรับ. ระบบมีเสียงสำรอง (Google Translate) รองรับอยู่แล้ว.
 */
class EdgeTts
{
    private const TRUSTED_TOKEN = '6A5AA1D4EAFF4E9FB37E23D68491D6F4';
    private const HOST = 'speech.platform.bing.com';
    private const PATH = '/consumer/speech/synthesize/readaloud/edge/v1';
    private const GEC_VERSION = '1-130.0.2849.68';
    private const OUTPUT_FORMAT = 'audio-24khz-48kbitrate-mono-mp3';

    /**
     * @param  string  $text   ข้อความภาษาลาว
     * @param  string  $voice  เช่น lo-LA-KeomanyNeural
     * @param  int     $rate   ความเร็ว % (-100..100, 0 = ปกติ)
     * @param  int     $pitch  โทนเสียง Hz offset (เช่น -50..50, 0 = ปกติ)
     */
    public function synth(string $text, string $voice, int $rate = 0, int $pitch = 0): string
    {
        if (trim($text) === '' || ! extension_loaded('openssl')) {
            return '';
        }

        try {
            return $this->run($text, $voice, $rate, $pitch);
        } catch (Throwable) {
            return '';
        }
    }

    private function run(string $text, string $voice, int $rate, int $pitch): string
    {
        $query = http_build_query([
            'TrustedClientToken' => self::TRUSTED_TOKEN,
            'Sec-MS-GEC' => $this->gecToken(),
            'Sec-MS-GEC-Version' => self::GEC_VERSION,
            'ConnectionId' => $this->uuid(),
        ]);

        $ctx = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $sock = @stream_socket_client(
            'ssl://'.self::HOST.':443',
            $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx,
        );
        if (! $sock) {
            return '';
        }
        stream_set_timeout($sock, 15);

        // --- WebSocket upgrade handshake ---
        $key = base64_encode(random_bytes(16));
        $req = "GET ".self::PATH."?{$query} HTTP/1.1\r\n"
            ."Host: ".self::HOST."\r\n"
            ."Upgrade: websocket\r\n"
            ."Connection: Upgrade\r\n"
            ."Sec-WebSocket-Key: {$key}\r\n"
            ."Sec-WebSocket-Version: 13\r\n"
            ."Pragma: no-cache\r\n"
            ."Cache-Control: no-cache\r\n"
            ."Origin: chrome-extension://jdiccldimpnbgmabfggnadeogkfmnpelp\r\n"
            ."User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Edg/130.0.0.0\r\n"
            ."\r\n";
        fwrite($sock, $req);

        $status = $this->readHttpStatus($sock);
        if (! str_contains($status, '101')) {
            fclose($sock);
            return '';
        }

        // --- ส่ง config + ssml ---
        $ts = gmdate('D M d Y H:i:s').' GMT+0000 (Coordinated Universal Time)';

        $config = "X-Timestamp:{$ts}\r\nContent-Type:application/json; charset=utf-8\r\nPath:speech.config\r\n\r\n"
            .'{"context":{"synthesis":{"audio":{"metadataoptions":{"sentenceBoundaryEnabled":false,"wordBoundaryEnabled":false},"outputFormat":"'.self::OUTPUT_FORMAT.'"}}}}';
        $this->sendFrame($sock, 0x1, $config);

        $ssml = "<speak version='1.0' xmlns='http://www.w3.org/2001/10/synthesis' xml:lang='lo-LA'>"
            ."<voice name='".htmlspecialchars($voice, ENT_QUOTES)."'>"
            ."<prosody pitch='".sprintf('%+dHz', $pitch)."' rate='".sprintf('%+d%%', $rate)."' volume='+0%'>"
            .htmlspecialchars($text, ENT_QUOTES | ENT_XML1)
            ."</prosody></voice></speak>";
        $ssmlMsg = "X-RequestId:".$this->uuid()."\r\nContent-Type:application/ssml+xml\r\nX-Timestamp:{$ts}\r\nPath:ssml\r\n\r\n".$ssml;
        $this->sendFrame($sock, 0x1, $ssmlMsg);

        // --- รับ audio frames ---
        $audio = '';
        $deadline = microtime(true) + 20;

        while (microtime(true) < $deadline) {
            $frame = $this->readFrame($sock);
            if ($frame === null) {
                break;
            }
            [$opcode, $payload] = $frame;

            if ($opcode === 0x8) { // close
                break;
            }
            if ($opcode === 0x2) { // binary = audio
                $headerLen = (ord($payload[0]) << 8) | ord($payload[1]);
                $audio .= substr($payload, 2 + $headerLen);
            } elseif ($opcode === 0x1) { // text = control
                if (str_contains($payload, 'Path:turn.end')) {
                    break;
                }
            }
        }

        fclose($sock);

        return $audio;
    }

    /** โทเคนความปลอดภัยของ Edge (Sec-MS-GEC) = SHA256 ของ FILETIME(ปัดลง 5 นาที) + token */
    private function gecToken(): string
    {
        $ticks = (int) ((time() + 11644473600) * 10000000);
        $ticks -= $ticks % 3000000000; // ปัดลงทุก 5 นาที

        return strtoupper(hash('sha256', $ticks.self::TRUSTED_TOKEN));
    }

    private function uuid(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function readHttpStatus($sock): string
    {
        $headers = '';
        while (! str_contains($headers, "\r\n\r\n")) {
            $c = fread($sock, 1);
            if ($c === '' || $c === false) {
                break;
            }
            $headers .= $c;
            if (strlen($headers) > 4096) {
                break;
            }
        }

        return $headers;
    }

    /** ส่ง WebSocket frame แบบ masked (client → server ต้อง mask เสมอ) */
    private function sendFrame($sock, int $opcode, string $payload): void
    {
        $len = strlen($payload);
        $header = chr(0x80 | $opcode);

        if ($len <= 125) {
            $header .= chr(0x80 | $len);
        } elseif ($len <= 0xFFFF) {
            $header .= chr(0x80 | 126).pack('n', $len);
        } else {
            $header .= chr(0x80 | 127).pack('J', $len);
        }

        $mask = random_bytes(4);
        $masked = $payload ^ str_repeat($mask, (int) ($len / 4) + 1);

        fwrite($sock, $header.$mask.$masked);
    }

    /**
     * อ่าน WebSocket frame หนึ่งเฟรม (server → server ไม่ mask)
     *
     * @return array{0:int,1:string}|null
     */
    private function readFrame($sock): ?array
    {
        $h = $this->readN($sock, 2);
        if ($h === null) {
            return null;
        }

        $opcode = ord($h[0]) & 0x0F;
        $len = ord($h[1]) & 0x7F;

        if ($len === 126) {
            $ext = $this->readN($sock, 2);
            if ($ext === null) {
                return null;
            }
            $len = unpack('n', $ext)[1];
        } elseif ($len === 127) {
            $ext = $this->readN($sock, 8);
            if ($ext === null) {
                return null;
            }
            $len = unpack('J', $ext)[1];
        }

        $payload = $len > 0 ? $this->readN($sock, $len) : '';
        if ($payload === null) {
            return null;
        }

        return [$opcode, $payload];
    }

    /** อ่านให้ครบ n ไบต์ (loop กัน fread คืนไม่ครบ) */
    private function readN($sock, int $n): ?string
    {
        $buf = '';
        while (strlen($buf) < $n) {
            $chunk = fread($sock, $n - strlen($buf));
            if ($chunk === '' || $chunk === false) {
                $meta = stream_get_meta_data($sock);
                if (! empty($meta['timed_out']) || feof($sock)) {
                    return null;
                }
                continue;
            }
            $buf .= $chunk;
        }

        return $buf;
    }
}
