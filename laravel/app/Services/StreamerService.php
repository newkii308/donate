<?php

namespace App\Services;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StreamerService
{
    private const DISK = 'public';

    /**
     * Create a streamer profile (with default donation page + overlay
     * settings) for a freshly registered user.
     *
     * @param  array{username?:string, display_name?:string}  $data
     */
    public function createForUser(User $user, array $data = []): Streamer
    {
        return DB::transaction(function () use ($user, $data) {
            $displayName = $data['display_name'] ?? $user->name;
            $username = $this->uniqueUsername($data['username'] ?? $displayName);

            $streamer = $user->streamer()->create([
                'username' => $username,
                'display_name' => $displayName,
                'currency' => config('newlab.default_currency', 'LAK'),
                'is_active' => true,
            ]);

            $streamer->donationPage()->create([
                'title' => "ສະໜັບສະໜູນ {$displayName}",
                'description' => 'ຂອບໃຈທຸກການສະໜັບສະໜູນ! 💜',
                'min_amount' => config('newlab.donation.min_amount', 1000),
                'quick_amounts' => [10000, 20000, 50000, 100000, 200000],
            ]);

            $streamer->overlaySetting()->create([]);

            return $streamer;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(Streamer $streamer, array $data): Streamer
    {
        if (isset($data['username'])) {
            $data['username'] = Str::slug($data['username']);
        }

        $streamer->update($data);

        return $streamer;
    }

    public function updateAvatar(Streamer $streamer, UploadedFile $file): Streamer
    {
        $this->deleteIfExists($streamer->avatar_path);
        $path = $file->store("avatars/{$streamer->id}", self::DISK);
        $streamer->update(['avatar_path' => $path]);

        return $streamer;
    }

    public function updateQrCode(Streamer $streamer, UploadedFile $file): Streamer
    {
        $this->deleteIfExists($streamer->qr_code_path);
        $path = $file->store("qr-codes/{$streamer->id}", self::DISK);
        $streamer->update(['qr_code_path' => $path]);

        return $streamer;
    }

    public function regenerateOverlayKey(Streamer $streamer): Streamer
    {
        $streamer->update(['overlay_key' => Streamer::generateOverlayKey()]);

        return $streamer;
    }

    public function uniqueUsername(string $base): string
    {
        $slug = Str::slug($base) ?: 'streamer';
        $username = $slug;
        $i = 1;

        while (Streamer::withTrashed()->where('username', $username)->exists()) {
            $username = $slug.'-'.(++$i);
        }

        return $username;
    }

    private function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
