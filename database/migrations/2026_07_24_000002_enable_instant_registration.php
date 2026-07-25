<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('users')) {
            $pendingUserIds = DB::table('users')
                ->where('role', 'streamer')
                ->whereNull('approved_at')
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($pendingUserIds->isNotEmpty()) {
                DB::table('users')
                    ->whereIn('id', $pendingUserIds)
                    ->update([
                        'is_active' => true,
                        'approved_at' => $now,
                        'updated_at' => $now,
                    ]);

                if (Schema::hasTable('streamers')) {
                    DB::table('streamers')
                        ->whereIn('user_id', $pendingUserIds)
                        ->update([
                            'is_active' => true,
                            'updated_at' => $now,
                        ]);
                }
            }
        }

        if (! Schema::hasTable('content_blocks')) {
            return;
        }

        DB::table('content_blocks')
            ->where('page', 'welcome')
            ->where('type', 'hero')
            ->where('subheading', 'ຕິດຕາມຂ່າວສານ, ເຂົ້າຮ່ວມຄອມມູນິຕີ ແລະ ສະໝັກເພື່ອໃຊ້ລະບົບໂດເນດພາສາລາວ. ທຸກບັນຊີຈະຖືກກວດສອບໂດຍແອັດມິນກ່ອນເປີດໃຊ້ງານ.')
            ->update([
                'subheading' => 'ຕິດຕາມຂ່າວສານ, ເຂົ້າຮ່ວມຄອມມູນິຕີ ແລະ ສະໝັກໃຊ້ລະບົບໂດເນດພາສາລາວໄດ້ທັນທີ.',
                'updated_at' => $now,
            ]);

        $replacements = [
            '<p>ລະບົບຮອງຮັບເງິນກີບລາວ, Lao QR, Overlay ສຳລັບ OBS ແລະ ສຽງອ່ານພາສາລາວ. ຜູ້ສະໝັກໃໝ່ຈະຕ້ອງຜ່ານການກວດສອບຈາກແອັດມິນກ່ອນ.</p>'
                => '<p>ລະບົບຮອງຮັບເງິນກີບລາວ, Lao QR, Overlay ສຳລັບ OBS ແລະ ສຽງອ່ານພາສາລາວ. ຜູ້ສະໝັກໃໝ່ສາມາດເລີ່ມຕັ້ງຄ່າໜ້າໂດເນດໄດ້ທັນທີ.</p>',
            '<p>ກະລຸນາໃຊ້ຊື່ຊ່ອງ ແລະ ອີເມວທີ່ຕິດຕໍ່ໄດ້ຈິງ. ຫຼັງຈາກສົ່ງຄຳຮ້ອງ ກະລຸນາລໍຖ້າແອັດມິນກວດສອບ ແລະ ເປີດບັນຊີ.</p>'
                => '<p>ກະລຸນາໃຊ້ຊື່ຊ່ອງ ແລະ ອີເມວທີ່ຕິດຕໍ່ໄດ້ຈິງ. ຫຼັງຈາກສະໝັກ ລະບົບຈະນຳທ່ານເຂົ້າແດຊບອດເພື່ອຕັ້ງຄ່າໄດ້ທັນທີ.</p>',
        ];

        foreach ($replacements as $old => $new) {
            DB::table('content_blocks')
                ->where('page', 'news')
                ->where('body', $old)
                ->update([
                    'body' => $new,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Do not deactivate accounts that have already started using the platform.
    }
};
