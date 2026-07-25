<?php

namespace Database\Factories;

use App\Models\OverlaySetting;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OverlaySetting>
 */
class OverlaySettingFactory extends Factory
{
    protected $model = OverlaySetting::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
        ];
    }

    public function withTts(?string $template = null): static
    {
        return $this->state(fn () => [
            'tts_enabled' => true,
            'tts_template' => $template ?? 'ຂອບໃຈ {donor_name} ທີ່ໂດເນດ {amount} ກີບ {message}',
        ]);
    }
}
