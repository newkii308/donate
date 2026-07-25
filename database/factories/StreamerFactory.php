<?php

namespace Database\Factories;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Streamer>
 */
class StreamerFactory extends Factory
{
    protected $model = Streamer::class;

    public function definition(): array
    {
        $name = fake()->userName();

        return [
            'user_id' => User::factory(),
            'username' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'overlay_key' => Str::lower(Str::random(40)),
            'display_name' => fake()->name(),
            'description' => 'ຂອບໃຈທຸກການສະໜັບສະໜູນ! 💜',
            'truewallet_phone' => null,
            'payment_method' => 'ໂອນຜ່ານ Lao QR',
            'account_name' => fake()->name(),
            'account_number' => fake()->numerify('###-#-#####-#'),
            'bank_name' => fake()->randomElement(['BCEL', 'LDB', 'JDB']),
            'currency' => 'LAK',
            'is_active' => true,
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
