<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The currency used as default for new streamers and donations.
    |
    */

    'default_currency' => env('NEWLAB_DEFAULT_CURRENCY', 'LAK'),

    'currencies' => ['LAK'],

    /*
    |--------------------------------------------------------------------------
    | Overlay Polling
    |--------------------------------------------------------------------------
    |
    | How often (in seconds) the OBS overlay polls the API for new donations.
    | The platform uses lightweight polling instead of WebSockets so that it
    | remains compatible with shared hosting (DirectAdmin).
    |
    */

    'overlay' => [
        'poll_seconds' => (int) env('NEWLAB_OVERLAY_POLL_SECONDS', 3),

        /*
        | ชุดธีมสำเร็จรูปของ Overlay (เลือกได้ในหน้าตั้งค่า + ใช้ทั้ง renderer และ live preview)
        | คีย์ 'custom' = ใช้สี/พื้นหลังที่ผู้ใช้ตั้งเองในส่วนปรับแต่งขั้นสูง
        */
        'themes' => [
            'neon-purple' => [
                'label' => 'ມ່ວງນີອອນ',
                'background' => 'linear-gradient(135deg, rgba(124,58,237,.28), rgba(15,15,26,.92))',
                'border' => '1px solid rgba(167,139,250,.75)',
                'glow' => '0 0 42px rgba(124,58,237,.55)',
                'name_color' => '#c4b5fd',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.88)',
            ],
            'cyber-blue' => [
                'label' => 'ຟ້າໄຊເບີ',
                'background' => 'linear-gradient(135deg, rgba(6,182,212,.28), rgba(8,15,30,.92))',
                'border' => '1px solid rgba(103,232,249,.75)',
                'glow' => '0 0 42px rgba(6,182,212,.55)',
                'name_color' => '#67e8f9',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.88)',
            ],
            'pink-glow' => [
                'label' => 'ບົວເຮືອງແສງ',
                'background' => 'linear-gradient(135deg, rgba(236,72,153,.30), rgba(26,10,22,.92))',
                'border' => '1px solid rgba(249,168,212,.8)',
                'glow' => '0 0 42px rgba(236,72,153,.55)',
                'name_color' => '#f9a8d4',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.88)',
            ],
            'gold-lux' => [
                'label' => 'ຄຳຫຼູຫຼາ',
                'background' => 'linear-gradient(135deg, rgba(234,179,8,.28), rgba(28,20,5,.94))',
                'border' => '1px solid rgba(253,224,71,.8)',
                'glow' => '0 0 42px rgba(234,179,8,.5)',
                'name_color' => '#fde047',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.88)',
            ],
            'emerald' => [
                'label' => 'ຂຽວມໍລະກົດ',
                'background' => 'linear-gradient(135deg, rgba(16,185,129,.28), rgba(6,22,18,.92))',
                'border' => '1px solid rgba(110,231,183,.78)',
                'glow' => '0 0 42px rgba(16,185,129,.5)',
                'name_color' => '#6ee7b7',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.88)',
            ],
            'minimal-dark' => [
                'label' => 'ມິນິມອນສີດຳ',
                'background' => 'rgba(17,17,27,0.92)',
                'border' => '1px solid rgba(255,255,255,.12)',
                'glow' => '0 18px 50px rgba(0,0,0,.5)',
                'name_color' => '#e2e8f0',
                'amount_color' => '#ffffff',
                'message_color' => 'rgba(255,255,255,.8)',
            ],
            'custom' => [
                'label' => 'ກຳນົດເອງ (ໃຊ້ສີທີ່ຕັ້ງດ້ານລຸ່ມ)',
            ],
        ],

        /*
        | รูปแบบ "โครงการ์ด" แจ้งเตือน (layout) — ต่างจากธีมตรงที่กำหนดโครงหน้าตา
        | ไม่ใช่แค่สี. renderer + live preview อ่านคีย์เหล่านี้เพื่อจัดวาง
        |   classic     = การ์ดกลางแบบเดิม (avatar บน, ข้อความกลาง)
        |   banner      = แถบยาวแนวนอน avatar/ไอคอนซ้าย ข้อความขวา
        |   pill        = แคปซูลบรรทัดเดียว กะทัดรัด
        |   hero        = การ์ดใหญ่ ยอดเงินตัวเบิ้ม โดดเด่น
        |   side-accent = มินิมอล แถบสีไฮไลต์ด้านซ้าย
        */
        'alert_styles' => [
            'classic'    => ['label' => 'ຄລາສສິກ (ກາງກາດ)',       'icon' => 'layout-classic'],
            'banner'     => ['label' => 'ແບນເນີ (ແນວນອນ)',         'icon' => 'layout-banner'],
            'pill'       => ['label' => 'ແຄັບຊູນຂະໜາດນ້ອຍ',         'icon' => 'layout-pill'],
            'hero'       => ['label' => 'ຮີໂຣ (ຍອດເງິນຂະໜາດໃຫຍ່)', 'icon' => 'layout-hero'],
            'side-accent'=> ['label' => 'ຂອບສີດ້ານຂ້າງ (ມິນິມອນ)', 'icon' => 'layout-side'],
        ],

        /*
        | แอนิเมชันเข้า/ออกของการ์ดแจ้งเตือน — keyframe จริงอยู่ใน renderer/preview
        | (คีย์ต้องตรงกับ map ใน overlay/show.blade.php และ overlay.blade.php)
        */
        'animations' => [
            'fade'        => 'ຄ່ອຍໆປາກົດ (Fade)',
            'slide'       => 'ເລື່ອນລົງ (Slide)',
            'slide-left'  => 'ເລື່ອນຈາກຊ້າຍ (Slide Left)',
            'slide-right' => 'ເລື່ອນຈາກຂວາ (Slide Right)',
            'bounce'      => 'ເດັ້ງ (Bounce)',
            'zoom'        => 'ຊູມ (Zoom)',
            'pop'         => 'ປັອບຍືດ (Pop / Rubber)',
            'flip'        => 'ພິກ 3 ມິຕິ (Flip)',
            'glitch'      => 'ກລິດນີອອນ (Glitch)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Donation Constraints
    |--------------------------------------------------------------------------
    */

    'donation' => [
        'min_amount' => 1_000,
        'max_amount' => 100_000_000,
        'max_message_length' => 255,
        'max_name_length' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Text To Speech
    |--------------------------------------------------------------------------
    |
    | provider: "browser" uses the client Web Speech API inside the OBS
    | browser source (no server cost, shared-hosting friendly). Server-side
    | providers can be plugged in later; generated audio is cached in the
    | tts_cache table and expired entries are pruned automatically.
    |
    */

    'tts' => [
        'provider' => env('NEWLAB_TTS_PROVIDER', 'browser'),
        'cache_days' => (int) env('NEWLAB_TTS_CACHE_DAYS', 7),
        'default_language' => env('NEWLAB_TTS_LANGUAGE', 'lo-LA'),
        'default_template' => 'ຂອບໃຈ {donor_name} ທີ່ໂດເນດ {amount} ກີບ {message}',

        /*
        | เสียง neural ภาษาลาวของ Microsoft Edge (ฟรี ไม่ต้องใช้ API key)
        | ระบบจะพยายามใช้ตัวนี้ก่อน ถ้าโฮสต์เชื่อมต่อ Edge ไม่ได้ (บาง IP ถูกบล็อก)
        | จะ fallback ไปเสียง Google Translate อัตโนมัติ. คีย์ = ชื่อ voice ที่ Microsoft ใช้.
        */
        'edge_voices' => [
            'lo-LA-KeomanyNeural' => 'ແກ້ວມະນີ (ຍິງ)',
            'lo-LA-ChanthavongNeural' => 'ຈັນທະວົງ (ຊາຍ)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | เสียงแจ้งเตือนสำเร็จในระบบ (Built-in notification sounds)
    |--------------------------------------------------------------------------
    |
    | ไฟล์อยู่ที่ public/sounds/<key>.wav — สตรีมเมอร์เลือกใช้ได้โดยไม่ต้องอัปโหลด
    | (ยังอัปโหลดเสียงเองได้ในเมนูคลังสื่อ)
    |
    */

    'sound_presets' => [
        'ding'  => 'ກະດິ່ງ (Ding)',
        'bell'  => 'ລະຄັງ (Bell)',
        'coin'  => 'ຫຼຽນ (Coin)',
        'chime' => 'ລະຄັງລາວ (Chime)',
        'cash'  => 'ເຄື່ອງຄິດເງິນ (Cash)',
        'blip'  => 'ບລິບ (Blip)',
        'soft'  => 'ອ່ອນນຸ້ມ (Soft)',
        'pop'   => 'ປັອບ (Pop)',
    ],

    /*
    |--------------------------------------------------------------------------
    | TrueMoney ซองของขวัญ (Angpao) — ช่องทางรับโดเนท
    |--------------------------------------------------------------------------
    |
    | ผู้สนับสนุนสร้าง "ซองของขวัญ" ในแอป TrueMoney แล้ววางลิงก์ซองที่หน้าโดเนท
    | ระบบจะ redeem ซองเข้าเบอร์วอลเล็ตของสตรีมเมอร์อัตโนมัติ แล้วบันทึกยอดจริง
    |
    */

    'truewallet' => [
        'enabled' => (bool) env('NEWLAB_TRUEWALLET_ENABLED', false),
        'redeem_url' => 'https://gift.truemoney.com/campaign/vouchers/%s/redeem',
        'min_amount' => (float) env('NEWLAB_TRUEWALLET_MIN', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Upload Rules
    |--------------------------------------------------------------------------
    */

    'media' => [
        'image' => [
            'mimes' => ['png', 'jpg', 'jpeg', 'webp'],
            'max_kb' => 4096,
        ],
        'animation' => [
            'mimes' => ['gif', 'webm'],
            'max_kb' => 8192,
        ],
        'audio' => [
            'mimes' => ['mp3', 'wav', 'ogg'],
            'max_kb' => 4096,
        ],
        'avatar' => [
            'mimes' => ['png', 'jpg', 'jpeg', 'webp'],
            'max_kb' => 2048,
        ],
        'qr' => [
            'mimes' => ['png', 'jpg', 'jpeg', 'webp'],
            'max_kb' => 2048,
        ],
    ],
];
