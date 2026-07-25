@extends('layouts.app')
@section('title', 'ຕັ້ງຄ່າລະບົບ')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="mx-auto max-w-2xl space-y-6">
    @csrf @method('PUT')

    {{-- ===== เอกลักษณ์ & แบรนด์ ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ເອກະລັກ ແລະ ແບຣນ</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຊື່ເວັບ / ແພລດຟອມ</label>
                <input name="platform_name" value="{{ old('platform_name', $settings['platform_name']) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ສະໂລແກນ (Tagline)</label>
                <input name="tagline" value="{{ old('tagline', $settings['tagline']) }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">ລິ້ງໂລໂກ້ (URL ຮູບ — ປ່ອຍວ່າງ = ໃຊ້ໂລໂກ້ເລີ່ມຕົ້ນ)</label>
                <input name="logo_url" value="{{ old('logo_url', $settings['logo_url']) }}" class="nl-input" placeholder="https://.../logo.png">
            </div>
            <div>
                <label class="nl-label">ລິ້ງ favicon (URL)</label>
                <input name="favicon_url" value="{{ old('favicon_url', $settings['favicon_url']) }}" class="nl-input" placeholder="https://.../favicon.ico">
            </div>
            <div>
                <label class="nl-label">ສີແບຣນ (hex — ປ່ອຍວ່າງ = ໃຊ້ສີທີມເດີມ)</label>
                <input name="brand_color" value="{{ old('brand_color', $settings['brand_color']) }}" class="nl-input font-mono" placeholder="#7c3aed">
            </div>
            <div>
                <label class="nl-label">ຄຳອະທິບາຍເວັບ (SEO meta description)</label>
                <input name="meta_description" value="{{ old('meta_description', $settings['meta_description']) }}" class="nl-input">
            </div>
        </div>
    </div>

    {{-- ===== การทำงาน ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ການເຮັດວຽກ</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ສະກຸນເງິນຫຼັກ</label>
                <select name="default_currency" class="nl-input">
                    @foreach ($currencies as $cur)
                        <option value="{{ $cur }}" @selected(old('default_currency', $settings['default_currency']) === $cur)>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="registration_open" value="1" @checked(old('registration_open', $settings['registration_open'])) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    ເປີດໃຫ້ສະໝັກສະຕຣີມເມີໃໝ່
                </label>
            </div>
        </div>
    </div>

    {{-- ===== Central payment account ===== --}}
    <div class="nl-card-glow p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="font-semibold">ບັນຊີຮັບຊຳລະຂອງແພລດຟອມ / ຜູ້ໃຫ້ບໍລິການ</h3>
                <p class="mt-1 text-xs leading-5 text-slate-400">ຜູ້ໂດເນດຈະເຫັນບັນຊີນີ້ໃນຂັ້ນຕອນຊຳລະ. ຕອນນີ້ໃຊ້ການກວດກາແບບ manual; ສາມາດປ່ຽນໄປໃຊ້ Payment Gateway ໃນພາຍຫຼັງ.</p>
            </div>
            <label class="flex shrink-0 items-center gap-2 text-sm">
                <input type="checkbox" name="central_payment_enabled" value="1" @checked(old('central_payment_enabled', $settings['central_payment_enabled'])) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                ເປີດຮັບເງິນ
            </label>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຊື່ທະນາຄານ</label>
                <input name="central_bank_name" value="{{ old('central_bank_name', $settings['central_bank_name']) }}" class="nl-input" placeholder="ຕົວຢ່າງ: BCEL">
            </div>
            <div>
                <label class="nl-label">ຊື່ບັນຊີຮັບຊຳລະ</label>
                <input name="central_account_name" value="{{ old('central_account_name', $settings['central_account_name']) }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">ເລກບັນຊີຮັບຊຳລະ</label>
                <input name="central_account_number" value="{{ old('central_account_number', $settings['central_account_number']) }}" class="nl-input font-mono">
            </div>
            <div>
                <label class="nl-label">URL ຮູບ Lao QR ຮັບຊຳລະ</label>
                <input name="central_qr_url" value="{{ old('central_qr_url', $settings['central_qr_url']) }}" class="nl-input" placeholder="https://.../central-qr.png">
            </div>
        </div>

        <div class="mt-5 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-900 dark:border-amber-700/60 dark:bg-amber-950/30 dark:text-amber-200">
            ຕ້ອງກຳນົດເລກບັນຊີ ຫຼື QR ຢ່າງໜ້ອຍ 1 ຢ່າງ. ບັນຊີກາງຄວນແຍກອອກຈາກເງິນດຳເນີນງານຂອງບໍລິສັດ ແລະ ກວດສອບຂໍ້ກຳນົດຂອງທະນາຄານແຫ່ງ ສປປ ລາວ ກ່ອນເປີດໃຊ້ຈິງ.
        </div>
    </div>

    {{-- ===== Creator payout rules ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ຄ່າທຳນຽມ ແລະ ເງື່ອນໄຂຖອນເງິນ</h3>
        <p class="mt-1 text-xs text-slate-400">ຄ່າທຳນຽມທັງໝົດຈະສະແດງໃຫ້ຄຣີເອເຕີເຫັນກ່ອນຢືນຢັນ.</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຄ່າບໍລິການຈາກໂດເນດ (%)</label>
                <input name="platform_fee_percent" type="number" min="0" max="100" step="0.01" value="{{ old('platform_fee_percent', $settings['platform_fee_percent']) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ຈຳນວນຖອນຂັ້ນຕ່ຳ (LAK)</label>
                <input name="withdrawal_min_amount" type="number" min="0" step="1000" value="{{ old('withdrawal_min_amount', $settings['withdrawal_min_amount']) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ຄ່າທຳນຽມຖອນຕໍ່ຄັ້ງ (LAK)</label>
                <input name="withdrawal_fee" type="number" min="0" step="1000" value="{{ old('withdrawal_fee', $settings['withdrawal_fee']) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ໄລຍະດຳເນີນການໂດຍປົກກະຕິ (ວັນເຮັດວຽກ)</label>
                <input name="withdrawal_processing_days" type="number" min="1" max="30" value="{{ old('withdrawal_processing_days', $settings['withdrawal_processing_days']) }}" class="nl-input" required>
            </div>
        </div>
    </div>

    {{-- ===== แถบประกาศ ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ແຖບປະກາດ (ສະແດງເທິງສຸດຂອງໜ້າເວັບສາທາລະນະ)</h3>
        <div class="mt-5 space-y-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="announcement_enabled" value="1" @checked(old('announcement_enabled', $settings['announcement_enabled'])) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                ເປີດແຖບປະກາດ
            </label>
            <div>
                <label class="nl-label">ຂໍ້ຄວາມປະກາດ</label>
                <input name="announcement_text" value="{{ old('announcement_text', $settings['announcement_text']) }}" class="nl-input" placeholder="ຕົວຢ່າງ: ໂປຣໂມຊັນພິເສດ / ປິດປັບປຸງ 20:00">
            </div>
        </div>
    </div>

    {{-- ===== โหมดปิดปรับปรุง ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ໂໝດປິດປັບປຸງ</h3>
        <p class="mt-1 text-xs text-slate-400">ເມື່ອເປີດ: ຜູ້ເຂົ້າຊົມຈະເຫັນໜ້າປິດປັບປຸງ ແຕ່ແອັດມິນຍັງໃຊ້ງານໄດ້</p>
        <div class="mt-5 space-y-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="maintenance_enabled" value="1" @checked(old('maintenance_enabled', $settings['maintenance_enabled'])) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                ເປີດໂໝດປິດປັບປຸງ
            </label>
            <div>
                <label class="nl-label">ຂໍ້ຄວາມໜ້າປິດປັບປຸງ</label>
                <textarea name="maintenance_message" rows="2" class="nl-input">{{ old('maintenance_message', $settings['maintenance_message']) }}</textarea>
            </div>
        </div>
    </div>

    {{-- ===== ติดต่อ & โซเชียล ===== --}}
    <div class="nl-card p-6">
        <h3 class="font-semibold">ຕິດຕໍ່ ແລະ ໂຊຊຽວ (ສະແດງທີ່ footer)</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ອີເມວຕິດຕໍ່</label>
                <input name="contact_email" type="email" value="{{ old('contact_email', $settings['contact_email']) }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">ຂໍ້ຄວາມ footer (ປ່ອຍວ່າງ = © ປີ + ຊື່ເວັບ)</label>
                <input name="footer_text" value="{{ old('footer_text', $settings['footer_text']) }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">Facebook</label>
                <input name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}" class="nl-input" placeholder="https://facebook.com/...">
            </div>
            <div>
                <label class="nl-label">YouTube</label>
                <input name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}" class="nl-input" placeholder="https://youtube.com/...">
            </div>
            <div>
                <label class="nl-label">TikTok</label>
                <input name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok']) }}" class="nl-input" placeholder="https://tiktok.com/@...">
            </div>
            <div>
                <label class="nl-label">Discord</label>
                <input name="social_discord" value="{{ old('social_discord', $settings['social_discord']) }}" class="nl-input" placeholder="https://discord.gg/...">
            </div>
            <div>
                <label class="nl-label">Telegram</label>
                <input name="social_telegram" value="{{ old('social_telegram', $settings['social_telegram']) }}" class="nl-input" placeholder="https://t.me/...">
            </div>
            <div>
                <label class="nl-label">LINE</label>
                <input name="social_line" value="{{ old('social_line', $settings['social_line']) }}" class="nl-input" placeholder="https://line.me/...">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button class="nl-btn-primary">ບັນທຶກການຕັ້ງຄ່າ</button>
    </div>
</form>
@endsection
