<?php

namespace Database\Factories;

use App\Models\DonationPage;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DonationPage>
 */
class DonationPageFactory extends Factory
{
    protected $model = DonationPage::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'title' => 'ສະໜັບສະໜູນ '.fake()->firstName(),
            'description' => 'ຂອບໃຈທຸກການສະໜັບສະໜູນ! 💜',
            'min_amount' => 1000,
            'max_amount' => null,
            'quick_amounts' => [10000, 20000, 50000, 100000, 200000],
            'allow_anonymous' => true,
            'show_recent_donations' => true,
            'thank_you_message' => 'ຂອບໃຈສຳລັບການສະໜັບສະໜູນ! 💜',
            'theme' => 'dark',
            'accent_color' => fake()->randomElement(['#7c3aed', '#2563eb', '#db2777', '#06b6d4', '#059669']),
        ];
    }
}
