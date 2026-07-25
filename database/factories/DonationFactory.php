<?php

namespace Database\Factories;

use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'donor_name' => fake()->randomElement(['ນ້ອຍ', 'ຄຳ', 'ດາວ', 'ມາຍ', 'ຕົ້ນ', 'ພອນ', 'ແກ້ວ', 'ຈັນ']),
            'amount' => fake()->randomElement([10000, 20000, 50000, 100000, 200000, 500000]),
            'currency' => 'LAK',
            'message' => fake()->boolean(70)
                ? fake()->randomElement([
                    'ເປັນກຳລັງໃຈໃຫ້ ສູ້ໆ!',
                    'ມື້ນີ້ມ່ວນຫຼາຍ',
                    'ຂໍໃຫ້ຊ່ອງເຕີບໃຫຍ່ ❤️',
                    'ຕິດຕາມຕະຫຼອດເດີ',
                    'ຮັກເລີຍ ❤️❤️❤️',
                    'ມາຊ້າແຕ່ກໍມາ',
                    'ຫຼິ້ນເກັ່ງຫຼາຍ',
                ])
                : null,
            'is_anonymous' => fake()->boolean(15),
            'status' => DonationStatus::Completed->value,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function anonymous(): static
    {
        return $this->state(fn () => ['is_anonymous' => true, 'donor_name' => 'Anonymous']);
    }
}
