<?php

/*
|--------------------------------------------------------------------------
| ข้อความ Validation ภาษาไทย
|--------------------------------------------------------------------------
| ครอบคลุมกฎที่ใช้บ่อยในระบบ (สามารถเพิ่มได้ตามต้องการ)
*/

return [
    'accepted' => 'ต้องยอมรับ :attribute',
    'active_url' => ':attribute ไม่ใช่ URL ที่ถูกต้อง',
    'after' => ':attribute ต้องเป็นวันที่หลังจาก :date',
    'after_or_equal' => ':attribute ต้องเป็นวันที่หลังจากหรือเท่ากับ :date',
    'alpha' => ':attribute ต้องเป็นตัวอักษรเท่านั้น',
    'alpha_dash' => ':attribute ต้องเป็นตัวอักษร ตัวเลข ขีดกลาง หรือขีดล่างเท่านั้น',
    'alpha_num' => ':attribute ต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
    'array' => ':attribute ต้องเป็นรายการ (array)',
    'before' => ':attribute ต้องเป็นวันที่ก่อน :date',
    'before_or_equal' => ':attribute ต้องเป็นวันที่ก่อนหรือเท่ากับ :date',
    'between' => [
        'numeric' => ':attribute ต้องมีค่าระหว่าง :min ถึง :max',
        'file' => ':attribute ต้องมีขนาดระหว่าง :min ถึง :max กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวระหว่าง :min ถึง :max ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวน :min ถึง :max รายการ',
    ],
    'boolean' => ':attribute ต้องเป็น true หรือ false',
    'confirmed' => ':attribute ยืนยันไม่ตรงกัน',
    'current_password' => 'รหัสผ่านไม่ถูกต้อง',
    'date' => ':attribute ไม่ใช่วันที่ที่ถูกต้อง',
    'date_equals' => ':attribute ต้องเป็นวันที่เท่ากับ :date',
    'different' => ':attribute และ :other ต้องไม่เหมือนกัน',
    'digits' => ':attribute ต้องเป็นตัวเลข :digits หลัก',
    'digits_between' => ':attribute ต้องเป็นตัวเลข :min ถึง :max หลัก',
    'email' => ':attribute ต้องเป็นอีเมลที่ถูกต้อง',
    'ends_with' => ':attribute ต้องลงท้ายด้วย: :values',
    'exists' => ':attribute ที่เลือกไม่ถูกต้อง',
    'file' => ':attribute ต้องเป็นไฟล์',
    'filled' => 'ต้องระบุ :attribute',
    'gt' => [
        'numeric' => ':attribute ต้องมากกว่า :value',
        'file' => ':attribute ต้องมีขนาดมากกว่า :value กิโลไบต์',
        'string' => ':attribute ต้องยาวมากกว่า :value ตัวอักษร',
    ],
    'gte' => [
        'numeric' => ':attribute ต้องมากกว่าหรือเท่ากับ :value',
        'file' => ':attribute ต้องมีขนาดมากกว่าหรือเท่ากับ :value กิโลไบต์',
    ],
    'image' => ':attribute ต้องเป็นรูปภาพ',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'integer' => ':attribute ต้องเป็นจำนวนเต็ม',
    'ip' => ':attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง',
    'json' => ':attribute ต้องเป็นข้อความ JSON ที่ถูกต้อง',
    'lt' => [
        'numeric' => ':attribute ต้องน้อยกว่า :value',
    ],
    'lte' => [
        'numeric' => ':attribute ต้องน้อยกว่าหรือเท่ากับ :value',
    ],
    'max' => [
        'numeric' => ':attribute ต้องมีค่าไม่เกิน :max',
        'file' => ':attribute ต้องมีขนาดไม่เกิน :max กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวไม่เกิน :max ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนไม่เกิน :max รายการ',
    ],
    'mimes' => ':attribute ต้องเป็นไฟล์ชนิด: :values',
    'mimetypes' => ':attribute ต้องเป็นไฟล์ชนิด: :values',
    'min' => [
        'numeric' => ':attribute ต้องมีค่าอย่างน้อย :min',
        'file' => ':attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
        'array' => ':attribute ต้องมีอย่างน้อย :min รายการ',
    ],
    'not_in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'numeric' => ':attribute ต้องเป็นตัวเลข',
    'present' => 'ต้องมี :attribute',
    'regex' => 'รูปแบบ :attribute ไม่ถูกต้อง',
    'required' => 'กรุณากรอก :attribute',
    'required_if' => 'กรุณากรอก :attribute เมื่อ :other เป็น :value',
    'required_with' => 'กรุณากรอก :attribute เมื่อมี :values',
    'same' => ':attribute และ :other ต้องเหมือนกัน',
    'size' => [
        'numeric' => ':attribute ต้องมีค่าเท่ากับ :size',
        'file' => ':attribute ต้องมีขนาด :size กิโลไบต์',
        'string' => ':attribute ต้องมีความยาว :size ตัวอักษร',
    ],
    'starts_with' => ':attribute ต้องขึ้นต้นด้วย: :values',
    'string' => ':attribute ต้องเป็นข้อความ',
    'unique' => ':attribute นี้ถูกใช้ไปแล้ว',
    'uploaded' => 'อัปโหลด :attribute ไม่สำเร็จ',
    'url' => ':attribute ต้องเป็น URL ที่ถูกต้อง',

    /*
    | ข้อความเฉพาะเจาะจง (ต่อ field + rule)
    */
    'custom' => [
        'password' => [
            'min' => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
        ],
    ],

    /*
    | ชื่อ field ภาษาไทย (ใช้แทน :attribute)
    */
    'attributes' => [
        'name' => 'ชื่อที่แสดง',
        'username' => 'ชื่อผู้ใช้',
        'email' => 'อีเมล',
        'password' => 'รหัสผ่าน',
        'password_confirmation' => 'การยืนยันรหัสผ่าน',
        'display_name' => 'ชื่อที่แสดง',
        'description' => 'คำอธิบาย',
        'currency' => 'สกุลเงิน',
        'donor_name' => 'ชื่อผู้โดเนท',
        'amount' => 'จำนวนเงิน',
        'message' => 'ข้อความ',
        'title' => 'หัวข้อ',
        'min_amount' => 'จำนวนเงินขั้นต่ำ',
        'max_amount' => 'จำนวนเงินสูงสุด',
        'quick_amounts' => 'ปุ่มจำนวนเงินด่วน',
        'thank_you_message' => 'ข้อความขอบคุณ',
        'accent_color' => 'สีหลัก',
        'theme' => 'ธีม',
        'payment_method' => 'ช่องทางการชำระเงิน',
        'bank_name' => 'ชื่อธนาคาร',
        'account_name' => 'ชื่อบัญชี',
        'account_number' => 'เลขที่บัญชี',
        'avatar' => 'รูปโปรไฟล์',
        'qr_code' => 'QR Code',
        'file' => 'ไฟล์',
        'platform_name' => 'ชื่อแพลตฟอร์ม',
        'default_currency' => 'สกุลเงินเริ่มต้น',
        'tts_template' => 'ข้อความเสียงอ่าน',
        'tts_language' => 'ภาษาเสียงอ่าน',
        'font_family' => 'ฟอนต์',
        'font_size' => 'ขนาดตัวอักษร',
        'width' => 'ความกว้าง',
        'position' => 'ตำแหน่ง',
    ],
];
