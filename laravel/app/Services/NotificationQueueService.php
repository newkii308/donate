<?php

namespace App\Services;

use App\Enums\NotificationStatus;
use App\Models\Donation;
use App\Models\NotificationQueue;
use App\Models\OverlaySetting;
use App\Models\Streamer;
use App\Support\Money;
use Illuminate\Support\Facades\DB;

class NotificationQueueService
{
    public function __construct(private readonly TtsService $tts)
    {
    }

    /**
     * Queue a real donation alert for the overlay. Returns null when the
     * donation is below the streamer's configured minimum display amount
     * (the donation itself is still recorded — only the alert is skipped).
     */
    public function enqueueDonation(Donation $donation): ?NotificationQueue
    {
        $streamer = $donation->streamer;
        $settings = $this->settingsFor($streamer);

        if ((float) $donation->amount < (float) $settings->min_amount_to_show) {
            return null;
        }

        $payload = $this->buildPayload($streamer, $settings, [
            'donor_name' => $donation->displayName(),
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'message' => $donation->message,
        ], isTest: false);

        return NotificationQueue::create([
            'streamer_id' => $streamer->id,
            'donation_id' => $donation->id,
            'payload' => $payload,
            'status' => NotificationStatus::Pending->value,
            'is_test' => false,
        ]);
    }

    /**
     * Queue a test alert (no real donation is created).
     *
     * @param  array{donor_name?:string, amount?:float|string, message?:?string}  $data
     */
    public function enqueueTest(Streamer $streamer, array $data): NotificationQueue
    {
        $settings = $this->settingsFor($streamer);

        $payload = $this->buildPayload($streamer, $settings, [
            'donor_name' => $data['donor_name'] ?? 'ຜູ້ທົດສອບ',
            'amount' => $data['amount'] ?? 50000,
            'currency' => $streamer->currency,
            'message' => $data['message'] ?? 'ນີ້ແມ່ນການແຈ້ງເຕືອນທົດສອບ',
        ], isTest: true);

        return NotificationQueue::create([
            'streamer_id' => $streamer->id,
            'donation_id' => null,
            'payload' => $payload,
            'status' => NotificationStatus::Pending->value,
            'is_test' => true,
        ]);
    }

    /**
     * Build the full alert payload snapshot for the overlay. Computed once at
     * enqueue time and stored as JSON so polling stays cheap.
     *
     * @param  array{donor_name:string, amount:float|string, currency:string, message:?string}  $data
     * @return array<string, mixed>
     */
    public function buildPayload(Streamer $streamer, OverlaySetting $settings, array $data, bool $isTest): array
    {
        $settings->loadMissing(['image', 'sound']);

        $ttsVars = $this->tts->variables(
            $streamer->display_name,
            $data['donor_name'],
            $data['amount'],
            $data['currency'],
            $data['message'],
        );

        $image = $settings->image;
        $sound = $settings->sound;

        // เสียงแจ้งเตือน: ไฟล์ที่อัปโหลดมาก่อน ถ้าไม่มีใช้เสียง preset ในระบบ
        $soundUrl = null;
        if ($settings->sound_enabled) {
            if ($sound) {
                $soundUrl = $sound->url();
            } elseif ($settings->sound_preset) {
                $soundUrl = asset('sounds/'.$settings->sound_preset.'.wav');
            }
        }

        return [
            'donor_name' => $data['donor_name'],
            'amount' => (float) $data['amount'],
            'amount_formatted' => Money::format($data['amount']),
            'currency' => $data['currency'],
            'message' => (string) $data['message'],
            'is_test' => $isTest,
            'avatar_url' => $settings->show_avatar ? $streamer->avatarUrl() : null,
            'image' => $image ? [
                'url' => $image->url(),
                'type' => $image->type->value,
            ] : null,
            'sound' => $soundUrl ? [
                'url' => $soundUrl,
                'volume' => $settings->sound_volume / 100,
                'delay' => (int) $settings->sound_delay,
            ] : null,
            'tts' => $this->tts->buildPayload($settings, $ttsVars),
            'style' => [
                'theme' => $settings->theme,
                'alert_style' => $settings->alert_style,
                'font_family' => $settings->font_family,
                'font_size' => $settings->font_size,
                'font_weight' => $settings->font_weight,
                'font_color' => $settings->font_color,
                'background_color' => $settings->background_color,
                'border_radius' => $settings->border_radius,
                'shadow' => $settings->shadow,
                'position' => $settings->position,
                'width' => $settings->width,
                'height' => $settings->height,
            ],
            'animation' => [
                'name' => $settings->animation,
                'duration' => (int) $settings->animation_duration,
            ],
            'display_duration' => (int) $settings->display_duration,
        ];
    }

    /**
     * Fetch the next alert to play for a streamer, enforcing "one at a time".
     * If an alert is already playing, returns null until it is completed.
     */
    public function next(Streamer $streamer): ?NotificationQueue
    {
        return DB::transaction(function () use ($streamer) {
            $playing = NotificationQueue::where('streamer_id', $streamer->id)
                ->where('status', NotificationStatus::Playing->value)
                ->lockForUpdate()
                ->exists();

            if ($playing) {
                return null;
            }

            $next = NotificationQueue::where('streamer_id', $streamer->id)
                ->where('status', NotificationStatus::Pending->value)
                ->orderBy('id')           // chronological order
                ->lockForUpdate()
                ->first();

            if (! $next) {
                return null;
            }

            $next->update(['status' => NotificationStatus::Playing->value]);

            return $next;
        });
    }

    /**
     * Mark a notification as completed after the overlay finished playing it.
     */
    public function markCompleted(Streamer $streamer, int $notificationId): bool
    {
        $notification = NotificationQueue::where('streamer_id', $streamer->id)
            ->whereKey($notificationId)
            ->first();

        if (! $notification) {
            return false;
        }

        $notification->update([
            'status' => NotificationStatus::Completed->value,
            'played_at' => now(),
        ]);

        return true;
    }

    public function pendingCount(Streamer $streamer): int
    {
        return NotificationQueue::where('streamer_id', $streamer->id)
            ->whereIn('status', [NotificationStatus::Pending->value, NotificationStatus::Playing->value])
            ->count();
    }

    private function settingsFor(Streamer $streamer): OverlaySetting
    {
        return $streamer->overlaySetting()->firstOrCreate([]);
    }
}
