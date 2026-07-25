<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Admin-configurable platform settings, stored in the `settings` table so the
 * client can edit the site (name, branding, contact, banner, limits...) without
 * touching code — and so values survive `cache:clear` / deploys.
 *
 * all() is resilient: if the table doesn't exist yet (e.g. before migrating on
 * a fresh deploy) it falls back to defaults instead of crashing the whole app.
 */
class GlobalSettings
{
    /** @var array<string, mixed>|null */
    private ?array $memo = null;

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        $stored = [];

        try {
            if (Schema::hasTable('settings')) {
                foreach (Setting::all(['key', 'value']) as $row) {
                    $stored[$row->key] = json_decode((string) $row->value, true);
                }
            }
        } catch (Throwable) {
            // DB not ready yet — just use defaults
            $stored = [];
        }

        return $this->memo = array_merge($this->defaults(), $stored);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => json_encode($value, JSON_UNESCAPED_UNICODE)],
            );
        }

        $this->memo = null; // invalidate per-request cache
    }

    /**
     * Default values for every editable setting. Adding a key here makes it
     * available everywhere via $settings->get('key') even before it's saved.
     *
     * @return array<string, mixed>
     */
    private function defaults(): array
    {
        return [
            // ---- Identity / branding ----
            'platform_name' => config('app.name', 'TIPLAO DONATE'),
            'tagline' => 'ແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ',
            'logo_url' => null,        // ถ้าว่าง = ใช้โลโก้ SVG เริ่มต้น
            'favicon_url' => null,
            'brand_color' => null,     // hex เช่น #7c3aed (ถ้าว่าง = ใช้สีธีมเดิม)
            'meta_description' => 'ຮັບໂດເນດ ແລະ ແຈ້ງເຕືອນໃນໄລຟ໌ ພ້ອມ Overlay ສຳລັບ OBS ແລະ ສຽງອ່ານພາສາລາວ',

            // ---- Behavior ----
            'default_currency' => config('newlab.default_currency', 'LAK'),
            'registration_open' => true,

            // ---- Central donation account / creator payouts ----
            'central_payment_enabled' => true,
            'central_bank_name' => null,
            'central_account_name' => null,
            'central_account_number' => null,
            'central_qr_url' => null,
            'platform_fee_percent' => 0,
            'withdrawal_min_amount' => 50000,
            'withdrawal_fee' => 0,
            'withdrawal_processing_days' => 3,

            // ---- Announcement banner (แถบประกาศบนสุด) ----
            'announcement_enabled' => false,
            'announcement_text' => '',

            // ---- Maintenance (ปิดเว็บชั่วคราวสำหรับผู้ใช้ทั่วไป แอดมินยังเข้าได้) ----
            'maintenance_enabled' => false,
            'maintenance_message' => 'ລະບົບກຳລັງປັບປຸງຊົ່ວຄາວ ກະລຸນາກັບມາໃໝ່ອີກຄັ້ງ 🙏',

            // ---- Contact / social (ใช้ที่ footer) ----
            'contact_email' => null,
            'social_facebook' => null,
            'social_youtube' => null,
            'social_tiktok' => null,
            'social_discord' => null,
            'social_telegram' => null,
            'social_line' => null,
            'footer_text' => null, // ถ้าว่าง = ใช้ข้อความ © ปี + ชื่อเว็บ
        ];
    }
}
