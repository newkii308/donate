<?php

namespace App\Services;

use App\Models\OverlaySetting;
use App\Models\TtsCache;
use App\Support\Money;
use Illuminate\Support\Facades\Storage;

class TtsService
{
    /**
     * Render a TTS template, replacing the supported variables. No sentence
     * is ever hardcoded — the streamer controls the whole template.
     *
     * Supported: {streamer_name} {donor_name} {amount} {currency} {message}
     *
     * @param  array<string, string>  $vars
     */
    public function render(string $template, array $vars): string
    {
        $replacements = [];
        foreach ($vars as $key => $value) {
            $replacements['{'.$key.'}'] = $value;
        }

        return trim(strtr($template, $replacements));
    }

    /**
     * Build the TTS portion of a notification payload from overlay settings.
     * Returns null when TTS is disabled for the streamer.
     *
     * @param  array<string, string>  $vars
     * @return array<string, mixed>|null
     */
    public function buildPayload(OverlaySetting $settings, array $vars): ?array
    {
        if (! $settings->tts_enabled) {
            return null;
        }

        $template = $settings->tts_template
            ?: (string) config('newlab.tts.default_template');

        $text = $this->render($template, $vars);

        if ($text === '') {
            return null;
        }

        $language = $settings->tts_language ?: (string) config('newlab.tts.default_language', 'lo-LA');

        $cache = $this->getOrCreateCache(
            $text,
            $language,
            $settings->tts_voice,
        );

        return [
            'enabled' => true,
            'provider' => (string) config('newlab.tts.provider', 'browser'),
            'text' => $text,
            'language' => $language,
            'voice' => $settings->tts_voice,
            'rate' => (float) $settings->tts_rate,
            'pitch' => (float) $settings->tts_pitch,
            'volume' => (int) $settings->tts_volume / 100,
            'audio_url' => $cache->path ? Storage::disk($cache->disk ?? 'public')->url($cache->path) : null,
        ];
    }

    /**
     * Look up a cached TTS entry by content hash, reusing existing audio when
     * possible. For the browser provider the cache simply records that this
     * exact text/voice combination was requested (path stays null).
     */
    public function getOrCreateCache(string $text, string $language, ?string $voice): TtsCache
    {
        $hash = sha1(implode('|', [$text, $language, (string) $voice]));

        $cache = TtsCache::where('hash', $hash)->first();

        if ($cache && ! $cache->isExpired()) {
            return $cache;
        }

        $expiresAt = now()->addDays((int) config('newlab.tts.cache_days', 7));

        if ($cache) {
            $cache->update(['expires_at' => $expiresAt]);

            return $cache;
        }

        return TtsCache::create([
            'hash' => $hash,
            'text' => $text,
            'language' => $language,
            'voice' => $voice,
            'disk' => null,
            'path' => null,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Delete expired cache entries (and any associated audio files).
     * Intended to be triggered from a Cron job on shared hosting.
     */
    public function pruneExpired(): int
    {
        $count = 0;

        TtsCache::query()->expired()->chunkById(100, function ($entries) use (&$count) {
            foreach ($entries as $entry) {
                if ($entry->path && $entry->disk) {
                    Storage::disk($entry->disk)->delete($entry->path);
                }
                $entry->delete();
                $count++;
            }
        });

        return $count;
    }

    /**
     * Convenience: standard variable set from a streamer + donation data.
     *
     * @return array<string, string>
     */
    public function variables(string $streamerName, string $donorName, float|string $amount, string $currency, ?string $message): array
    {
        return [
            'streamer_name' => $streamerName,
            'donor_name' => $donorName,
            'amount' => Money::format($amount),
            'currency' => $currency,
            'message' => (string) $message,
        ];
    }
}
