@extends('layouts.app')
@section('title', 'ຕັ້ງຄ່າເປົ້າໝາຍໂດເນດ (Donation Goal Widget)')

@section('content')
<style>
    /* CSS Animations for Themes & Progress Styles */
    @keyframes wave-animation {
        0% { background-position-x: 0px; }
        100% { background-position-x: 400px; }
    }
    @keyframes shine-animation {
        0% { transform: translateX(-100%) rotate(15deg); }
        100% { transform: translateX(100%) rotate(15deg); }
    }
    @keyframes lava-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes rainbow-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes space-animation {
        0% { background-position: 0% 0%; }
        100% { background-position: 100% 100%; }
    }
    @keyframes pulse-effect {
        0%, 100% { transform: scale(1); box-shadow: 0 0 10px rgba(var(--brand-rgb), 0.2); }
        50% { transform: scale(1.01); box-shadow: 0 0 20px rgba(var(--brand-rgb), 0.6); }
    }
</style>

<div x-data="goalConfig()" class="space-y-6">

    {{-- ลิงก์ OBS Widget --}}
    <div class="nl-card-glow p-6" x-data="{ copied: false }">
        <div class="flex items-center gap-2">
            <span class="nl-chip">📺 OBS Browser Source (Donation Goal Widget)</span>
        </div>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">ນຳລິ້ງນີ້ໄປວາງໃນ OBS → Sources → Browser (ຂະໜາດແນະນຳ: 800 × 220)</p>
        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
            <input readonly value="{{ $widgetUrl }}" class="nl-input font-mono text-sm" x-ref="url">
            <button type="button" @click="navigator.clipboard.writeText($refs.url.value); copied=true; setTimeout(()=>copied=false,2000)" class="nl-btn-primary shrink-0">
                <span x-show="!copied">📋 ຄັດລອກລິ້ງ</span><span x-show="copied" x-cloak>✓ ຄັດລອກແລ້ວ</span>
            </button>
            <a href="{{ $widgetUrl }}" target="_blank" class="nl-btn-ghost shrink-0">ເປີດເບິ່ງ ↗</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        {{-- ===== คอลัมน์ซ้าย: ฟอร์มตั้งค่า ===== --}}
        <div class="space-y-4 lg:col-span-3">
            {{-- แท็บเมนู --}}
            <div class="flex flex-wrap gap-1.5 rounded-xl border border-slate-200 bg-white p-1.5 dark:border-white/10 dark:bg-slate-900/60">
                @foreach ([
                    'setup' => '⚙️ ຕັ້ງຄ່າເປົ້າໝາຍ',
                    'themes' => '🎨 ແກລເລີຣີທີມ',
                    'customize' => '🛠️ ປັບແຕ່ງສະໄຕລ໌',
                    'celebrate' => '🎉 ສະຫຼອງຄວາມສຳເລັດ',
                ] as $key => $label)
                    <button type="button" @click="tab='{{ $key }}'"
                            class="flex-1 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold transition"
                            :class="tab==='{{ $key }}' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <form method="POST" action="{{ route('donation-goal.update') }}" class="space-y-4">
                @csrf @method('PUT')

                {{-- ซ่อนข้อมูลการตั้งค่าธีมไว้เพื่อส่งผ่าน Form --}}
                <input type="hidden" name="theme_id" :value="form.theme_id">
                <input type="hidden" name="progress_style" :value="form.progress_style">
                <template x-for="eff in form.effects">
                    <input type="hidden" name="effects[]" :value="eff">
                </template>
                <template x-for="fav in form.favorite_themes">
                    <input type="hidden" name="favorite_themes[]" :value="fav">
                </template>

                {{-- ---- แท็บ: ตั้งค่าเป้าหมาย ---- --}}
                <div x-show="tab==='setup'" class="nl-card p-6 space-y-4">
                    <h3 class="font-semibold text-lg">ຂໍ້ມູນເປົ້າໝາຍໂດເນດ</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="nl-label">ຊື່ເປົ້າໝາຍ (Goal Title)</label>
                            <input name="title" x-model="form.title" class="nl-input" placeholder="ຕົວຢ່າງ: ຊື້ໄມໂຄຣໂຟນໃໝ່">
                        </div>
                        <div>
                            <label class="nl-label">ຍອດເປົ້າໝາຍ ({{ $streamer->currency }})</label>
                            <input name="target_amount" type="number" step="0.01" x-model.number="form.target_amount" class="nl-input">
                        </div>
                        <div>
                            <label class="nl-label">ຍອດປັດຈຸບັນເລີ່ມຕົ້ນ ({{ $streamer->currency }})</label>
                            <input name="current_amount" type="number" step="0.01" x-model.number="form.current_amount" class="nl-input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="nl-label">ຂໍ້ຄວາມຂອບໃຈດ້ານລຸ່ມ</label>
                            <input name="bottom_text" x-model="form.bottom_text" class="nl-input" placeholder="ຂອບໃຈທຸກການສະໜັບສະໜູນ! ❤️">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-white/10 space-y-3">
                        <h4 class="font-semibold text-sm">ການສະແດງຂໍ້ມູນໃນວິດເຈັດ</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_title" value="1" x-model="form.show_title" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງຊື່ເປົ້າໝາຍ
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_current" value="1" x-model="form.show_current" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງຍອດປັດຈຸບັນ
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_target" value="1" x-model="form.show_target" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງຍອດເປົ້າໝາຍ
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="show_percentage" value="1" x-model="form.show_percentage" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງເປີເຊັນ (%)
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer col-span-2">
                                <input type="checkbox" name="show_bottom_text" value="1" x-model="form.show_bottom_text" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                ສະແດງຂໍ້ຄວາມຂອບໃຈດ້ານລຸ່ມ
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ---- แท็บ: แกลเลอรีธีม ---- --}}
                <div x-show="tab==='themes'" x-cloak class="nl-card p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="font-semibold text-lg">ແກລເລີຣີເປົ້າໝາຍໂດເນດ</h3>
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="searchTheme" placeholder="ຄົ້ນຫາທີມ..." class="nl-input py-1.5 px-3 text-xs w-44">
                            <select x-model="filterCategory" class="nl-input py-1.5 px-2 text-xs w-32">
                                <option value="all">ທຸກສະໄຕລ໌</option>
                                <option value="gaming">ເກມມິ່ງ / ແຟນຕາຊີ</option>
                                <option value="luxury">ມິນິມອນ / ຫຼູຫຼາ</option>
                                <option value="nature">ທຳມະຊາດ / ສົດໃສ</option>
                                <option value="cute">ໜ້າຮັກ</option>
                                <option value="favorite">ລາຍການມັກ ⭐</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <template x-for="t in filteredThemes()" :key="t.id">
                            <div class="group relative overflow-hidden rounded-xl border border-slate-200 dark:border-white/10 p-3 bg-slate-50 dark:bg-[#121220] flex flex-col justify-between transition-all"
                                 :class="form.theme_id === t.id ? 'ring-2 ring-brand-500 border-transparent' : 'hover:border-brand-400'">
                                
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-lg" x-text="t.icon"></span>
                                        <span class="font-bold text-sm" x-text="t.name"></span>
                                    </div>
                                    <button type="button" @click="toggleFavorite(t.id)" class="text-slate-400 hover:text-amber-500 transition">
                                        <span class="text-base" x-text="isFavorite(t.id) ? '⭐' : '☆'"></span>
                                    </button>
                                </div>

                                {{-- พรีวิวจำลองขนาดเล็ก --}}
                                <div class="h-16 rounded-lg flex items-center justify-center text-xs font-bold text-white relative overflow-hidden shadow-inner"
                                     :style="getThemePreviewStyle(t)">
                                    <div class="absolute inset-0 bg-black/10"></div>
                                    <div class="relative z-10 px-2 text-center">
                                        <span x-text="t.name"></span>
                                        <div class="w-24 bg-white/20 h-2.5 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full bg-white/80" :style="`width: 50%`"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider" x-text="t.categoryLabel"></span>
                                    <button type="button" @click="selectTheme(t)" class="nl-btn py-1 px-3 text-xs bg-brand-600 text-white hover:bg-brand-500 rounded-lg">
                                        ເລືອກໃຊ້ທີມ
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ---- แท็บ: ปรับแต่งสไตล์ ---- --}}
                <div x-show="tab==='customize'" x-cloak class="space-y-4">
                    {{-- เลือก Progress Style & Effects --}}
                    <div class="nl-card p-6 space-y-4">
                        <h3 class="font-semibold text-lg">ສະໄຕລ໌ຫຼອດ ແລະ ເອັບເຟັກ</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="nl-label">ຮູບແບບ Progress Bar</label>
                                <select x-model="form.progress_style" class="nl-input">
                                    <option value="fill">Fill (ສີເຕັມຫຼອດ)</option>
                                    <option value="water">Water (ຄື້ນນ້ຳໄຫຼ)</option>
                                    <option value="wave">Wave (ເສັ້ນຄື້ນເຄື່ອນໄຫວ)</option>
                                    <option value="gradient">Gradient (ໄລ່ສີແບບນຸ່ມນວນ)</option>
                                    <option value="glow">Glow (ນີອອນເຮືອງແສງ)</option>
                                    <option value="shine">Shine (ແສງເງົາເຄື່ອນຜ່ານ)</option>
                                    <option value="rainbow">Rainbow (ສີຮຸ້ງເຄື່ອນທີ່)</option>
                                    <option value="segmented">Segmented (ແບ່ງເປັນຊ່ອງ)</option>
                                    <option value="pixel">Pixel (ພິກເຊວແບບຍ້ອນຍຸກ)</option>
                                </select>
                            </div>
                            <div>
                                <label class="nl-label">ເປີດ/ປິດ ເອັບເຟັກພິເສດ</label>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                        <input type="checkbox" :checked="form.effects.includes('glow')" @change="toggleEffect('glow')" class="h-3.5 w-3.5 text-brand-600 rounded">
                                        ເຮືອງແສງ (Glow)
                                    </label>
                                    <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                        <input type="checkbox" :checked="form.effects.includes('shine')" @change="toggleEffect('shine')" class="h-3.5 w-3.5 text-brand-600 rounded">
                                        ສະທ້ອນແສງ (Shine)
                                    </label>
                                    <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                        <input type="checkbox" :checked="form.effects.includes('pulse')" @change="toggleEffect('pulse')" class="h-3.5 w-3.5 text-brand-600 rounded">
                                        ກະພິບເປັນຈັງຫວະ (Pulse)
                                    </label>
                                    <label class="flex items-center gap-1.5 text-xs cursor-pointer">
                                        <input type="checkbox" :checked="form.effects.includes('sparkle')" @change="toggleEffect('sparkle')" class="h-3.5 w-3.5 text-brand-600 rounded">
                                        ປະກາຍວິບວັບ (Sparkle)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ตั้งค่าธีมกำหนดเอง (Custom Theme) --}}
                    <div class="nl-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-lg">ສະໄຕລ໌ກຳນົດເອງ (Custom Theme)</h3>
                            <button type="button" @click="form.theme_id = 'custom'" class="text-xs text-brand-500 hover:underline">ເປີດໃຊ້ທີມກຳນົດເອງ</button>
                        </div>
                        <p class="text-xs text-slate-400">ສະໄຕລ໌ດ້ານລຸ່ມຈະສະແດງເມື່ອເລືອກທີມ “ກຳນົດເອງ (Custom)”</p>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="nl-label">ສີຫຼັກ (Primary)</label>
                                <input name="primary_color" type="color" x-model="form.primary_color" class="nl-input h-10 p-1">
                            </div>
                            <div>
                                <label class="nl-label">ສີຮອງ (Secondary)</label>
                                <input name="secondary_color" type="color" x-model="form.secondary_color" class="nl-input h-10 p-1">
                            </div>
                            <div>
                                <label class="nl-label">ສີຂໍ້ຄວາມ</label>
                                <input name="font_color" type="color" x-model="form.font_color" class="nl-input h-10 p-1">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="nl-label">ສະໄຕລ໌ພື້ນຫຼັງກາດ (CSS Background ເຊັ່ນ linear-gradient ຫຼື rgba)</label>
                                <input name="background_style" x-model="form.background_style" class="nl-input font-mono text-xs">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="nl-label">ສະໄຕລ໌ຂອບກາດ (CSS Border)</label>
                                <input name="border_style" x-model="form.border_style" class="nl-input font-mono text-xs">
                            </div>
                            <div>
                                <label class="nl-label">ຄວາມໂຄ້ງຂອບກາດ (px)</label>
                                <input name="border_radius" type="number" min="0" max="99" x-model.number="form.border_radius" class="nl-input">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="nl-label">ສະໄຕລ໌ເງົາກາດ (CSS Box-shadow)</label>
                                <input name="shadow_style" x-model="form.shadow_style" class="nl-input font-mono text-xs">
                            </div>
                            <div>
                                <label class="nl-label">ຕະກູນຟອນ (Font Family)</label>
                                <select name="font_family" x-model="form.font_family" class="nl-input">
                                    <option value="'Noto Sans Lao', sans-serif">Noto Sans Lao (ແນະນຳ)</option>
                                    <option value="Kanit, sans-serif">Kanit</option>
                                    <option value="system-ui, sans-serif">System UI</option>
                                    <option value="Sarabun, sans-serif">Sarabun</option>
                                    <option value="Mitr, sans-serif">Mitr</option>
                                </select>
                            </div>
                            <div>
                                <label class="nl-label">ຄວາມໜາຕົວອັກສອນ</label>
                                <select name="font_weight" x-model="form.font_weight" class="nl-input">
                                    @foreach (['300'=>'ບາງ','400'=>'ປົກກະຕິ','500'=>'ປານກາງ','600'=>'ກຶ່ງໜາ','700'=>'ໜາ','800'=>'ໜາຫຼາຍ','900'=>'ໜາສຸດ'] as $w => $lbl)
                                        <option value="{{ $w }}">{{ $lbl }} ({{ $w }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ---- แท็บ: ฉลองเป้าหมายสำเร็จ ---- --}}
                <div x-show="tab==='celebrate'" x-cloak class="nl-card p-6 space-y-4">
                    <h3 class="font-semibold text-lg">ເອັບເຟັກສະຫຼອງເມື່ອບັນລຸເປົ້າໝາຍ</h3>
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="celebration_enabled" value="1" x-model="form.celebration_enabled" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        ເປີດເອັບເຟັກອະນິເມຊັນເມື່ອຍອດຮອດເປົ້າໝາຍ
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2" x-show="form.celebration_enabled">
                        <div>
                            <label class="nl-label">ຮູບແບບເອັບເຟັກຄວາມສຳເລັດ</label>
                            <select name="celebration_type" x-model="form.celebration_type" class="nl-input">
                                <option value="confetti">Confetti (ເຈ້ຍສີປິວ)</option>
                                <option value="firework">Firework (ດອກໄຟ)</option>
                                <option value="golden-shine">Golden Shine (ປະກາຍແສງຄຳ)</option>
                                <option value="sparkle">Sparkle (ປະກາຍດາວວິບວັບ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="nl-label">ຂໍ້ຄວາມສະຫຼອງຄວາມສຳເລັດ</label>
                            <input name="celebration_message" x-model="form.celebration_message" class="nl-input" placeholder="ບັນລຸເປົ້າໝາຍແລ້ວ! 🎉">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="submit" class="nl-btn-primary">
                        💾 ບັນທຶກການຕັ້ງຄ່າທັງໝົດ
                    </button>
                </div>
            </form>
        </div>

        {{-- ===== คอลัมน์ขวา: พรีวิวแบบตอบสนองทันที (Interactive Live Preview) ===== --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="sticky top-20 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-700 dark:text-slate-300">👁️ ຕົວຢ່າງວິດເຈັດສົດ (Live Preview)</h3>
                    <span class="text-xs text-brand-500 bg-brand-50 dark:bg-brand-500/10 px-2 py-0.5 rounded-full font-bold">ອັບເດດທັນທີ</span>
                </div>

                {{-- พื้นหลังจำลอง OBS Stage --}}
                <div class="h-56 rounded-2xl bg-[#09090e] border border-slate-800 flex items-center justify-center p-4 relative overflow-hidden shadow-2xl">
                    <div class="absolute top-2 left-2 flex gap-1">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    </div>
                    <span class="absolute top-2 right-3 text-[10px] font-mono text-slate-600">OBS Stage (Transparent)</span>

                    {{-- CONTAINER WIDGET --}}
                    <div id="preview-widget" 
                         class="w-full max-w-[460px] rounded-xl p-4 transition-all duration-300 relative"
                         :style="getWidgetStyle()"
                         :class="getEffectClasses()">
                         
                         {{-- เอฟเฟกต์ฉลองความสำเร็จ --}}
                         <div x-show="isCelebrationPlaying" class="absolute inset-0 bg-amber-500/10 border-2 border-amber-400 rounded-xl flex items-center justify-center z-30 transition-all">
                             <div class="text-center font-extrabold text-amber-300 animate-bounce">
                                 <span class="block text-sm" x-text="form.celebration_message"></span>
                                 <span class="text-xs font-normal text-white">🎉 GOAL REACHED! 🎉</span>
                             </div>
                         </div>

                         {{-- โครงสร้างด้านบน: ชื่อเป้าหมาย และยอด --}}
                         <div class="flex items-center justify-between mb-3 text-sm font-extrabold relative z-10">
                             <div class="flex items-center gap-1" x-show="form.show_title">
                                 <span x-text="getThemeIcon()"></span>
                                 <span x-text="form.title" :style="getFontColorStyle()"></span>
                             </div>
                             <div class="font-mono text-xs" x-show="form.show_current || form.show_target" :style="getFontColorStyle()">
                                 <span x-show="form.show_current" x-text="formatNumber(form.current_amount)"></span>
                                 <span x-show="form.show_current && form.show_target">/</span>
                                 <span x-show="form.show_target" x-text="formatNumber(form.target_amount)"></span>
                                 <span class="text-brand-400 font-bold" x-text="streamerCurrency"></span>
                             </div>
                         </div>

                         {{-- หลอดความคืบหน้า (Progress Bar) --}}
                         <div class="w-full h-11 bg-black/30 rounded-lg p-1 relative overflow-hidden flex items-center border border-white/5"
                              :style="getProgressBarOuterStyle()">
                              
                              {{-- หลอดสีวิ่งจริง --}}
                              <div class="h-full rounded-md transition-all duration-1000 ease-out relative overflow-hidden"
                                   :style="getProgressBarInnerStyle()">
                                   
                                   {{-- เอฟเฟกต์อนิเมชันสำหรับ Wave / Water --}}
                                   <div class="absolute inset-0 opacity-60" :style="getProgressOverlayStyle()"></div>
                                   
                                   {{-- เอฟเฟกต์ Shine --}}
                                   <div x-show="form.progress_style === 'shine' || form.effects.includes('shine')"
                                        class="absolute inset-y-0 left-0 w-12 bg-white/40 skew-x-12 blur-xs animate-[shine-animation_2s_infinite]"></div>
                              </div>

                              {{-- ตัวเลขเปอร์เซ็นต์ในหลอด --}}
                              <div x-show="form.show_percentage" 
                                   class="absolute inset-0 flex items-center justify-end pr-4 text-xs font-black font-mono select-none pointer-events-none z-10"
                                   :style="getPercentColorStyle()">
                                  <span x-text="getPercentageText()"></span>
                              </div>
                         </div>

                         {{-- ข้อความขอบคุณด้านล่าง --}}
                         <div x-show="form.show_bottom_text" class="mt-2 text-center text-[10px] font-semibold opacity-85 relative z-10" :style="getFontColorStyle()">
                             <span x-text="form.bottom_text"></span>
                         </div>
                    </div>
                </div>

                {{-- ตัวทดสอบยิงโดเนทจำลอง --}}
                <div class="nl-card p-5 space-y-3">
                    <h4 class="font-bold text-sm">🧪 ເຄື່ອງມືຈຳລອງໂດເນດ (Simulator)</h4>
                    <p class="text-xs text-slate-400">ສົ່ງຍອດໂດເນດທົດສອບເພື່ອເບິ່ງຫຼອດເພີ່ມຂຶ້ນ ແລະ ເອັບເຟັກຄວາມສຳເລັດໃນ OBS ແລະ ໜ້ານີ້</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="simulateDonation(1000)" class="nl-btn-ghost py-2 text-xs rounded-lg">+ 1,000</button>
                        <button type="button" @click="simulateDonation(5000)" class="nl-btn-ghost py-2 text-xs rounded-lg">+ 5,000</button>
                        <button type="button" @click="simulateDonation(20000)" class="nl-btn-ghost py-2 text-xs rounded-lg">+ 20,000</button>
                    </div>
                    <form method="POST" action="{{ route('donation-goal.test-update') }}" class="flex gap-2 pt-1">
                        @csrf
                        <input name="amount" type="number" value="50000" min="1000" step="1000" class="nl-input py-1.5 px-3 text-xs" placeholder="ຈຳນວນເງິນ">
                        <button type="submit" class="nl-btn-primary py-1.5 px-4 text-xs whitespace-nowrap rounded-lg">ສົ່ງຍອດເຂົ້າລະບົບ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function goalConfig() {
        return {
            tab: 'setup',
            searchTheme: '',
            filterCategory: 'all',
            isCelebrationPlaying: false,
            streamerCurrency: @json($streamer->currency),
            
            // ข้อมูลฟอร์มหลัก
            form: {
                title: @json($goal->title),
                target_amount: {{ (float) $goal->target_amount }},
                current_amount: {{ (float) $goal->current_amount }},
                bottom_text: @json($goal->bottom_text),
                theme_id: @json($goal->theme_id),
                progress_style: @json($goal->progress_style),
                effects: @json($goal->effects ?? []),
                favorite_themes: @json($goal->favorite_themes ?? []),
                show_title: {{ $goal->show_title ? 'true' : 'false' }},
                show_current: {{ $goal->show_current ? 'true' : 'false' }},
                show_target: {{ $goal->show_target ? 'true' : 'false' }},
                show_percentage: {{ $goal->show_percentage ? 'true' : 'false' }},
                show_bottom_text: {{ $goal->show_bottom_text ? 'true' : 'false' }},
                celebration_enabled: {{ $goal->celebration_enabled ? 'true' : 'false' }},
                celebration_type: @json($goal->celebration_type),
                celebration_message: @json($goal->celebration_message),
                
                // Custom
                primary_color: @json($goal->primary_color),
                secondary_color: @json($goal->secondary_color),
                background_style: @json($goal->background_style),
                border_style: @json($goal->border_style),
                border_radius: {{ (int) $goal->border_radius }},
                shadow_style: @json($goal->shadow_style),
                font_family: @json($goal->font_family),
                font_weight: @json($goal->font_weight),
                font_color: @json($goal->font_color),
            },

            // แฟ้มธีมทั้งหมด (ไม่มี background)
            themes: [
                { id: 'water-flow', name: 'ນ້ຳໄຫຼ (Water Flow)', icon: '💧', category: 'nature', border: '2px solid #00bcff', name_color: '#00d2ff', amount_color: '#ffffff', bar_bg: '#1b3b5a', bar_fill: 'linear-gradient(90deg, #0055ff 0%, #00d5ff 100%)' },
                { id: 'glassmorphism', name: 'ແກ້ວໃສ (Glassmorphism)', icon: '🎙️', category: 'luxury', border: '1px solid rgba(255, 255, 255, 0.15)', name_color: '#e8dbff', amount_color: '#ffffff', bar_bg: 'rgba(255,255,255,0.08)', bar_fill: 'linear-gradient(90deg, #812cff 0%, #d178ff 100%)' },
                { id: 'golden', name: 'ຄຳ (Golden)', icon: '👑', category: 'luxury', border: '3px double #f3a80f', name_color: '#ffd043', amount_color: '#ffffff', bar_bg: '#332007', bar_fill: 'linear-gradient(90deg, #d38d06 0%, #ffd659 100%)' },
                { id: 'gaming-neon', name: 'ເກມມິ່ງ (Gaming Neon)', icon: '🎮', category: 'gaming', border: '2px solid #00ff22', name_color: '#00ff3c', amount_color: '#ffffff', bar_bg: '#122412', bar_fill: 'linear-gradient(90deg, #007711 0%, #00ff3c 100%)' },
                { id: 'space', name: 'ອະວະກາດ (Space)', icon: '🚀', category: 'gaming', border: '1px solid #7000ff', name_color: '#d6bfff', amount_color: '#ffffff', bar_bg: '#150a28', bar_fill: 'linear-gradient(90deg, #8b00ff 0%, #ff00ff 100%)' },
                { id: 'cute-pastel', name: 'ໜ້າຮັກ (Cute Pastel)', icon: '💗', category: 'cute', border: '2px solid #ffb2c2', name_color: '#e75376', amount_color: '#555555', bar_bg: '#ffeef0', bar_fill: 'linear-gradient(90deg, #ff7ba0 0%, #ffa5be 100%)' },
                { id: 'lava', name: 'ລາວາ (Lava)', icon: '🔥', category: 'nature', border: '2px solid #ff4400', name_color: '#ff6600', amount_color: '#ffffff', bar_bg: '#30100a', bar_fill: 'linear-gradient(90deg, #e5320d 0%, #ff9500 100%)' },
                { id: 'minimal', name: 'ມິນິມອນ (Minimal)', icon: '🤍', category: 'luxury', border: '1px solid #e2e8f0', name_color: '#1e293b', amount_color: '#1e293b', bar_bg: '#f1f5f9', bar_fill: 'linear-gradient(90deg, #0284c7 0%, #06b6d4 100%)' },
                { id: 'nature', name: 'ທຳມະຊາດ (Nature)', icon: '🌿', category: 'nature', border: '2px solid #00d215', name_color: '#00ff3c', amount_color: '#ffffff', bar_bg: '#112211', bar_fill: 'linear-gradient(90deg, #008811 0%, #44cc44 100%)' }
            ],

            filteredThemes() {
                return this.themes.filter(t => {
                    const matchSearch = t.name.toLowerCase().includes(this.searchTheme.toLowerCase());
                    let matchCategory = true;
                    if (this.filterCategory === 'favorite') {
                        matchCategory = this.isFavorite(t.id);
                    } else if (this.filterCategory !== 'all') {
                        matchCategory = t.category === this.filterCategory;
                    }
                    return matchSearch && matchCategory;
                });
            },

            selectTheme(theme) {
                this.form.theme_id = theme.id;
                this.form.primary_color = theme.bar_fill.includes('#') ? theme.bar_fill.slice(0, 7) : '#8b5cf6';
                
                // แมปสไตล์ตามธีมต้นฉบับ
                if (theme.id === 'water-flow') this.form.progress_style = 'water';
                else if (theme.id === 'gaming-neon') this.form.progress_style = 'segmented';
                else if (theme.id === 'space') this.form.progress_style = 'rainbow';
                else if (theme.id === 'glassmorphism') this.form.progress_style = 'gradient';
                else if (theme.id === 'golden') this.form.progress_style = 'shine';
                else if (theme.id === 'nature') this.form.progress_style = 'wave';
                else if (theme.id === 'lava') this.form.progress_style = 'pixel';
                else this.form.progress_style = 'fill';
            },

            toggleFavorite(id) {
                if (this.isFavorite(id)) {
                    this.form.favorite_themes = this.form.favorite_themes.filter(t => t !== id);
                } else {
                    this.form.favorite_themes.push(id);
                }
            },

            isFavorite(id) {
                return this.form.favorite_themes.includes(id);
            },

            toggleEffect(name) {
                if (this.form.effects.includes(name)) {
                    this.form.effects = this.form.effects.filter(e => e !== name);
                } else {
                    this.form.effects.push(name);
                }
            },

            simulateDonation(amount) {
                this.form.current_amount += amount;
                if (this.form.current_amount >= this.form.target_amount && this.form.celebration_enabled) {
                    this.isCelebrationPlaying = true;
                    setTimeout(() => {
                        this.isCelebrationPlaying = false;
                    }, 5000);
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat().format(num);
            },

            getPercentageText() {
                if (this.form.target_amount <= 0) return '0%';
                let pct = (this.form.current_amount / this.form.target_amount) * 100;
                return pct.toFixed(1) + '%';
            },

            getThemeIcon() {
                const found = this.themes.find(t => t.id === this.form.theme_id);
                return found ? found.icon : '🎯';
            },

            // คืนค่า CSS ของธีมสำหรับแสดงพรีวิวในหน้าตัวเลือก (ไม่มี background)
            getThemePreviewStyle(t) {
                return `background: transparent; border: ${t.border}; font-family: 'Noto Sans Lao', sans-serif;`;
            },

            // ประกอบสไตล์กล่องการ์ดพรีวิวของวิดเจ็ต (ไม่มี background)
            getWidgetStyle() {
                if (this.form.theme_id === 'custom') {
                    return `
                        background: transparent;
                        border: ${this.form.border_style};
                        border-radius: ${this.form.border_radius}px;
                        box-shadow: ${this.form.shadow_style};
                        font-family: ${this.form.font_family};
                    `;
                }

                const theme = this.themes.find(t => t.id === this.form.theme_id) || this.themes[7]; // default minimal
                let borderRad = this.form.border_radius;
                if (this.form.theme_id === 'minimal') borderRad = 8;
                if (this.form.theme_id === 'glassmorphism') borderRad = 20;

                return `
                    background: transparent;
                    border: ${theme.border};
                    border-radius: ${borderRad}px;
                    font-family: 'Noto Sans Lao', sans-serif;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                `;
            },

            getFontColorStyle() {
                if (this.form.theme_id === 'custom') {
                    return `color: ${this.form.font_color}; font-weight: ${this.form.font_weight};`;
                }
                const theme = this.themes.find(t => t.id === this.form.theme_id) || this.themes[7];
                return `color: ${theme.name_color};`;
            },

            getPercentColorStyle() {
                if (this.form.theme_id === 'custom') {
                    return `color: ${this.form.font_color};`;
                }
                if (this.form.theme_id === 'cute-pastel' || this.form.theme_id === 'minimal') {
                    return 'color: #ffffff; text-shadow: 0 1px 2px rgba(0,0,0,0.4);';
                }
                return 'color: #ffffff; text-shadow: 0 0 8px rgba(0,0,0,0.8);';
            },

            getProgressBarOuterStyle() {
                const theme = this.themes.find(t => t.id === this.form.theme_id);
                if (this.form.theme_id === 'custom') {
                    return `background: rgba(0,0,0,0.25);`;
                }
                return `background: ${theme ? theme.bar_bg : 'rgba(0,0,0,0.2)'};`;
            },

            getProgressBarInnerStyle() {
                if (this.form.target_amount <= 0) return 'width: 0%';
                let pct = Math.min(100, (this.form.current_amount / this.form.target_amount) * 100);
                
                let fill = 'linear-gradient(90deg, var(--color-brand-500) 0%, var(--color-brand-600) 100%)';
                if (this.form.theme_id === 'custom') {
                    fill = `linear-gradient(90deg, ${this.form.primary_color} 0%, ${this.form.secondary_color} 100%)`;
                } else {
                    const theme = this.themes.find(t => t.id === this.form.theme_id);
                    if (theme) fill = theme.bar_fill;
                }

                // เอฟเฟกต์สีสำหรับสายรุ้ง (rainbow)
                if (this.form.progress_style === 'rainbow') {
                    fill = 'linear-gradient(90deg, #ff0000, #ff7f00, #ffff00, #00ff00, #0000ff, #4b0082, #8b00ff)';
                }

                return `
                    width: ${pct}%;
                    background: ${fill};
                    box-shadow: ${this.form.effects.includes('glow') || this.form.progress_style === 'glow' ? '0 0 12px ' + (this.form.primary_color || '#00d2ff') : 'none'};
                `;
            },

            getProgressOverlayStyle() {
                // เอฟเฟกต์แอนิเมชันสำหรับ Water หรือ Wave
                if (this.form.progress_style === 'water') {
                    return `
                        background: radial-gradient(circle at 50% 120%, rgba(255,255,255,0.4) 0%, transparent 80%);
                        background-size: 20px 20px;
                        animation: wave-animation 3s linear infinite;
                    `;
                }
                if (this.form.progress_style === 'wave') {
                    return `
                        background: repeating-linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.1) 10px, transparent 10px, transparent 20px);
                        background-size: 40px 40px;
                        animation: wave-animation 4s linear infinite;
                    `;
                }
                if (this.form.progress_style === 'pixel') {
                    return `
                        background-image: linear-gradient(rgba(255,255,255,0.15) 50%, transparent 50%), linear-gradient(90deg, rgba(255,255,255,0.15) 50%, transparent 50%);
                        background-size: 4px 4px;
                    `;
                }
                if (this.form.progress_style === 'segmented') {
                    return `
                        background: repeating-linear-gradient(90deg, transparent, transparent 18px, rgba(0,0,0,0.3) 18px, rgba(0,0,0,0.3) 22px);
                    `;
                }
                return '';
            },

            getEffectClasses() {
                return {
                    'animate-[pulse-effect_1.8s_infinite]': this.form.effects.includes('pulse'),
                };
            }
        }
    }
</script>
@endpush
@endsection
