@extends('layouts.app')
@section('title', 'ຕັ້ງຄ່າ Overlay')

@php
    use App\Support\Money;
    $s = $settings;
    $curLabel = $streamer->currency === 'LAK' ? 'ກີບ' : $streamer->currency;

    // ไอคอน SVG สำหรับ UI (แทนอิโมจิ) — path ชุด stroke สไตล์เดียวกับทั้งเว็บ
    $iconPaths = [
        'palette' => '<path d="M12 2a10 10 0 1 0 0 20 2.4 2.4 0 0 0 2-3.8 2.4 2.4 0 0 1 2-3.8h1.2A3.8 3.8 0 0 0 21 10.6 9 9 0 0 0 12 2Z"/><circle cx="7.5" cy="11" r="1.1"/><circle cx="12" cy="7.5" r="1.1"/><circle cx="16.5" cy="11" r="1.1"/>',
        'type'    => '<path d="M5 5h14M12 5v14M8.5 19h7"/>',
        'layout'  => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/>',
        'sparkle' => '<path d="M12 3l1.7 4.3L18 9l-4.3 1.7L12 15l-1.7-4.3L6 9l4.3-1.7L12 3Z"/><path d="M18.5 14.5l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8Z"/>',
        'speaker' => '<path d="M11 5 6 9H2v6h4l5 4V5Z"/><path d="M15.5 8.5a5 5 0 0 1 0 7M18.5 5.5a9 9 0 0 1 0 13"/>',
        'mic'     => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3M8 21h8"/>',
        'gift'    => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"/><path d="M12 8S10.5 4 8 4a2 2 0 0 0 0 4h4zM12 8s1.5-4 4-4a2 2 0 0 1 0 4h-4z"/>',
        'copy'    => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'link'    => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        'refresh' => '<path d="M20.5 12a8.5 8.5 0 1 1-2.6-6.1M20.5 4v5h-5"/>',
        'monitor' => '<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>',
        'save'    => '<path d="M5 3h11l3 3v15H5V3Z"/><path d="M8 3v6h8M8 21v-6h8v6"/>',
        'play'    => '<path d="M7 4v16l13-8L7 4Z"/>',
        'check'   => '<path d="M5 13l4 4L19 7"/>',
    ];
    $oicon = fn (string $n, string $cls = 'h-4 w-4') =>
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="'.$cls.' shrink-0">'.($iconPaths[$n] ?? '').'</svg>';

    // มินิเลย์เอาต์ SVG สำหรับสวอตช์เลือกสไตล์การ์ด (โครงต่างกันให้เห็นชัด)
    $styleSwatch = [
        'classic'     => '<rect x="14" y="6" width="12" height="6" rx="3"/><rect x="8" y="16" width="24" height="4" rx="2"/><rect x="12" y="24" width="16" height="3" rx="1.5"/>',
        'banner'      => '<circle cx="10" cy="16" r="5"/><rect x="19" y="12" width="15" height="4" rx="2"/><rect x="19" y="19" width="10" height="3" rx="1.5"/>',
        'pill'        => '<rect x="4" y="12" width="32" height="9" rx="4.5"/><circle cx="10" cy="16.5" r="2.5"/><rect x="15" y="15" width="17" height="3" rx="1.5"/>',
        'hero'        => '<circle cx="20" cy="8" r="4"/><rect x="10" y="15" width="20" height="7" rx="2"/><rect x="14" y="25" width="12" height="3" rx="1.5"/>',
        'side-accent' => '<rect x="5" y="9" width="3" height="15" rx="1.5"/><rect x="12" y="12" width="22" height="4" rx="2"/><rect x="12" y="19" width="14" height="3" rx="1.5"/>',
    ];
@endphp

@section('content')
<style>
    /* คีย์เฟรมแอนิเมชันสำหรับเล่นตัวอย่าง overlay ในหน้านี้ (ชุดเดียวกับ renderer จริง) */
    @keyframes nl-fade-in   { from { opacity: 0 } to { opacity: 1 } }
    @keyframes nl-fade-out  { from { opacity: 1 } to { opacity: 0 } }
    @keyframes nl-zoom-in   { from { opacity: 0; transform: scale(.7) } to { opacity: 1; transform: scale(1) } }
    @keyframes nl-zoom-out  { from { opacity: 1; transform: scale(1) } to { opacity: 0; transform: scale(.7) } }
    @keyframes nl-slide-in  { from { opacity: 0; transform: translateY(-40px) } to { opacity: 1; transform: translateY(0) } }
    @keyframes nl-slide-out { from { opacity: 1; transform: translateY(0) } to { opacity: 0; transform: translateY(-40px) } }
    @keyframes nl-slideleft-in   { from { opacity: 0; transform: translateX(-60px) } to { opacity: 1; transform: translateX(0) } }
    @keyframes nl-slideleft-out  { from { opacity: 1; transform: translateX(0) } to { opacity: 0; transform: translateX(-60px) } }
    @keyframes nl-slideright-in  { from { opacity: 0; transform: translateX(60px) } to { opacity: 1; transform: translateX(0) } }
    @keyframes nl-slideright-out { from { opacity: 1; transform: translateX(0) } to { opacity: 0; transform: translateX(60px) } }
    @keyframes nl-bounce-in { 0% { opacity: 0; transform: scale(.3) } 60% { opacity: 1; transform: scale(1.08) } 100% { transform: scale(1) } }
    @keyframes nl-pop-in    { 0% { opacity: 0; transform: scale(.5) } 55% { opacity: 1; transform: scale(1.12) } 75% { transform: scale(.96) } 100% { transform: scale(1) } }
    @keyframes nl-pop-out   { from { opacity: 1; transform: scale(1) } to { opacity: 0; transform: scale(.5) } }
    @keyframes nl-flip-in   { from { opacity: 0; transform: perspective(700px) rotateY(90deg) } to { opacity: 1; transform: perspective(700px) rotateY(0) } }
    @keyframes nl-flip-out  { from { opacity: 1; transform: perspective(700px) rotateY(0) } to { opacity: 0; transform: perspective(700px) rotateY(-90deg) } }
    @keyframes nl-glitch-in { 0% { opacity: 0; transform: translate(0) } 20% { opacity: 1; transform: translate(-6px, 3px) } 40% { transform: translate(6px, -3px) } 60% { transform: translate(-4px, -2px) } 80% { transform: translate(4px, 2px) } 100% { opacity: 1; transform: translate(0) } }

    /* โครงการ์ดพรีวิว (ต้องตรงกับ renderer overlay/show.blade.php) */
    .pvcard { display: flex; flex-direction: column; align-items: center; gap: 2px; text-align: center; }
    .pvcard .accent-icon { display: none; color: var(--accent, currentColor); }
    .pvcard .accent-icon svg { display: block; width: 100%; height: 100%; }
    .pvcard .body { width: 100%; }
    .pvcard.as-banner { flex-direction: row; align-items: center; text-align: left; gap: 14px; border-left: 5px solid var(--accent, currentColor); }
    .pvcard.as-banner .accent-icon { display: block; width: 40px; height: 40px; flex: 0 0 auto; }
    .pvcard.as-banner .avatar { margin: 0 !important; }
    .pvcard.as-banner .body { width: auto; }
    .pvcard.as-pill { flex-direction: row; align-items: center; gap: 10px; border-radius: 9999px !important; text-align: left; padding: 10px 20px !important; }
    .pvcard.as-pill .accent-icon { display: block; width: 24px; height: 24px; flex: 0 0 auto; }
    .pvcard.as-pill .avatar { width: 32px !important; height: 32px !important; margin: 0 !important; }
    .pvcard.as-pill .pv-message { display: none; }
    .pvcard.as-pill .body { width: auto; }
    .pvcard.as-hero .accent-icon { display: block; width: 50px; height: 50px; margin: 0 auto 4px; }
    .pvcard.as-hero .pv-amount { display: block; font-size: 1.7em; margin-top: 4px; }
    .pvcard.as-side-accent { flex-direction: row; align-items: center; text-align: left; gap: 12px; position: relative; }
    .pvcard.as-side-accent::before { content: ''; position: absolute; left: 6px; top: 10px; bottom: 10px; width: 5px; border-radius: 4px; background: var(--accent, currentColor); }
    .pvcard.as-side-accent .accent-icon { display: block; width: 34px; height: 34px; flex: 0 0 auto; margin-left: 6px; }
    .pvcard.as-side-accent .avatar { margin: 0 !important; }
