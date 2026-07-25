<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Donation;
use App\Models\DonationPage;
use App\Models\OverlaySetting;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'ຜູ້ດູແລ TIPLAO',
            'email' => 'admin@newlab.test',
            'password' => Hash::make('password'),
        ]);

        $demoUser = User::factory()->create([
            'name' => 'ສະຕຣີມເມີທົດລອງ',
            'email' => 'streamer@newlab.test',
            'password' => Hash::make('password'),
            'role' => Role::Streamer->value,
        ]);

        $demo = Streamer::factory()->for($demoUser)->create([
            'username' => 'demo',
            'display_name' => 'TIPLAO LIVE',
            'description' => 'ຂອບໃຈທຸກການສະໜັບສະໜູນ!',
        ]);
        DonationPage::factory()->for($demo)->create();
        OverlaySetting::factory()->for($demo)->withTts()->create();
        Donation::factory()->count(25)->for($demo)->create();

        Streamer::factory()->count(4)->create()->each(function (Streamer $streamer) {
            DonationPage::factory()->for($streamer)->create();
            OverlaySetting::factory()->for($streamer)->create();
            Donation::factory()->count(fake()->numberBetween(5, 30))->for($streamer)->create();
        });
    }
}
