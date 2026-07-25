<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'type' => MediaType::Image->value,
            'disk' => 'public',
            'path' => 'media/sample/'.fake()->uuid().'.png',
            'original_name' => fake()->word().'.png',
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(1000, 500000),
            'meta' => ['width' => 512, 'height' => 512],
        ];
    }

    public function audio(): static
    {
        return $this->state(fn () => [
            'type' => MediaType::Audio->value,
            'path' => 'media/sample/'.fake()->uuid().'.mp3',
            'original_name' => fake()->word().'.mp3',
            'mime_type' => 'audio/mpeg',
        ]);
    }

    public function animation(): static
    {
        return $this->state(fn () => [
            'type' => MediaType::Animation->value,
            'path' => 'media/sample/'.fake()->uuid().'.gif',
            'original_name' => fake()->word().'.gif',
            'mime_type' => 'image/gif',
        ]);
    }
}