</style>

<div x-data="overlayConfig()" class="space-y-6">

    {{-- ลิงก์ OBS --}}
    <div class="nl-card-glow p-6" x-data="{ copied: false }">
        <div class="flex items-center gap-2">
            <span class="nl-chip inline-flex items-center gap-1.5">{!! $oicon('monitor', 'h-4 w-4') !!} OBS Browser Source</span>
        </div>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">ນຳລິ້ງນີ້ໄປວາງໃນ OBS → Sources → Browser (ພື້ນຫຼັງໂປ່ງໃສອັດຕະໂນມັດ) ແນະນຳຂະໜາດ 1920 × 1080</p>
        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
            <input readonly value="{{ $overlayUrl }}" class="nl-input font-mono text-sm" x-ref="url">
            <button type="button" @click="navigator.clipboard.writeText($refs.url.value); copied=true; setTimeout(()=>copied=false,2000)" class="nl-btn-primary shrink-0 inline-flex items-center justify-center gap-1.5">
                <span x-show="!copied" class="inline-flex items-center gap-1.5">{!! $oicon('copy', 'h-4 w-4') !!} ຄັດລອກລິ້ງ</span><span x-show="copied" x-cloak class="inline-flex items-center gap-1.5">{!! $oicon('check', 'h-4 w-4') !!} ຄັດລອກແລ້ວ</span>
            </button>
            <a href="{{ $overlayUrl }}" target="_blank" class="nl-btn-ghost shrink-0 inline-flex items-center gap-1.5">{!! $oicon('link', 'h-4 w-4') !!} ເປີດເບິ່ງ</a>
        </div>
        <form method="POST" action="{{ route('overlay-settings.regenerate') }}" class="mt-3"
              onsubmit="return confirm('ສ້າງລິ້ງ Overlay ໃໝ່ບໍ? ລິ້ງເກົ່າຈະໃຊ້ບໍ່ໄດ້ອີກ')">
            @csrf
            <button class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-500 hover:underline">{!! $oicon('refresh', 'h-3.5 w-3.5') !!} ສ້າງລິ້ງໃໝ່ (ກໍລະນີລິ້ງຮົ່ວໄຫຼ)</button>
        </form>
    </div>

    <form method="POST" action="{{ route('overlay-settings.update') }}">
        @csrf @method('PUT')

        <div class="grid gap-6 lg:grid-cols-5">
            {{-- ===== คอลัมน์ซ้าย: ฟอร์มตั้งค่า (แท็บ) ===== --}}
            <div class="space-y-4 lg:col-span-3">
                {{-- แท็บ --}}
                <div class="flex flex-wrap gap-1.5 rounded-xl border border-slate-200 bg-white p-1.5 dark:border-white/10 dark:bg-slate-900/60">
                    @foreach ([
                        'theme' => ['palette', 'ທີມ ແລະ ສະໄຕລ໌'],
                        'text'  => ['type', 'ຕົວອັກສອນ ແລະ ກ່ອງ'],
                        'anim'  => ['sparkle', 'ອະນິເມຊັນ'],
                        'sound' => ['speaker', 'ສຽງ'],
                        'tts'   => ['mic', 'ສຽງອ່ານ (TTS)'],
                    ] as $key => [$ic, $label])
                        <button type="button" @click="tab='{{ $key }}'"
                                class="flex flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition"
                                :class="tab==='{{ $key }}' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5'">
                            {!! $oicon($ic, 'h-4 w-4') !!}{{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- ---- แท็บ: ธีม & สไตล์ ---- --}}
                <div x-show="tab==='theme'" class="space-y-4">
                    {{-- รูปแบบการ์ด (layout) --}}
                    <div class="nl-card p-6">
                        <h3 class="font-semibold">ຮູບແບບກາດແຈ້ງເຕືອນ (Alert Style)</h3>
                        <p class="mt-1 text-xs text-slate-400">ໂຄງສ້າງຂອງກ່ອງແຈ້ງເຕືອນ — ແຕ່ລະແບບຈັດວາງຕ່າງກັນ ກົດ “ຫຼິ້ນຕົວຢ່າງ” ເພື່ອເບິ່ງ</p>
                        <input type="hidden" name="alert_style" :value="form.alert_style">
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach (config('newlab.overlay.alert_styles') as $key => $meta)
                                <button type="button" @click="form.alert_style='{{ $key }}'"
                                        class="group relative flex flex-col items-center gap-2 rounded-xl border-2 p-3 transition"
                                        :class="form.alert_style === '{{ $key }}' ? 'border-brand-500' : 'border-transparent ring-1 ring-slate-200 dark:ring-white/10 hover:ring-brand-400'">
                                    <span class="grid h-12 w-full place-items-center rounded-lg bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-300">
                                        <svg viewBox="0 0 40 32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-auto">{!! $styleSwatch[$key] ?? '' !!}</svg>
                                    </span>
                                    <span class="text-xs font-semibold">{{ $meta['label'] }}</span>
                                    <span x-show="form.alert_style === '{{ $key }}'" class="absolute right-2 top-2 grid h-5 w-5 place-items-center rounded-full bg-brand-500 text-white">
                                        {!! $oicon('check', 'h-3 w-3') !!}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- ธีมสี --}}
                    <div class="nl-card p-6">
                    <h3 class="font-semibold">ເລືອກທີມສີ</h3>
                    <p class="mt-1 text-xs text-slate-400">ເລືອກຊຸດສີສຳເລັດຮູບ ແລ້ວກົດ “ຫຼິ້ນຕົວຢ່າງ” ດ້ານຂວາ</p>
                    <input type="hidden" name="theme" :value="form.theme">
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <template x-for="(t, key) in themes" :key="key">
                            <button type="button" @click="form.theme = key"
                                    class="group relative overflow-hidden rounded-xl border-2 p-3 text-left transition"
                                    :class="form.theme === key ? 'border-brand-500' : 'border-transparent ring-1 ring-slate-200 dark:ring-white/10 hover:ring-brand-400'">
                                <div class="flex h-14 items-center justify-center rounded-lg text-xs font-bold text-white"
                                     :style="previewSwatch(key)">ໂດເນດ</div>
                                <p class="mt-2 truncate text-xs font-semibold" x-text="t.label"></p>
                                <span x-show="form.theme === key" class="absolute right-2 top-2 grid h-5 w-5 place-items-center rounded-full bg-brand-500 text-white">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="h-3 w-3"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </button>
                        </template>
                    </div>

                    {{-- สีกำหนดเอง (แสดงเมื่อเลือกธีม "กำหนดเอง"; input ยังอยู่ใน DOM เพื่อส่งค่าเสมอ) --}}
                    <div class="mt-5 grid gap-4 sm:grid-cols-2" x-show="form.theme === 'custom'" x-cloak>
                        <div>
                            <label class="nl-label">ສີພື້ນຫຼັງ (CSS color)</label>
                            <input name="background_color" x-model="form.background_color" class="nl-input" placeholder="rgba(17,17,27,0.9)">
                        </div>
                        <div>
                            <label class="nl-label">ສີຂໍ້ຄວາມ</label>
                            <input name="font_color" type="color" x-model="form.font_color" class="nl-input h-11 p-1">
                        </div>
                    </div>
                    </div>
                </div>

                {{-- ---- แท็บ: ตัวอักษร & กล่อง ---- --}}
                <div x-show="tab==='text'" x-cloak class="space-y-4">
                    <div class="nl-card p-6">
                        <h3 class="font-semibold">ຕົວອັກສອນ</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="nl-label">ຟອນ</label>
                                <select name="font_family" x-model="form.font_family" class="nl-input">
                                    <option value="'Noto Sans Lao', sans-serif">Noto Sans Lao (ແນະນຳ)</option>
                                    <option value="Kanit, sans-serif">Kanit</option>
                                    <option value="system-ui, sans-serif">System UI</option>
                                    <option value="Sarabun, sans-serif">Sarabun</option>
                                    <option value="Mitr, sans-serif">Mitr</option>
                                </select>
                            </div>
                            <div>
                                <label class="nl-label">ຂະໜາດຕົວອັກສອນ: <span x-text="form.font_size"></span> px</label>
                                <input name="font_size" type="range" min="16" max="72" x-model.number="form.font_size" class="w-full accent-brand-600">
                            </div>
                            <div>
                                <label class="nl-label">ນ້ຳໜັກຕົວອັກສອນ</label>
                                <select name="font_weight" x-model="form.font_weight" class="nl-input">
                                    @foreach (['300'=>'ບາງ','400'=>'ປົກກະຕິ','500'=>'ກາງ','600'=>'ກຶ່ງໜາ','700'=>'ໜາ','800'=>'ໜາຫຼາຍ','900'=>'ໜາສຸດ'] as $w => $lbl)
                                        <option value="{{ $w }}">{{ $lbl }} ({{ $w }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="nl-card p-6">
                        <h3 class="font-semibold">ກ່ອງ ແລະ ຕຳແໜ່ງ</h3>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="nl-label">ຄວາມໂຄ້ງມຸມ: <span x-text="form.border_radius"></span> px</label>
                                <input name="border_radius" type="range" min="0" max="48" x-model.number="form.border_radius" class="w-full accent-brand-600">
                            </div>
                            <div>
                                <label class="nl-label">ຄວາມກວ້າງ: <span x-text="form.width"></span> px</label>
                                <input name="width" type="range" min="280" max="900" step="10" x-model.number="form.width" class="w-full accent-brand-600">
                            </div>
                            <div>
                                <label class="nl-label">ຕຳແໜ່ງໃນຈໍ</label>
                                <select name="position" x-model="form.position" class="nl-input">
                                    @foreach ([
                                        'top-left'=>'ເທິງຊ້າຍ','top-center'=>'ເທິງກາງ','top-right'=>'ເທິງຂວາ',
                                        'middle-center'=>'ກາງຈໍ',
                                        'bottom-left'=>'ລຸ່ມຊ້າຍ','bottom-center'=>'ລຸ່ມກາງ','bottom-right'=>'ລຸ່ມຂວາ',
                                    ] as $p => $lbl)
                                        <option value="{{ $p }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="nl-label">ຄວາມສູງຂັ້ນຕ່ຳ (px, ບໍ່ບັງຄັບ)</label>
                                <input name="height" type="number" min="80" max="1080" x-model="form.height" class="nl-input" placeholder="ອັດຕະໂນມັດ">
                            </div>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="shadow" value="1" x-model="form.shadow" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ເປີດເງົາ / ແສງເຮືອງ
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="show_avatar" value="1" x-model="form.show_avatar" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງຮູບໂປຣໄຟລ໌
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ---- แท็บ: แอนิเมชัน ---- --}}
                <div x-show="tab==='anim'" x-cloak class="nl-card p-6">
                    <h3 class="font-semibold">ອະນິເມຊັນ ແລະ ການສະແດງຜົນ</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="nl-label">ຮູບແບບອະນິເມຊັນ</label>
                            <select name="animation" x-model="form.animation" class="nl-input">
                                @foreach (config('newlab.overlay.animations') as $a => $lbl)
                                    <option value="{{ $a }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="nl-label">ຄວາມໄວອະນິເມຊັນ: <span x-text="form.animation_duration"></span> ms</label>
                            <input name="animation_duration" type="range" min="100" max="2000" step="50" x-model.number="form.animation_duration" class="w-full accent-brand-600">
                        </div>
                        <div>
                            <label class="nl-label">ໄລຍະເວລາສະແດງ: <span x-text="(form.display_duration/1000).toFixed(1)"></span> ວິນາທີ</label>
                            <input name="display_duration" type="range" min="2000" max="20000" step="500" x-model.number="form.display_duration" class="w-full accent-brand-600">
                        </div>
                        <div>
                            <label class="nl-label">ສະແດງສະເພາະຍອດຕັ້ງແຕ່ ({{ $streamer->currency }})</label>
                            <input name="min_amount_to_show" type="number" min="0" step="0.01" x-model="form.min_amount_to_show" class="nl-input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="nl-label">ຮູບ/GIF/WEBM ທີ່ສະແດງເວລາແຈ້ງເຕືອນ</label>
                            <select name="image_media_id" x-model="form.image_media_id" class="nl-input">
                                <option value="">ບໍ່ໃຊ້</option>
                                @foreach ($images as $m)
                                    <option value="{{ $m->id }}">{{ $m->original_name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-400">ອັບໂຫຼດໄຟລ໌ໄດ້ທີ່ເມນູ “ຄັງສື່”</p>
                        </div>
                    </div>
                </div>

                {{-- ---- แท็บ: เสียง ---- --}}
                <div x-show="tab==='sound'" x-cloak class="nl-card p-6">
                    <h3 class="font-semibold">ສຽງແຈ້ງເຕືອນ</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="flex items-center gap-2 text-sm sm:col-span-2">
                            <input type="checkbox" name="sound_enabled" value="1" x-model="form.sound_enabled" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            ເປີດສຽງແຈ້ງເຕືອນ
                        </label>
                        <div class="sm:col-span-2">
                            <label class="nl-label">ສຽງແຈ້ງເຕືອນ</label>
                            <div class="flex gap-2">
                                <select x-model="soundChoice" class="nl-input">
                                    <option value="">🔇 ບໍ່ໃຊ້ສຽງ</option>
                                    <optgroup label="🔔 ສຽງໃນລະບົບ (ພ້ອມໃຊ້)">
                                        @foreach (config('newlab.sound_presets') as $key => $label)
                                            <option value="p:{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </optgroup>
                                    @if ($sounds->isNotEmpty())
                                        <optgroup label="📁 ສຽງທີ່ອັບໂຫຼດ">
                                            @foreach ($sounds as $m)
                                                <option value="m:{{ $m->id }}">{{ $m->original_name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                <button type="button" @click="previewSound()" class="nl-btn-ghost inline-flex shrink-0 items-center gap-1.5 text-sm" :disabled="!soundChoice">{!! $oicon('play', 'h-3.5 w-3.5') !!} ຟັງ</button>
                            </div>
                            {{-- แยกค่าไป 2 ฟิลด์: เสียงระบบ (preset) / ไฟล์อัปโหลด (media) --}}
                            <input type="hidden" name="sound_preset" :value="soundChoice.startsWith('p:') ? soundChoice.slice(2) : ''">
                            <input type="hidden" name="sound_media_id" :value="soundChoice.startsWith('m:') ? soundChoice.slice(2) : ''">
                            <p class="mt-1 text-xs text-slate-400">ອັບໂຫຼດສຽງເອງໄດ້ທີ່ເມນູ “ຄັງສື່” ແລ້ວຈະປາກົດໃນ “ສຽງທີ່ອັບໂຫຼດ”</p>
                        </div>
                        <div>
                            <label class="nl-label">ລະດັບສຽງ: <span x-text="form.sound_volume"></span>%</label>
                            <input name="sound_volume" type="range" min="0" max="100" x-model.number="form.sound_volume" class="w-full accent-brand-600">
                        </div>
                        <div>
                            <label class="nl-label">ໜ່ວງເວລາກ່ອນຫຼິ້ນ (ms)</label>
                            <input name="sound_delay" type="number" min="0" max="10000" x-model="form.sound_delay" class="nl-input">
                        </div>
                    </div>
                </div>

                {{-- ---- แท็บ: TTS (เสียงไทยล้วน) ---- --}}
                <div x-show="tab==='tts'" x-cloak class="nl-card p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">ສຽງອ່ານຂໍ້ຄວາມ (TTS) — ພາສາລາວ 🇱🇦</h3>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="tts_enabled" value="1" x-model="form.tts_enabled" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            ເປີດໃຊ້ງານ
                        </label>
                    </div>
                    {{-- ภาษาไทยตายตัว (ส่งค่าไปเสมอ) --}}
                    <input type="hidden" name="tts_language" value="lo-LA">

                    <p class="mt-2 text-xs text-slate-400">ຕົວແປທີ່ໃຊ້ໄດ້ (ກົດເພື່ອແຊກ):</p>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach (['{donor_name}'=>'ຊື່ຜູ້ໂດເນດ','{amount}'=>'ຈຳນວນເງິນ','{currency}'=>'ສະກຸນເງິນ','{message}'=>'ຂໍ້ຄວາມ','{streamer_name}'=>'ຊື່ສະຕຣີມເມີ'] as $var => $desc)
                            <button type="button" @click="insertVar('{{ $var }}')" class="nl-chip" title="{{ $desc }}">{{ $var }}</button>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <label class="nl-label">ຂໍ້ຄວາມທີ່ຈະໃຫ້ອ່ານ (Template)</label>
                        <textarea name="tts_template" x-ref="ttsTemplate" x-model="form.tts_template" rows="2" class="nl-input" placeholder="{{ config('newlab.tts.default_template') }}"></textarea>
                    </div>

                    {{-- ตัวอย่างการอ่าน --}}
                    <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold text-slate-400">ຕົວຢ່າງການອ່ານ</p>
                        <p class="mt-1 text-sm" x-text="ttsPreview()"></p>
                        <button type="button" @click="testSpeak()" class="nl-btn-primary mt-3 inline-flex items-center gap-1.5 text-sm">{!! $oicon('play', 'h-3.5 w-3.5') !!} ທົດລອງຟັງສຽງລາວ</button>
                        <span x-show="speaking" x-cloak class="ml-2 text-xs text-brand-400">ກຳລັງອ່ານ…</span>
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="nl-label">ສຽງເວົ້າ</label>

                            {{-- คาแรกเตอร์สำเร็จรูป (กดแล้วตั้งเสียง + โทน + ความเร็วให้) --}}
                            <div class="mb-2 flex flex-wrap gap-1.5">
                                @foreach ([
                                    ['🌸 ສຽງຜູ້ຍິງ', 'edge:lo-LA-KeomanyNeural', 1, 1],
                                    ['🧔 ສຽງຜູ້ຊາຍ', 'edge:lo-LA-ChanthavongNeural', 0.95, 0.95],
                                ] as $p)
                                    <button type="button" @click="applyPreset('{{ $p[1] }}', {{ $p[2] }}, {{ $p[3] }})" class="nl-chip">{{ $p[0] }}</button>
                                @endforeach
                            </div>

                            <select name="tts_voice" x-model="form.tts_voice" class="nl-input">
                                <option value="">ອັດຕະໂນມັດ (ເລືອກສຽງທີ່ດີທີ່ສຸດ)</option>
                                <optgroup label="🌐 ສຽງ Neural ອອນລາຍ (ຄຸນນະພາບສູງ · ແນະນຳ)">
                                    @foreach (config('newlab.tts.edge_voices') as $key => $label)
                                        <option value="edge:{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="💻 ສຽງໃນເຄື່ອງ (ຕິດຕັ້ງໃນເຄື່ອງ OBS)">
                                    <template x-for="v in voices" :key="v.name">
                                        <option :value="v.name" x-text="v.label"></option>
                                    </template>
                                </optgroup>
                            </select>

                            <p class="mt-1.5 text-xs text-slate-400">ສຽງ Neural ອອນລາຍຫຼິ້ນຜ່ານເຊີບເວີ (ບໍ່ຕ້ອງຕິດຕັ້ງໃນເຄື່ອງ OBS) · ຖ້າໂຮສເຊື່ອມ Edge ບໍ່ໄດ້ ລະບົບຈະໃຊ້ສຽງ Google ສຳຮອງອັດຕະໂນມັດ</p>
                            <p x-show="ttsSource" x-cloak class="mt-1.5 text-xs font-semibold" :class="ttsSource === 'edge' ? 'text-emerald-500' : 'text-amber-500'"
                               x-text="ttsSource === 'edge' ? '✓ ຫຼິ້ນດ້ວຍສຽງ Neural (Edge) — ໂຮສໃຊ້ງານໄດ້' : '⚠ Edge ໃຊ້ບໍ່ໄດ້ໃນໂຮສນີ້ → ກຳລັງໃຊ້ສຽງ Google ສຳຮອງ'"></p>
                            <p x-show="voices.length === 0" x-cloak class="mt-1.5 rounded-lg bg-sky-50 px-3 py-2 text-xs text-sky-600 dark:bg-sky-500/10 dark:text-sky-400">
                                ℹ️ ເຄື່ອງນີ້ຍັງບໍ່ມີສຽງລາວຕິດຕັ້ງ — ແນະນຳໃຊ້ <b>ສຽງ Neural ອອນລາຍ</b> ດ້ານເທິງ
                            </p>
                        </div>
                        <div>
                            <label class="nl-label">ຄວາມໄວ: <span x-text="Number(form.tts_rate).toFixed(2)"></span>x</label>
                            <input name="tts_rate" type="range" min="0.5" max="2" step="0.05" x-model.number="form.tts_rate" class="w-full accent-brand-600">
                        </div>
                        <div>
                            <label class="nl-label">ໂທນສຽງ (Pitch): <span x-text="Number(form.tts_pitch).toFixed(2)"></span></label>
                            <input name="tts_pitch" type="range" min="0" max="2" step="0.05" x-model.number="form.tts_pitch" class="w-full accent-brand-600">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="nl-label">ລະດັບສຽງອ່ານ: <span x-text="form.tts_volume"></span>%</label>
                            <input name="tts_volume" type="range" min="0" max="100" x-model.number="form.tts_volume" class="w-full accent-brand-600">
                        </div>
                    </div>
                </div>

                <button class="nl-btn-primary inline-flex w-full items-center justify-center gap-2 py-3 text-base">{!! $oicon('save', 'h-5 w-5') !!} ບັນທຶກການຕັ້ງຄ່າ Overlay</button>
            </div>

            {{-- ===== คอลัมน์ขวา: Live Preview + ปุ่มเล่นตัวอย่าง ===== --}}
            <div class="lg:col-span-2">
                <div class="sticky top-20 space-y-4">
                    <div class="nl-card-glow p-5">
                        <div class="flex items-center gap-2">
                            <span class="nl-live-dot inline-block h-2 w-2 rounded-full bg-rose-500"></span>
                            <h3 class="text-sm font-semibold">ຕົວຢ່າງສົດ (Live Preview)</h3>
                        </div>
                        {{-- พื้นหลังตารางหมากรุก = พื้นโปร่งใส + เวที (stage) เล่นแอนิเมชันจริง --}}
                        <div class="relative mt-3 grid min-h-[240px] place-items-center overflow-hidden rounded-xl p-4"
                             style="background-image:linear-gradient(45deg,#1118 25%,transparent 25%),linear-gradient(-45deg,#1118 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#1118 75%),linear-gradient(-45deg,transparent 75%,#1118 75%);background-size:20px 20px;background-position:0 0,0 10px,10px -10px,-10px 0;background-color:#0e0e18">
                            {{-- การ์ดตัวอย่างนิ่ง (ซ่อนตอนกำลังเล่นตัวอย่าง) --}}
                            <div x-show="!playing" class="pvcard" :class="'as-' + form.alert_style" style="max-width:100%" :style="cardStyle()">
                                <div class="accent-icon" x-show="form.alert_style !== 'classic'" x-html="accentSvg()"></div>
                                <template x-if="form.show_avatar && avatarUrl">
                                    <img class="avatar" :src="avatarUrl" style="width:56px;height:56px;border-radius:9999px;object-fit:cover;border:3px solid rgba(255,255,255,.55);margin:0 auto 8px;display:block">
                                </template>
                                <div class="body">
                                    <div style="line-height:1.35">
                                        <span style="font-weight:800" :style="donorStyle()">{{ $streamer->display_name }}</span>
                                        <span> ໂດເນດ </span>
                                        <span class="pv-amount" style="font-weight:800" :style="amountStyle()">50,000 {{ $curLabel }}</span>
                                    </div>
                                    <div class="pv-message" style="margin-top:6px;font-size:.8em;opacity:.9;font-weight:500" :style="messageStyle()">ເປັນກຳລັງໃຈໃຫ້ ສູ້ໆ!</div>
                                </div>
                            </div>
                            {{-- เวทีเล่นแอนิเมชันจริงตอนกดทดสอบ --}}
                            <div x-ref="stage" class="pointer-events-none absolute inset-0 grid place-items-center p-4"></div>
                        </div>
                        <p class="mt-2 text-center text-[11px] text-slate-400">ປັບຄ່າທາງຊ້າຍ ຕົວຢ່າງຈະອັບເດດທັນທີ</p>
                    </div>

                    {{-- ปุ่มเล่นตัวอย่าง overlay จริง (ในหน้านี้เลย) --}}
                    <div class="nl-card p-5">
                        <h3 class="inline-flex items-center gap-1.5 text-sm font-semibold">{!! $oicon('play', 'h-4 w-4') !!} ຫຼິ້ນຕົວຢ່າງ overlay</h3>
                        <p class="mt-1 text-xs text-slate-400">ກົດແລ້ວ overlay ຈະສະແດງໃນກອບດ້ານເທິງ (ພ້ອມອະນິເມຊັນ + ສຽງ + ອ່ານພາສາລາວ)</p>
                        <div class="mt-3 grid grid-cols-3 gap-2">
                            <button type="button" @click="playInline(10000)"  class="nl-btn-ghost text-sm" :disabled="playing">10,000 ₭</button>
                            <button type="button" @click="playInline(50000)"  class="nl-btn-ghost text-sm" :disabled="playing">50,000 ₭</button>
                            <button type="button" @click="playInline(100000)" class="nl-btn-ghost text-sm" :disabled="playing">100,000 ₭</button>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <input type="number" min="1000" step="1000" x-model="customAmount" class="nl-input text-sm" placeholder="ຈຳນວນເອງ">
                            <button type="button" @click="playInline(customAmount)" class="nl-btn-primary inline-flex shrink-0 items-center gap-1.5 text-sm" :disabled="playing">{!! $oicon('play', 'h-3.5 w-3.5') !!} ຫຼິ້ນ</button>
                        </div>

                        <div class="my-3 border-t border-slate-200 dark:border-white/10"></div>

                        <button type="button" @click="sendTest(customAmount)" class="nl-btn-ghost inline-flex w-full items-center justify-center gap-1.5 text-sm">{!! $oicon('monitor', 'h-4 w-4') !!} ສົ່ງໄປຍັງ OBS overlay ຈິງ</button>
                        <p x-show="testResult" x-cloak x-text="testResult" class="mt-2 text-center text-xs text-emerald-500"></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function overlayConfig() {
        return {
            tab: 'theme',
            themes: @json($themes),
            voices: [],
            speaking: false,
            playing: false,
            ttsSource: '',
            customAmount: 50000,
            testResult: '',
            streamerName: @json($streamer->display_name),
            currencyLabel: @json($curLabel),
            avatarUrl: @json($streamer->avatarUrl()),
            // ตัวเลือกเสียง: '' = ไม่ใช้, 'p:<key>' = เสียงระบบ, 'm:<id>' = ไฟล์อัปโหลด
            soundChoice: @json($s->sound_media_id ? ('m:'.$s->sound_media_id) : ($s->sound_preset ? ('p:'.$s->sound_preset) : '')),
            soundBase: @json(url('sounds')),
            // แผนที่ id → url ของไฟล์เสียง / รูป (ไว้เล่นตัวอย่างในหน้านี้)
            soundUrls: @json($sounds->mapWithKeys(fn ($m) => [$m->id => $m->url()])),
            imageUrls: @json($images->mapWithKeys(fn ($m) => [$m->id => ['url' => $m->url(), 'video' => \Illuminate\Support\Str::endsWith($m->path, '.webm')]])),
            form: {
                theme: @json(old('theme', $s->theme)),
                alert_style: @json(old('alert_style', $s->alert_style)),
                font_family: @json(old('font_family', $s->font_family)),
                font_size: {{ (int) old('font_size', $s->font_size) }},
                font_weight: @json((string) old('font_weight', $s->font_weight)),
                font_color: @json(old('font_color', $s->font_color)),
                background_color: @json(old('background_color', $s->background_color)),
                border_radius: {{ (int) old('border_radius', $s->border_radius) }},
                width: {{ (int) old('width', $s->width) }},
                height: @json(old('height', $s->height)),
                position: @json(old('position', $s->position)),
                shadow: {{ old('shadow', $s->shadow) ? 'true' : 'false' }},
                show_avatar: {{ old('show_avatar', $s->show_avatar) ? 'true' : 'false' }},
                animation: @json(old('animation', $s->animation)),
                animation_duration: {{ (int) old('animation_duration', $s->animation_duration) }},
                display_duration: {{ (int) old('display_duration', $s->display_duration) }},
                min_amount_to_show: @json(old('min_amount_to_show', $s->min_amount_to_show)),
                image_media_id: @json((string) old('image_media_id', $s->image_media_id)),
                sound_enabled: {{ old('sound_enabled', $s->sound_enabled) ? 'true' : 'false' }},
                sound_volume: {{ (int) old('sound_volume', $s->sound_volume) }},
                sound_delay: {{ (int) old('sound_delay', $s->sound_delay) }},
                tts_enabled: {{ old('tts_enabled', $s->tts_enabled) ? 'true' : 'false' }},
                tts_template: @json(old('tts_template', $s->tts_template ?: config('newlab.tts.default_template'))),
                tts_voice: @json(old('tts_voice', $s->tts_voice)),
                tts_rate: {{ (float) old('tts_rate', $s->tts_rate) }},
                tts_pitch: {{ (float) old('tts_pitch', $s->tts_pitch) }},
                tts_volume: {{ (int) old('tts_volume', $s->tts_volume) }},
            },

            init() {
                this.loadVoices();
                if ('speechSynthesis' in window) speechSynthesis.onvoiceschanged = () => this.loadVoices();
            },

            // ---------- Live Preview (การ์ดนิ่ง) ----------
            cardStyle() {
                const f = this.form;
                const t = this.themes[f.theme] || {};
                const isCustom = f.theme === 'custom';
                const style = {
                    width: f.width + 'px', fontFamily: f.font_family, fontSize: f.font_size + 'px',
                    fontWeight: f.font_weight, borderRadius: f.border_radius + 'px', padding: '20px 24px', margin: '0 auto',
                };
                if (f.height) style.minHeight = f.height + 'px';
                if (isCustom) {
                    style.background = f.background_color; style.color = f.font_color;
                    if (f.shadow) style.boxShadow = '0 20px 50px rgba(0,0,0,.45)';
                } else {
                    style.background = t.background; style.color = f.font_color || '#fff';
                    if (t.border) style.border = t.border;
                    if (f.shadow && t.glow) style.boxShadow = t.glow;
                }
                style['--accent'] = isCustom ? (f.font_color || '#fff') : (t.name_color || f.font_color || '#fff');
                return style;
            },
            // ไอคอน SVG กล่องของขวัญ (ชุดเดียวกับ renderer) สำหรับสไตล์ banner/pill/hero/side-accent
            accentSvg() {
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:100%;height:100%"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M4 12v7a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-7"/><path d="M12 8S10.5 4 8 4a2 2 0 0 0 0 4h4zM12 8s1.5-4 4-4a2 2 0 0 1 0 4h-4z"/></svg>';
            },
            donorStyle()   { const t = this.themes[this.form.theme] || {}; return this.form.theme === 'custom' ? {} : { color: t.name_color || '' }; },
            amountStyle()  { const t = this.themes[this.form.theme] || {}; return this.form.theme === 'custom' ? {} : { color: t.amount_color || '' }; },
            messageStyle() { const t = this.themes[this.form.theme] || {}; return this.form.theme === 'custom' ? {} : { color: t.message_color || '' }; },
            previewSwatch(key) {
                const t = this.themes[key] || {};
                if (key === 'custom') return { background: 'repeating-linear-gradient(45deg,#334155,#334155 6px,#475569 6px,#475569 12px)' };
                return { background: t.background, border: t.border, boxShadow: t.glow };
            },

            fmt(n) { return Number(n || 0).toLocaleString('en-US'); },

            // ---------- เล่น overlay จริงในหน้านี้ ----------
            buildCardEl(amount) {
                const f = this.form;
                const t = this.themes[f.theme] || {};
                const isCustom = f.theme === 'custom';

                const card = document.createElement('div');
                card.className = 'pvcard as-' + f.alert_style;
                card.style.cssText = 'padding:20px 24px;max-width:100%;overflow:hidden';
                card.style.width = Math.min(f.width, 520) + 'px';
                card.style.fontFamily = f.font_family;
                card.style.fontSize = f.font_size + 'px';
                card.style.fontWeight = f.font_weight;
                card.style.borderRadius = f.border_radius + 'px';
                if (f.height) card.style.minHeight = f.height + 'px';
                if (isCustom) {
                    card.style.background = f.background_color; card.style.color = f.font_color;
                    if (f.shadow) card.style.boxShadow = '0 20px 50px rgba(0,0,0,.45)';
                } else {
                    card.style.background = t.background; card.style.color = f.font_color || '#fff';
                    if (t.border) card.style.border = t.border;
                    if (f.shadow && t.glow) card.style.boxShadow = t.glow;
                }
                card.style.setProperty('--accent', isCustom ? (f.font_color || '#fff') : (t.name_color || f.font_color || '#fff'));

                // ไอคอน SVG ประดับ (ทุกสไตล์ยกเว้น classic)
                if (f.alert_style !== 'classic') {
                    const acc = document.createElement('div');
                    acc.className = 'accent-icon';
                    acc.innerHTML = this.accentSvg();
                    card.appendChild(acc);
                }
                // รูปโปรไฟล์
                if (f.show_avatar && this.avatarUrl) {
                    const img = document.createElement('img');
                    img.className = 'avatar';
                    img.src = this.avatarUrl;
                    img.style.cssText = 'width:64px;height:64px;border-radius:9999px;object-fit:cover;margin:0 auto 12px;display:block;border:3px solid rgba(255,255,255,.55)';
                    card.appendChild(img);
                }
                // รูป/วิดีโอแจ้งเตือน (เฉพาะสไตล์ classic/hero ที่มีพื้นที่แนวตั้ง)
                const media = this.imageUrls[f.image_media_id];
                if (media && (f.alert_style === 'classic' || f.alert_style === 'hero')) {
                    const el = media.video ? document.createElement('video') : document.createElement('img');
                    el.src = media.url;
                    if (media.video) { el.autoplay = true; el.muted = true; el.playsInline = true; }
                    el.style.cssText = 'display:block;max-width:100%;margin:0 auto 14px;max-height:160px;border-radius:12px';
                    card.appendChild(el);
                }

                const donorColor = isCustom ? '' : (t.name_color || '');
                const amountColor = isCustom ? '' : (t.amount_color || '');
                const body = document.createElement('div');
                body.className = 'body';
                const head = document.createElement('div');
                head.style.lineHeight = '1.35';
                head.innerHTML = '<span style="font-weight:800;' + (donorColor ? 'color:' + donorColor : '') + '">' +
                    this.esc(this.streamerName) + '</span> ໂດເນດ <span class="pv-amount" style="font-weight:800;' + (amountColor ? 'color:' + amountColor : '') + '">' +
                    this.fmt(amount) + ' ' + this.esc(this.currencyLabel) + '</span>';
                body.appendChild(head);

                const msg = document.createElement('div');
                msg.className = 'pv-message';
                msg.style.cssText = 'margin-top:6px;font-size:.8em;opacity:.92;font-weight:500';
                if (!isCustom && t.message_color) msg.style.color = t.message_color;
                msg.textContent = 'ເປັນກຳລັງໃຈໃຫ້ ສູ້ໆ!';
                body.appendChild(msg);

                card.appendChild(body);
                return card;
            },
            esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; },
            animateEl(el, name, dir, dur) {
                return new Promise((resolve) => {
                    const map = {
                        'fade':        ['nl-fade-in', 'nl-fade-out'],
                        'zoom':        ['nl-zoom-in', 'nl-zoom-out'],
                        'slide':       ['nl-slide-in', 'nl-slide-out'],
                        'slide-left':  ['nl-slideleft-in', 'nl-slideleft-out'],
                        'slide-right': ['nl-slideright-in', 'nl-slideright-out'],
                        'bounce':      ['nl-bounce-in', 'nl-fade-out'],
                        'pop':         ['nl-pop-in', 'nl-pop-out'],
                        'flip':        ['nl-flip-in', 'nl-flip-out'],
                        'glitch':      ['nl-glitch-in', 'nl-fade-out'],
                    };
                    const pair = map[name] || map.fade;
                    el.style.animation = (dir === 'in' ? pair[0] : pair[1]) + ' ' + dur + 'ms ease both';
                    setTimeout(resolve, dur);
                });
            },
            async playInline(amount) {
                if (this.playing) return;
                this.playing = true;
                const stage = this.$refs.stage;
                stage.innerHTML = '';
                const card = this.buildCardEl(amount);
                stage.appendChild(card);

                const dur = this.form.animation_duration || 500;
                await this.animateEl(card, this.form.animation, 'in', dur);

                // เสียง + อ่านไทย พร้อมกับเวลาแสดง
                this.playSound();
                this.ttsSpeak(this.ttsPreviewAmount(amount));
                await new Promise(r => setTimeout(r, this.form.display_duration || 8000));

                await this.animateEl(card, this.form.animation, 'out', dur);
                stage.innerHTML = '';
                this.playing = false;
            },
            // หา URL ของเสียงที่เลือก (เสียงระบบ preset หรือไฟล์อัปโหลด)
            currentSoundUrl() {
                const c = this.soundChoice || '';
                if (c.startsWith('p:')) return this.soundBase + '/' + c.slice(2) + '.wav';
                if (c.startsWith('m:')) return this.soundUrls[c.slice(2)] || null;
                return null;
            },
            playSound() {
                if (!this.form.sound_enabled) return;
                const url = this.currentSoundUrl();
                if (!url) return;
                setTimeout(() => {
                    try {
                        const a = new Audio(url);
                        a.volume = Math.min(1, Math.max(0, (this.form.sound_volume || 80) / 100));
                        a.play().catch(() => {});
                    } catch (e) {}
                }, this.form.sound_delay || 0);
            },
            previewSound() {
                const url = this.currentSoundUrl();
                if (!url) return;
                try { const a = new Audio(url); a.volume = (this.form.sound_volume || 80) / 100; a.play().catch(() => {}); } catch (e) {}
            },

            // ---------- TTS ภาษาไทย ----------
            ttsPreview() { return this.ttsPreviewAmount(100); },
            ttsPreviewAmount(amount) {
                const tpl = this.form.tts_template || @json(config('newlab.tts.default_template'));
                return tpl.replaceAll('{donor_name}', this.streamerName)
                          .replaceAll('{amount}', this.fmt(amount))
                          .replaceAll('{currency}', this.currencyLabel)
                          .replaceAll('{message}', 'ເປັນກຳລັງໃຈໃຫ້')
                          .replaceAll('{streamer_name}', this.streamerName)
                          .trim();
            },
            insertVar(v) { this.form.tts_template = (this.form.tts_template || '') + v; this.$refs.ttsTemplate?.focus(); },

            // คัดเฉพาะ "เสียงไทย" — เรียงเสียงคุณภาพสูง (Natural/Online/Neural) ขึ้นก่อน
            loadVoices() {
                if (!('speechSynthesis' in window)) { this.voices = []; return; }
                const lao = speechSynthesis.getVoices().filter(v => (v.lang || '').toLowerCase().startsWith('lo'));
                const isHi = v => /natural|online|neural|enhanced|premium|keomany|chanthavong|google/i.test(v.name);
                lao.sort((a, b) => (isHi(a) ? 0 : 1) - (isHi(b) ? 0 : 1));
                this.voices = lao.map(v => ({
                    name: v.name,
                    label: (isHi(v) ? '⭐ ' : '') + v.name.replace('Microsoft ', '').replace(/ ?- ?Lao \(Laos\)/i, '').replace('Online ', ''),
                }));
            },
            // Find a Lao voice installed in the browser.
            nativeLaoVoice() {
                const all = ('speechSynthesis' in window) ? speechSynthesis.getVoices() : [];
                if (this.form.tts_voice) {
                    const picked = all.find(v => v.name === this.form.tts_voice);
                    if (picked) return picked;
                }
                return all.find(v => (v.lang || '').toLowerCase().startsWith('lo')) || null;
            },
            // ตั้งคาแรกเตอร์สำเร็จรูป (เสียง + ความเร็ว + โทน)
            applyPreset(voice, rate, pitch) {
                this.form.tts_voice = voice;
                this.form.tts_rate = rate;
                this.form.tts_pitch = pitch;
            },
            // อ่านข้อความ: เสียง Edge → เซิร์ฟเวอร์ / เสียงในเครื่อง → speechSynthesis / อื่นๆ → เซิร์ฟเวอร์ (Google สำรอง)
            ttsSpeak(text, onDone, force) {
                onDone = onDone || (() => {});
                if (!force && !this.form.tts_enabled) { onDone(); return; }
                if (this.form.tts_voice && this.form.tts_voice.indexOf('edge:') === 0) {
                    this.serverTts(text, this.form.tts_voice, onDone);
                    return;
                }
                const voice = this.nativeLaoVoice();
                if (voice && 'speechSynthesis' in window) {
                    speechSynthesis.cancel();
                    const u = new SpeechSynthesisUtterance(text);
                    u.voice = voice; u.lang = 'lo-LA';
                    u.rate = Number(this.form.tts_rate) || 1;
                    u.pitch = Number(this.form.tts_pitch) || 1;
                    u.volume = (Number(this.form.tts_volume) || 100) / 100;
                    u.onend = onDone; u.onerror = onDone;
                    speechSynthesis.speak(u);
                } else {
                    this.serverTts(text, '', onDone);
                }
            },
            // เสียงผ่านเซิร์ฟเวอร์: Edge neural หรือ Google สำรอง (อ่าน X-TTS-Source เพื่อบอกที่มา)
            serverTts(text, voice, onDone) {
                onDone = onDone || (() => {});
                const isEdge = voice && voice.indexOf('edge:') === 0;
                const ratePct = Math.max(-100, Math.min(100, Math.round(((Number(this.form.tts_rate) || 1) - 1) * 100)));
                const pitchHz = Math.max(-100, Math.min(100, Math.round(((Number(this.form.tts_pitch) || 1) - 1) * 50)));
                const url = @js(route('tts.speak')) + '?q=' + encodeURIComponent(text)
                    + (voice ? '&voice=' + encodeURIComponent(voice) : '')
                    + '&rate=' + ratePct + '&pitch=' + pitchHz;
                fetch(url).then(async (res) => {
                    this.ttsSource = res.headers.get('X-TTS-Source') || (isEdge ? 'edge' : 'google');
                    const blob = await res.blob();
                    const a = new Audio(URL.createObjectURL(blob));
                    a.volume = (Number(this.form.tts_volume) || 100) / 100;
                    if (this.ttsSource !== 'edge') a.playbackRate = Math.min(2, Math.max(0.5, Number(this.form.tts_rate) || 1));
                    let done = false; const fin = () => { if (!done) { done = true; onDone(); } };
                    a.onended = fin; a.onerror = fin;
                    a.play().catch(fin);
                }).catch(() => onDone());
            },
            testSpeak() {
                this.ttsSource = '';
                this.speaking = true;
                this.ttsSpeak(this.ttsPreview(), () => this.speaking = false, true);
            },

            // ---------- ส่งไปยัง OBS overlay จริง ----------
            async sendTest(amount) {
                this.testResult = '';
                try {
                    const res = await fetch(@js(route('test-notification.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json', 'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ donor_name: this.streamerName, amount: amount, message: 'ເປັນກຳລັງໃຈໃຫ້ ສູ້ໆ!' }),
                    });
                    const data = await res.json();
                    this.testResult = data.message || (res.ok ? '✓ ສົ່ງແລ້ວ! ເບິ່ງທີ່ OBS' : 'ສົ່ງບໍ່ສຳເລັດ');
                } catch (e) { this.testResult = 'ສົ່ງບໍ່ສຳເລັດ'; }
                setTimeout(() => this.testResult = '', 4000);
            },
        };
    }
</script>
@endsection
