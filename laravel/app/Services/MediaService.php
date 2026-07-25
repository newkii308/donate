<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\OverlaySetting;
use App\Models\Streamer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    private const DISK = 'public';

    /**
     * Store an uploaded media file for a streamer and record it.
     */
    public function store(Streamer $streamer, UploadedFile $file, MediaType $type): Media
    {
        $dir = "media/{$streamer->id}/{$type->value}";
        $path = $file->store($dir, self::DISK);

        return $streamer->media()->create([
            'type' => $type->value,
            'disk' => self::DISK,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
            'size' => $file->getSize() ?? 0,
            'meta' => $this->extractMeta($file, $type),
        ]);
    }

    /**
     * Delete a media file and clear any overlay references to it.
     */
    public function delete(Media $media): void
    {
        OverlaySetting::where('image_media_id', $media->id)->update(['image_media_id' => null]);
        OverlaySetting::where('sound_media_id', $media->id)->update(['sound_media_id' => null]);

        if ($media->path && Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractMeta(UploadedFile $file, MediaType $type): ?array
    {
        if ($type === MediaType::Image) {
            $info = @getimagesize($file->getRealPath());
            if ($info !== false) {
                return ['width' => $info[0], 'height' => $info[1]];
            }
        }

        return null;
    }

    /**
     * Map a config media-group name ("image"/"animation"/"audio") to the enum.
     */
    public function typeFromGroup(string $group): MediaType
    {
        return MediaType::from($group);
    }
}
