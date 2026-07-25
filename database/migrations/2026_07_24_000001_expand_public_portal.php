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

        $now = now();

        DB::table('content_blocks')
            ->where('page', 'welcome')
            ->where('type', 'hero')
            ->orderBy('sort_order')
            ->limit(1)
            ->update([
                'heading' => 'ຮັບໂດເນດເປັນກີບ ແຈ້ງເຕືອນສົດເຂົ້າໄລຟ໌',
                'subheading' => 'ຄົບທັງໜ້າໂດເນດ, Lao QR, Overlay ສຳລັບ OBS, Dashboard ແລະ ສຽງອ່ານພາສາລາວ ສ້າງມາເພື່ອຄຣີເອເຕີລາວ.',
                'link_label' => 'ສະໝັກເຂົ້າຮ່ວມ',
                'link_url' => '/register',
                'updated_at' => $now,
            ]);

        $pages = [
            'news' => [
                [
                    'type' => 'news',
                    'heading' => 'ເປີດຕົວ TIPLAO DONATE ສຳລັບຄຣີເອເຕີລາວ',
                    'subheading' => 'ປະກາດຈາກທີມງານ',
                    'body' => '<p>ລະບົບຮອງຮັບເງິນກີບລາວ, Lao QR, Overlay ສຳລັບ OBS ແລະ ສຽງອ່ານພາສາລາວ. ຜູ້ສະໝັກໃໝ່ສາມາດເລີ່ມຕັ້ງຄ່າໜ້າໂດເນດໄດ້ທັນທີ.</p>',
                ],
                [
                    'type' => 'news',
                    'heading' => 'ແນະນຳການສະໝັກເຂົ້າໃຊ້ງານ',
                    'subheading' => 'ຂັ້ນຕອນສຳລັບສະຕຣີມເມີ',
                    'body' => '<p>ກະລຸນາໃຊ້ຊື່ຊ່ອງ ແລະ ອີເມວທີ່ຕິດຕໍ່ໄດ້ຈິງ. ຫຼັງຈາກສະໝັກ ລະບົບຈະນຳທ່ານເຂົ້າແດຊບອດເພື່ອຕັ້ງຄ່າໄດ້ທັນທີ.</p>',
                ],
            ],
            'community' => [
                [
                    'type' => 'community',
                    'heading' => 'Facebook Community',
                    'subheading' => 'ຕິດຕາມຂ່າວ ແລະ ແລກປ່ຽນກັບຄຣີເອເຕີລາວ',
                    'body' => '<p>ແອັດມິນສາມາດເພີ່ມລິ້ງກຸ່ມ Facebook ໄດ້ຈາກໜ້າຈັດການເນື້ອຫາ.</p>',
                ],
                [
                    'type' => 'community',
                    'heading' => 'Discord Community',
                    'subheading' => 'ສອບຖາມການໃຊ້ງານ ແລະ ຮັບຂ່າວໄດ້ໄວ',
                    'body' => '<p>ເພີ່ມລິ້ງເຊີນ Discord ເພື່ອເປີດພື້ນທີ່ສົນທະນາຂອງຄອມມູນິຕີ.</p>',
                ],
                [
                    'type' => 'community',
                    'heading' => 'Telegram Channel',
                    'subheading' => 'ປະກາດສຳຄັນ ແລະ ສະຖານະລະບົບ',
                    'body' => '<p>ເພີ່ມລິ້ງ Telegram ສຳລັບແຈ້ງເຕືອນຂ່າວສານຈາກທີມງານ.</p>',
                ],
            ],
            'contact' => [
                [
                    'type' => 'contact',
                    'heading' => 'ຕິດຕໍ່ຝ່າຍບໍລິການ',
                    'subheading' => 'ສຳລັບຄຳຖາມກ່ຽວກັບບັນຊີ ແລະ ການໃຊ້ງານ',
                    'body' => '<p>ກະລຸນາແຈ້ງຊື່ບັນຊີ, ລາຍລະອຽດບັນຫາ ແລະ ຮູບໜ້າຈໍຖ້າມີ. ທີມງານຈະຕອບກັບຕາມລຳດັບ.</p>',
                ],
                [
                    'type' => 'contact',
                    'heading' => 'ລາຍງານບັນຫາການຊຳລະເງິນ',
                    'subheading' => 'ກຽມຫຼັກຖານການໂອນ ແລະ ເວລາເກີດບັນຫາ',
                    'body' => '<p>ຢ່າສົ່ງລະຫັດຜ່ານ ຫຼື ຂໍ້ມູນລັບໃນຂໍ້ຄວາມຕິດຕໍ່.</p>',
                ],
            ],
        ];

        foreach ($pages as $page => $blocks) {
            if (DB::table('content_blocks')->where('page', $page)->exists()) {
                continue;
            }

            foreach ($blocks as $index => $block) {
                DB::table('content_blocks')->insert(array_merge([
                    'page' => $page,
                    'heading' => null,
                    'subheading' => null,
                    'body' => null,
                    'image_url' => null,
                    'link_label' => null,
                    'link_url' => null,
                    'sort_order' => $index + 1,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $block));
            }
        }
    }

    public function down(): void
    {
        // Keep administrator-authored public content on rollback.
    }
};
