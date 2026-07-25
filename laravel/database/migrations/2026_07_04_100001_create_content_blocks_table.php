<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('page', 24)->index();   // welcome | about | terms | privacy | faq
            $table->string('type', 24);            // hero | feature | cta | richtext | faq | image
            $table->string('heading')->nullable();
            $table->string('subheading')->nullable();
            $table->longText('body')->nullable();  // rich HTML (about/terms/privacy/faq answer)
            $table->string('image_url')->nullable();
            $table->string('link_label')->nullable();
            $table->string('link_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        $this->seedDefaults();
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }

    /**
     * เนื้อหาเริ่มต้น เพื่อให้ทุกหน้ามีของแสดงทันที (ลูกค้าค่อยแก้/เพิ่มทีหลัง)
     */
    private function seedDefaults(): void
    {
        $now = now();
        $rows = [];
        $add = function (array $r) use (&$rows, $now) {
            $rows[] = array_merge([
                'heading' => null, 'subheading' => null, 'body' => null,
                'image_url' => null, 'link_label' => null, 'link_url' => null,
                'is_visible' => true, 'created_at' => $now, 'updated_at' => $now,
            ], $r);
        };

        // ---- Welcome / Landing ----
        $add([
            'page' => 'welcome', 'type' => 'hero', 'sort_order' => 1,
            'heading' => 'ຮັບໂດເນດ ແລະ ແຈ້ງເຕືອນໃນໄລຟ໌ ພາຍໃນ 10 ວິນາທີ',
            'subheading' => 'ແພລດຟອມຮັບໂດເນດທີ່ເບົາ ແລະ ສວຍງາມສຳລັບສະຕຣີມເມີລາວ — ໜ້າໂດເນດ, ແຈ້ງເຕືອນ OBS, ສຽງອ່ານພາສາລາວ (TTS) ແລະ ຄິວແຈ້ງເຕືອນແບບທັນທີ',
            'link_label' => 'ສ້າງໜ້າໂດເນດຂອງທ່ານ', 'link_url' => '/register',
        ]);
        $add(['page' => 'welcome', 'type' => 'feature', 'sort_order' => 2, 'heading' => 'ໜ້າໂດເນດ', 'body' => 'ແຊຣ໌ລິ້ງ /donate/ຊື່ຂອງທ່ານ ພ້ອມ Lao QR, ຄັດລອກເລກບັນຊີ ແລະ ໂດເນດແບບບໍ່ລະບຸຊື່ — ຮອງຮັບມືຖືເຕັມຮູບແບບ']);
        $add(['page' => 'welcome', 'type' => 'feature', 'sort_order' => 3, 'heading' => 'Overlay ສຳລັບ OBS', 'body' => 'ພື້ນຫຼັງໂປ່ງໃສ ພ້ອມອະນິເມຊັນ, ສຽງ, GIF ແລະ WEBM ໂດຍຄິວແຈ້ງເຕືອນບໍ່ຊ້ອນກັນ']);
        $add(['page' => 'welcome', 'type' => 'feature', 'sort_order' => 4, 'heading' => 'ສຽງອ່ານຂໍ້ຄວາມ (TTS)', 'body' => 'ປັບແຕ່ງຂໍ້ຄວາມສຽງອ່ານໄດ້ເຕັມທີ່ ສຽງພາສາລາວຊັດເຈນ']);

        // ---- About ----
        $add([
            'page' => 'about', 'type' => 'richtext', 'sort_order' => 1,
            'heading' => 'ກ່ຽວກັບພວກເຮົາ',
            'body' => '<p>ພວກເຮົາແມ່ນແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ ຊ່ວຍໃຫ້ຮັບການສະໜັບສະໜູນຈາກຜູ້ຊົມໄດ້ງ່າຍ ພ້ອມລະບົບແຈ້ງເຕືອນສວຍງາມໃນໄລຟ໌</p>',
        ]);

        // ---- Terms ----
        $add([
            'page' => 'terms', 'type' => 'richtext', 'sort_order' => 1,
            'heading' => 'ເງື່ອນໄຂການນຳໃຊ້',
            'body' => '<p>ກະລຸນາແກ້ໄຂເນື້ອຫາເງື່ອນໄຂການນຳໃຊ້ຢູ່ໜ້າແອັດມິນ → ຈັດການເນື້ອຫາ → ເງື່ອນໄຂ</p>',
        ]);

        // ---- Privacy ----
        $add([
            'page' => 'privacy', 'type' => 'richtext', 'sort_order' => 1,
            'heading' => 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ',
            'body' => '<p>ກະລຸນາແກ້ໄຂເນື້ອຫານະໂຍບາຍຄວາມເປັນສ່ວນຕົວຢູ່ໜ້າແອັດມິນ → ຈັດການເນື້ອຫາ → ນະໂຍບາຍ</p>',
        ]);

        // ---- FAQ ----
        $add(['page' => 'faq', 'type' => 'faq', 'sort_order' => 1, 'heading' => 'ຜູ້ສະໜັບສະໜູນຕ້ອງສະໝັກສະມາຊິກບໍ?', 'body' => 'ບໍ່ຕ້ອງ ສາມາດໂດເນດໄດ້ເລີຍຜ່ານໜ້າໂດເນດຂອງສະຕຣີມເມີ']);
        $add(['page' => 'faq', 'type' => 'faq', 'sort_order' => 2, 'heading' => 'ຮອງຮັບການຊຳລະເງິນແບບໃດ?', 'body' => 'ຮອງຮັບການໂອນເງິນກີບຜ່ານ Lao QR ແລະ ບັນຊີທະນາຄານລາວ']);
        $add(['page' => 'faq', 'type' => 'faq', 'sort_order' => 3, 'heading' => 'ໃຊ້ກັບ OBS ແນວໃດ?', 'body' => 'ຄັດລອກລິ້ງ Overlay ຈາກໜ້າຕັ້ງຄ່າ ແລ້ວວາງໃນ OBS → Sources → Browser']);

        DB::table('content_blocks')->insert($rows);
    }
};
