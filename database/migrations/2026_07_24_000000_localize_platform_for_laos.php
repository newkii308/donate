<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('streamers')) {
            DB::table('streamers')->update([
                'currency' => 'LAK',
                'truewallet_phone' => null,
            ]);
        }

        if (Schema::hasTable('donations')) {
            DB::table('donations')->update(['currency' => 'LAK']);
        }

        if (Schema::hasTable('donation_pages')) {
            DB::table('donation_pages')->update([
                'min_amount' => 1000,
                'quick_amounts' => json_encode([10000, 20000, 50000, 100000, 200000]),
            ]);
        }

        if (Schema::hasTable('overlay_settings')) {
            DB::table('overlay_settings')->update([
                'font_family' => "'Noto Sans Lao', sans-serif",
                'tts_language' => 'lo-LA',
                'tts_voice' => 'edge:lo-LA-KeomanyNeural',
                'tts_template' => 'ຂອບໃຈ {donor_name} ທີ່ໂດເນດ {amount} ກີບ {message}',
            ]);
        }

        if (Schema::hasTable('donation_goals')) {
            DB::table('donation_goals')->update([
                'title' => 'ຊື້ໄມໂຄຣໂຟນໃໝ່',
                'bottom_text' => 'ຂອບໃຈທຸກການສະໜັບສະໜູນ! ❤️',
                'celebration_message' => 'ບັນລຸເປົ້າໝາຍແລ້ວ! 🎉',
                'font_family' => "'Noto Sans Lao', sans-serif",
            ]);
        }

        if (Schema::hasTable('settings')) {
            $settings = [
                'platform_name' => 'TIPLAO DONATE',
                'tagline' => 'ແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ',
                'meta_description' => 'ຮັບໂດເນດ ແລະ ແຈ້ງເຕືອນໃນໄລຟ໌ ພ້ອມ Overlay ສຳລັບ OBS ແລະ ສຽງອ່ານພາສາລາວ',
                'default_currency' => 'LAK',
                'maintenance_message' => 'ລະບົບກຳລັງປັບປຸງຊົ່ວຄາວ ກະລຸນາກັບມາໃໝ່ອີກຄັ້ງ 🙏',
            ];

            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    ['value' => json_encode($value, JSON_UNESCAPED_UNICODE), 'updated_at' => now(), 'created_at' => now()],
                );
            }
        }

        if (Schema::hasTable('content_blocks')) {
            $blocks = [
                ['welcome', 'hero', 1, 'ຮັບໂດເນດ ແລະ ແຈ້ງເຕືອນໃນໄລຟ໌ ພາຍໃນ 10 ວິນາທີ', 'ແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ ພ້ອມ OBS Overlay ແລະ ສຽງອ່ານພາສາລາວ', null],
                ['welcome', 'feature', 2, 'ໜ້າໂດເນດ', null, 'ຮອງຮັບ Lao QR, ບັນຊີທະນາຄານລາວ ແລະ ການໂດເນດແບບບໍ່ລະບຸຊື່'],
                ['welcome', 'feature', 3, 'Overlay ສຳລັບ OBS', null, 'ພື້ນຫຼັງໂປ່ງໃສ ພ້ອມອະນິເມຊັນ, ສຽງ, GIF ແລະ WEBM'],
                ['welcome', 'feature', 4, 'ສຽງອ່ານຂໍ້ຄວາມ (TTS)', null, 'ປັບແຕ່ງສຽງອ່ານພາສາລາວໄດ້ຕາມຕ້ອງການ'],
                ['about', 'richtext', 1, 'ກ່ຽວກັບພວກເຮົາ', null, '<p>ແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ ຊ່ວຍໃຫ້ຮັບການສະໜັບສະໜູນຈາກຜູ້ຊົມໄດ້ງ່າຍ</p>'],
                ['terms', 'richtext', 1, 'ເງື່ອນໄຂການນຳໃຊ້', null, '<p>ກະລຸນາຕິດຕໍ່ຜູ້ດູແລລະບົບເພື່ອສອບຖາມເງື່ອນໄຂການນຳໃຊ້</p>'],
                ['privacy', 'richtext', 1, 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ', null, '<p>ພວກເຮົາເຄົາລົບ ແລະ ປົກປ້ອງຂໍ້ມູນສ່ວນຕົວຂອງທ່ານ</p>'],
                ['faq', 'faq', 1, 'ຜູ້ສະໜັບສະໜູນຕ້ອງສະໝັກສະມາຊິກບໍ?', null, 'ບໍ່ຕ້ອງ ສາມາດໂດເນດໄດ້ເລີຍຜ່ານໜ້າໂດເນດ'],
                ['faq', 'faq', 2, 'ຮອງຮັບການຊຳລະເງິນແບບໃດ?', null, 'ຮອງຮັບການໂອນເງິນກີບຜ່ານ Lao QR ແລະ ບັນຊີທະນາຄານລາວ'],
                ['faq', 'faq', 3, 'ໃຊ້ກັບ OBS ແນວໃດ?', null, 'ຄັດລອກລິ້ງ Overlay ຈາກໜ້າຕັ້ງຄ່າໄປວາງໃນ OBS → Sources → Browser'],
            ];

            foreach ($blocks as [$page, $type, $sort, $heading, $subheading, $body]) {
                DB::table('content_blocks')
                    ->where('page', $page)
                    ->where('type', $type)
                    ->where('sort_order', $sort)
                    ->update(compact('heading', 'subheading', 'body'));
            }
        }
    }

    public function down(): void
    {
        // The localization migration intentionally preserves Lao data.
    }
};
