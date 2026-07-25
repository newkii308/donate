<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_blocks')) {
            return;
        }

        DB::table('content_blocks')
            ->where('page', 'welcome')
            ->where('type', 'hero')
            ->where('heading', 'ສູນກາງໂດເນດສຳລັບຄຣີເອເຕີລາວ')
            ->update([
                'heading' => 'ຮັບໂດເນດເປັນກີບ ແຈ້ງເຕືອນສົດເຂົ້າໄລຟ໌',
                'subheading' => 'ຄົບທັງໜ້າໂດເນດ, Lao QR, Overlay ສຳລັບ OBS, Dashboard ແລະ ສຽງອ່ານພາສາລາວ ສ້າງມາເພື່ອຄຣີເອເຕີລາວ.',
                'link_label' => 'ສະໝັກໃຊ້ງານຟຣີ',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Keep administrator-edited marketing copy.
    }
};
