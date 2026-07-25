@extends('layouts.public')

@section('content')
@php
    $hero = $blocks->firstWhere('type', 'hero');
    $features = $blocks->where('type', 'feature')->take(6);
    $ctas = $blocks->where('type', 'cta');
    $texts = $blocks->where('type', 'richtext');
@endphp

<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-brand-500/10 via-transparent to-transparent" aria-hidden="true"></div>
    <div class="absolute -right-40 top-12 h-96 w-96 rounded-full bg-sky-500/15 blur-3xl" aria-hidden="true"></div>
    <div class="mx-auto grid min-h-[760px] max-w-7xl items-center gap-14 px-6 py-16 lg:grid-cols-[.9fr_1.1fr] lg:py-20">
        <div class="nl-hero-copy relative z-10">
            <div class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-500/10 px-3 py-1.5 text-xs font-extrabold text-brand-600 dark:text-brand-300">
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                </span>
                LAOS CREATOR DONATION PLATFORM
            </div>
            <h1 class="mt-7 max-w-3xl text-5xl font-black leading-[1.08] tracking-[-0.035em] sm:text-6xl lg:text-7xl">
                {{ $hero?->heading ?: 'ສູນກາງໂດເນດສຳລັບຄຣີເອເຕີລາວ' }}
            </h1>
            <p class="mt-6 max-w-xl text-base leading-8 text-slate-600 sm:text-lg dark:text-slate-300">
                {{ $hero?->subheading ?: 'ສ້າງໜ້າໂດເນດ, ແຈ້ງເຕືອນເຂົ້າ OBS ແລະ ຈັດການລາຍຮັບເປັນເງິນກີບໃນລະບົບດຽວ.' }}
            </p>
            <div x-data="{ active: 0, items: ['ໂດເນດຜ່ານ Lao QR', 'ກະເປົາ ແລະ ຖອນເງິນ', 'Overlay ແຈ້ງເຕືອນ OBS', 'TTS ສຽງພາສາລາວ'] }"
                 x-init="setInterval(() => active = (active + 1) % items.length, 2600)"
                 class="relative mt-5 flex h-8 items-center gap-2 overflow-hidden text-sm font-black sm:text-base"
                 aria-live="polite">
                <span class="shrink-0 text-slate-400">ຄົບທຸກຢ່າງ:</span>
                <template x-for="(item, index) in items" :key="item">
                    <span x-show="active === index"
                          x-transition:enter="transition duration-500 ease-out"
                          x-transition:enter-start="translate-y-5 opacity-0"
                          x-transition:enter-end="translate-y-0 opacity-100"
                          x-transition:leave="absolute transition duration-300 ease-in"
                          x-transition:leave-start="translate-y-0 opacity-100"
                          x-transition:leave-end="-translate-y-5 opacity-0"
                          class="nl-gradient-text" x-text="item"></span>
                </template>
            </div>
            <div class="mt-9 flex flex-wrap gap-3">
                @guest
                    <a href="{{ $hero?->link_url ?: route('register') }}" class="nl-btn-primary px-7 py-3.5 text-base">
                        {{ $hero?->link_label ?: 'ສະໝັກໃຊ້ງານຟຣີ' }}
                    </a>
                    <a href="#product-preview" class="nl-btn-ghost px-7 py-3.5 text-base">ເບິ່ງ Product Preview ↓</a>
                @else
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="nl-btn-primary px-7 py-3.5 text-base">ເຂົ້າແດຊບອດ</a>
                    <a href="#product-preview" class="nl-btn-ghost px-7 py-3.5 text-base">ເບິ່ງ Product Preview ↓</a>
                @endguest
            </div>
            <div class="mt-8 grid max-w-lg grid-cols-3 gap-3">
                <div>
                    <p class="text-xl font-black text-brand-500">LAK</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">ຮັບເງິນກີບລາວ</p>
                </div>
                <div class="border-l border-slate-200 pl-4 dark:border-white/10">
                    <p class="text-xl font-black text-brand-500">LIVE</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">ແຈ້ງເຕືອນທັນທີ</p>
                </div>
                <div class="border-l border-slate-200 pl-4 dark:border-white/10">
                    <p class="text-xl font-black text-brand-500">ລາວ</p>
                    <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">ເວັບ ແລະ ສຽງອ່ານ</p>
                </div>
            </div>
        </div>

        <div class="relative mx-auto w-full max-w-3xl lg:mx-0">
            <div class="absolute -inset-8 rounded-full bg-brand-500/20 blur-3xl" aria-hidden="true"></div>
            <div class="nl-product-float relative rounded-[2rem] border border-white/15 bg-slate-950 p-3 shadow-2xl shadow-brand-950/40 sm:p-4">
                <div class="flex items-center gap-2 px-2 pb-3">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                    <div class="ml-3 flex-1 rounded-lg bg-white/5 px-3 py-1.5 text-[10px] text-slate-500">tiplao.com/donate/your-channel</div>
                    <span class="rounded-md bg-emerald-500/15 px-2 py-1 text-[9px] font-black text-emerald-300">LIVE</span>
                </div>

                <div class="relative min-h-[480px] overflow-hidden rounded-[1.4rem] bg-gradient-to-br from-[#19132e] via-[#111827] to-[#07111e] p-5 sm:p-8">
                    <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand-500/25 blur-3xl"></div>
                    <div class="relative flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-600 text-lg font-black text-white">T</span>
                            <div>
                                <p class="text-sm font-extrabold text-white">TIPLAO LIVE</p>
                                <p class="text-[10px] text-slate-400">ກຳລັງສະຕຣີມ · 1,284 ຄົນກຳລັງເບິ່ງ</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-rose-500/15 px-2.5 py-1 text-[9px] font-black text-rose-300">● ON AIR</span>
                    </div>

                    <div class="relative mt-7 grid gap-4 sm:grid-cols-[.9fr_1.1fr]">
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5 backdrop-blur">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-300">SUPPORT CREATOR</p>
                            <p class="mt-2 text-xl font-black text-white">ສົ່ງກຳລັງໃຈໃຫ້ TIPLAO</p>
                            <div class="mt-5 grid grid-cols-2 gap-2">
                                @foreach (['10,000', '20,000', '50,000', '100,000'] as $amount)
                                    <span class="rounded-xl border border-white/10 bg-white/5 px-2 py-2 text-center text-xs font-bold text-slate-200">{{ $amount }} ₭</span>
                                @endforeach
                            </div>
                            <div class="mt-3 rounded-xl border border-brand-400/30 bg-brand-500/10 px-3 py-2.5">
                                <p class="text-[10px] text-brand-200">ຈຳນວນເງິນ</p>
                                <p class="mt-1 text-lg font-black text-white">50,000 ₭</p>
                            </div>
                            <div class="mt-3 rounded-xl bg-brand-600 py-2.5 text-center text-xs font-black text-white">ໂດເນດຜ່ານ Lao QR</div>
                        </div>

                        <div class="rounded-2xl bg-white p-5 text-slate-950 shadow-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400">LAO QR PAYMENT</p>
                                    <p class="mt-1 text-sm font-black">ສະແກນເພື່ອຊຳລະເງິນ</p>
                                </div>
                                <span class="rounded-lg bg-blue-600 px-2 py-1 text-[9px] font-black text-white">LAO QR</span>
                            </div>
                            <div class="mx-auto mt-5 grid h-36 w-36 grid-cols-7 gap-1 rounded-xl border-8 border-white bg-white p-1 shadow ring-1 ring-slate-200">
                                @foreach ([1,1,1,1,0,1,1,1,0,0,1,0,1,1,1,0,1,1,1,0,1,1,1,0,1,0,1,1,0,0,1,0,0,1,0,1,1,0,1,1,1,1,1,0,1,0,1,0,1] as $cell)
                                    <span class="{{ $cell ? 'bg-slate-950' : 'bg-white' }}"></span>
                                @endforeach
                            </div>
                            <p class="mt-4 text-center text-sm font-black">50,000 LAK</p>
                            <p class="mt-1 text-center text-[10px] text-slate-400">ບັນຊີ TIPLAO LIVE</p>
                        </div>
                    </div>

                    <div class="nl-alert-float absolute bottom-5 left-5 right-5 rounded-2xl border border-brand-300/30 bg-[#151025]/90 p-4 shadow-2xl backdrop-blur sm:left-20 sm:right-8">
                        <div class="flex items-center gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-brand-500 text-sm font-black text-white">K</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-extrabold text-white">Kham ໂດເນດ <span class="text-emerald-300">50,000 ₭</span></p>
                                <p class="mt-1 truncate text-[10px] text-slate-300">ສູ້ໆ ຕິດຕາມທຸກໄລຟ໌ເດີ້!</p>
                            </div>
                            <div class="flex h-5 items-end gap-0.5">
                                @foreach ([2,4,3,5,2,4] as $height)
                                    <span class="nl-wave-bar w-1 rounded-full bg-brand-400" style="height: {{ $height * 3 }}px; animation-delay: {{ $loop->index * 0.12 }}s"></span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nl-float-chip absolute -left-5 top-24 hidden rounded-2xl border border-slate-200 bg-white p-3 shadow-xl sm:block dark:border-white/10 dark:bg-slate-900">
                <p class="text-[9px] font-bold text-slate-400">ໂດເນດມື້ນີ້</p>
                <p class="mt-1 text-sm font-black text-emerald-500">+1,250,000 ₭</p>
            </div>
        </div>
    </div>
</section>

<section class="overflow-hidden border-y border-slate-200 bg-slate-950 py-5 text-white dark:border-white/10" aria-label="ຈຸດເດັ່ນ">
    <div class="nl-marquee-track text-xs font-black sm:text-sm">
        @foreach ([1, 2] as $copy)
            <div class="flex shrink-0 items-center gap-12 pr-12">
                <span>✦ ສະໝັກໃຊ້ງານໄດ້ທັນທີ</span>
                <span>✦ ການຊຳລະທີ່ກວດສອບໄດ້</span>
                <span>✦ Overlay ສຳລັບ OBS</span>
                <span>✦ TTS ສຽງພາສາລາວ</span>
                <span>✦ ລາຍຮັບສະແດງເປັນ LAK</span>
                <span>✦ ປັບແຕ່ງໜ້າໂດເນດໄດ້</span>
            </div>
        @endforeach
    </div>
</section>

<section id="product-preview"
         x-data="{
             preview: 'donation',
             order: ['donation', 'overlay', 'dashboard'],
             timer: null,
             start() {
                 this.stop();
                 this.timer = setInterval(() => {
                     const next = (this.order.indexOf(this.preview) + 1) % this.order.length;
                     this.preview = this.order[next];
                 }, 5500);
             },
             stop() { if (this.timer) clearInterval(this.timer); },
             selectPreview(key) { this.preview = key; this.start(); }
         }"
         x-init="start()"
         @mouseenter="stop()"
         @mouseleave="start()"
         class="scroll-mt-24 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-black uppercase tracking-[0.18em] text-brand-500">PRODUCT PREVIEW</p>
            <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">ເບິ່ງໂປຣດັກກ່ອນເລີ່ມໃຊ້ງານ</h2>
            <p class="mt-5 text-base leading-8 text-slate-500 dark:text-slate-400">ທຸກສ່ວນຖືກອອກແບບໃຫ້ຄຣີເອເຕີລາວຕັ້ງຄ່າງ່າຍ ແລະ ໃຊ້ງານໄດ້ຈິງໃນໄລຟ໌.</p>
        </div>

        <div class="mx-auto mt-9 flex max-w-2xl flex-wrap justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-2 dark:border-white/10 dark:bg-white/5">
            @foreach ([
                ['donation', 'ໜ້າໂດເນດ'],
                ['overlay', 'ແຈ້ງເຕືອນ OBS'],
                ['dashboard', 'Dashboard'],
            ] as [$key, $label])
                <button type="button" @click="selectPreview('{{ $key }}')"
                        class="relative overflow-hidden rounded-xl px-5 py-2.5 text-sm font-bold transition"
                        :class="preview === '{{ $key }}' ? 'bg-brand-600 text-white shadow-lg' : 'text-slate-500 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white'">
                    {{ $label }}
                    <span x-show="preview === '{{ $key }}'" :key="preview" class="nl-tab-progress absolute inset-x-0 bottom-0 h-0.5 bg-white/80"></span>
                </button>
            @endforeach
        </div>

        <div class="mt-10 overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 p-3 shadow-2xl shadow-slate-950/10 sm:p-5 dark:border-white/10 dark:bg-slate-950">
            <div class="flex items-center gap-2 px-2 pb-4">
                <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                <span class="ml-3 text-[10px] font-semibold text-slate-400" x-text="preview === 'donation' ? 'tiplao.com/donate/your-channel' : (preview === 'overlay' ? 'OBS Browser Source · TIPLAO Alert' : 'TIPLAO Creator Dashboard')"></span>
            </div>

            <div class="relative min-h-[520px]">
            <div x-show="preview === 'donation'"
                 x-transition:enter="transition duration-500 ease-out"
                 x-transition:enter-start="translate-x-8 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-8 opacity-0"
                 class="absolute inset-0 grid min-h-[520px] gap-8 rounded-[1.4rem] bg-gradient-to-br from-[#18112b] to-[#07111e] p-6 sm:p-10 lg:grid-cols-[.8fr_1.2fr]">
                <div class="flex flex-col justify-center text-white">
                    <span class="w-fit rounded-full bg-brand-500/15 px-3 py-1 text-xs font-bold text-brand-200">DONATION PAGE</span>
                    <h3 class="mt-5 text-3xl font-black sm:text-4xl">ໜ້າຮັບໂດເນດທີ່ເປັນແບຣນຂອງທ່ານ</h3>
                    <p class="mt-4 leading-8 text-slate-300">ເລືອກຈຳນວນ, ຂຽນຂໍ້ຄວາມ, ສະແກນ Lao QR ແລະ ສົ່ງກຳລັງໃຈໃຫ້ຄຣີເອເຕີໄດ້ຈາກມືຖື.</p>
                    <ul class="mt-6 grid gap-3 text-sm text-slate-200">
                        <li>✓ ປັບຊື່, ຮູບ ແລະ ຂໍ້ຄວາມໄດ້</li>
                        <li>✓ ຈຳນວນເງິນດ່ວນເປັນ LAK</li>
                        <li>✓ ຮອງຮັບມືຖືທຸກຂະໜາດ</li>
                    </ul>
                </div>
                <div class="self-center rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900">
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-600 font-black text-white">T</span>
                        <div><p class="font-black">TIPLAO LIVE</p><p class="text-xs text-slate-400">ຂອບໃຈທຸກການສະໜັບສະໜູນ!</p></div>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-2">
                        @foreach (['10,000', '20,000', '50,000', '100,000', '200,000', '500,000'] as $amount)
                            <span class="rounded-xl border border-slate-200 px-2 py-2 text-center text-xs font-bold dark:border-white/10">{{ $amount }} ₭</span>
                        @endforeach
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-400 dark:border-white/10">ຊື່ຜູ້ໂດເນດ</div>
                        <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-400 dark:border-white/10">50,000 LAK</div>
                    </div>
                    <div class="mt-3 rounded-xl border border-slate-200 px-4 py-4 text-sm text-slate-400 dark:border-white/10">ຂໍ້ຄວາມສົ່ງໃຫ້ຄຣີເອເຕີ...</div>
                    <div class="mt-4 rounded-xl bg-brand-600 py-3 text-center text-sm font-black text-white">ສືບຕໍ່ໄປທີ່ Lao QR</div>
                </div>
            </div>

            <div x-show="preview === 'overlay'" x-cloak
                 x-transition:enter="transition duration-500 ease-out"
                 x-transition:enter-start="translate-x-8 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-8 opacity-0"
                 class="absolute inset-0 min-h-[520px] overflow-hidden rounded-[1.4rem] bg-[#09131b] p-6 sm:p-10">
                <div class="absolute inset-0 opacity-20" style="background-image:linear-gradient(rgba(255,255,255,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.08) 1px,transparent 1px);background-size:32px 32px"></div>
                <div class="relative flex min-h-[440px] flex-col items-center justify-center">
                    <span class="rounded-full bg-sky-400/10 px-3 py-1 text-xs font-bold text-sky-300">OBS BROWSER SOURCE PREVIEW</span>
                    <div class="mt-8 w-full max-w-2xl rounded-[2rem] border border-brand-300/30 bg-[#171126]/95 p-6 shadow-2xl shadow-brand-500/20 backdrop-blur sm:p-8">
                        <div class="flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
                            <span class="grid h-20 w-20 shrink-0 place-items-center rounded-full bg-gradient-to-br from-pink-500 to-brand-600 text-2xl font-black text-white ring-4 ring-white/10">N</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-brand-200">NEW DONATION</p>
                                <p class="mt-1 text-2xl font-black text-white">Noy ໂດເນດ 100,000 ₭</p>
                                <p class="mt-2 text-sm text-slate-300">ໄລຟ໌ມ່ວນຫຼາຍ ສູ້ໆເດີ້!</p>
                            </div>
                            <div class="flex h-12 items-end gap-1">
                                @foreach ([3,7,5,10,6,12,8,5] as $height)
                                    <span class="w-1.5 rounded-full bg-gradient-to-t from-brand-600 to-pink-400" style="height: {{ $height * 3 }}px"></span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-9 grid w-full max-w-3xl gap-3 text-center text-xs font-bold text-slate-300 sm:grid-cols-4">
                        <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-3">ສຽງແຈ້ງເຕືອນ</span>
                        <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-3">TTS ພາສາລາວ</span>
                        <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-3">GIF / WEBM</span>
                        <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-3">Animation</span>
                    </div>
                </div>
            </div>

            <div x-show="preview === 'dashboard'" x-cloak
                 x-transition:enter="transition duration-500 ease-out"
                 x-transition:enter-start="translate-x-8 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-8 opacity-0"
                 class="absolute inset-0 min-h-[520px] overflow-y-auto rounded-[1.4rem] bg-slate-50 p-5 sm:p-8 dark:bg-[#0d111b]">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="text-xs font-bold text-brand-500">CREATOR DASHBOARD</p><h3 class="mt-1 text-2xl font-black">ພາບລວມລາຍຮັບຂອງທ່ານ</h3></div>
                    <span class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold dark:border-white/10 dark:bg-white/5">7 ມື້ຫຼ້າສຸດ</span>
                </div>
                <div class="mt-7 grid gap-4 sm:grid-cols-3">
                    @foreach ([
                        ['ຍອດລວມ', '8,450,000 ₭', '+18.4%'],
                        ['ຈຳນວນໂດເນດ', '284 ຄັ້ງ', '+32'],
                        ['ສູງສຸດ', '500,000 ₭', 'ມື້ນີ້'],
                    ] as [$label, $value, $change])
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                            <p class="text-xs text-slate-400">{{ $label }}</p>
                            <p class="mt-2 text-xl font-black">{{ $value }}</p>
                            <p class="mt-2 text-xs font-bold text-emerald-500">{{ $change }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 grid gap-4 lg:grid-cols-[1.5fr_.5fr]">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                        <div class="flex items-center justify-between"><p class="text-sm font-bold">ລາຍຮັບ 7 ມື້</p><p class="text-xs text-slate-400">LAK</p></div>
                        <div class="mt-7 flex h-44 items-end gap-3">
                            @foreach ([34, 52, 45, 72, 60, 88, 100] as $height)
                                <div class="flex flex-1 flex-col items-center gap-2">
                                    <span class="w-full rounded-t-lg bg-gradient-to-t from-brand-700 to-brand-400" style="height: {{ $height }}%"></span>
                                    <span class="text-[9px] text-slate-400">{{ $loop->iteration }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
                        <p class="text-sm font-bold">ໂດເນດລ່າສຸດ</p>
                        <div class="mt-4 grid gap-4">
                            @foreach ([['K', 'Kham', '50,000 ₭'], ['N', 'Noy', '100,000 ₭'], ['P', 'Phet', '20,000 ₭']] as [$initial, $name, $amount])
                                <div class="flex items-center gap-3">
                                    <span class="grid h-8 w-8 place-items-center rounded-full bg-brand-500/10 text-xs font-black text-brand-500">{{ $initial }}</span>
                                    <div class="min-w-0 flex-1"><p class="text-xs font-bold">{{ $name }}</p><p class="text-[10px] text-slate-400">ຫາກໍ່ໂດເນດ</p></div>
                                    <p class="text-xs font-black text-emerald-500">{{ $amount }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>

@if ($features->count())
    <section class="border-y border-slate-200 bg-slate-50/80 py-20 dark:border-white/10 dark:bg-white/[0.025]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-black uppercase tracking-[0.16em] text-brand-500">ALL-IN-ONE PRODUCT</p>
                <h2 class="mt-3 text-4xl font-black tracking-tight">ເຄື່ອງມືຄົບສຳລັບການໄລຟ໌</h2>
                <p class="mt-4 leading-8 text-slate-500 dark:text-slate-400">ຫຼຸດຂັ້ນຕອນທີ່ຊັບຊ້ອນ ແລະ ຈັດການທຸກຢ່າງຈາກ Dashboard ດຽວ.</p>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($features as $index => $feature)
                    <article class="group rounded-3xl border border-slate-200 bg-white p-7 transition hover:-translate-y-1 hover:border-brand-500/40 hover:shadow-xl dark:border-white/10 dark:bg-slate-900/60">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-500/10 text-sm font-black text-brand-500">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-xl text-slate-300 transition group-hover:translate-x-1 group-hover:text-brand-500">→</span>
                        </div>
                        <h3 class="mt-6 text-xl font-black">{{ $feature->heading }}</h3>
                        @if ($feature->body)
                            <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ strip_tags($feature->body) }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="py-20 sm:py-28">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-2">
        <div class="rounded-[2rem] bg-slate-950 p-7 text-white shadow-2xl sm:p-10">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-brand-300">START IN MINUTES</p>
            <div class="mt-7 space-y-7">
                @foreach ([
                    ['01', 'ສ້າງບັນຊີ', 'ສະໝັກດ້ວຍຊື່ຊ່ອງ ແລະ ອີເມວ ເຂົ້າໃຊ້ງານໄດ້ທັນທີ'],
                    ['02', 'ຕັ້ງຄ່າໜ້າໂດເນດ', 'ປັບຊື່, ຮູບ, ຈຳນວນດ່ວນ ແລະ ແບ່ງປັນລິ້ງຮັບໂດເນດ'],
                    ['03', 'ເຊື່ອມຕໍ່ OBS', 'ຄັດລອກລິ້ງ Overlay ໃສ່ Browser Source ແລ້ວເລີ່ມໄລຟ໌'],
                ] as [$number, $title, $body])
                    <div class="flex gap-5">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-600 text-xs font-black">{{ $number }}</span>
                        <div><h3 class="font-black">{{ $title }}</h3><p class="mt-1 text-sm leading-7 text-slate-300">{{ $body }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <p class="text-sm font-black uppercase tracking-[0.16em] text-brand-500">SIMPLE SETUP</p>
            <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">ຈາກສະໝັກ ຫາ ຮັບໂດເນດ ໃນບໍ່ກີ່ນາທີ</h2>
            <p class="mt-5 text-base leading-8 text-slate-500 dark:text-slate-400">ບໍ່ຕ້ອງຂຽນໂຄດ ຫຼື ຕິດຕັ້ງໂປຣແກຣມເພີ່ມ. ລະບົບຈັດການໜ້າໂດເນດ, ຄິວແຈ້ງເຕືອນ ແລະ ສຽງອ່ານໃຫ້ທັງໝົດ.</p>
            @guest
                <a href="{{ route('register') }}" class="nl-btn-primary mt-8 px-7 py-3.5 text-base">ສ້າງໜ້າໂດເນດຂອງທ່ານ</a>
            @endguest
        </div>
    </div>
</section>

@if ($communities->count())
    <section class="mx-auto max-w-7xl px-6 pb-20">
        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-700 to-slate-950 px-7 py-10 text-white sm:px-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-black text-brand-200">TIPLAO COMMUNITY</p>
                    <h2 class="mt-2 text-3xl font-black">ມີຄອມມູນິຕີຄຣີເອເຕີຢູ່ຂ້າງທ່ານ</h2>
                    <p class="mt-3 leading-7 text-slate-200">ຮັບຂ່າວ, ສອບຖາມການໃຊ້ງານ ແລະ ແລກປ່ຽນປະສົບການກັບຄຣີເອເຕີລາວ.</p>
                </div>
                <a href="{{ route('page.show', 'community') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-black text-slate-950">ເຂົ້າຄອມມູນິຕີ</a>
            </div>
            <div class="mt-8 grid gap-3 md:grid-cols-3">
                @foreach ($communities as $community)
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                        <p class="font-black">{{ $community->heading }}</p>
                        <p class="mt-2 text-sm leading-6 text-slate-200">{{ $community->subheading ?: strip_tags($community->body) }}</p>
                        @if ($community->link_url)
                            <a href="{{ $community->link_url }}" target="_blank" rel="noopener" class="mt-4 inline-flex text-sm font-bold text-brand-200">{{ $community->link_label ?: 'ເຂົ້າຮ່ວມ' }} →</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if ($news->count())
    <section class="border-y border-slate-200 bg-slate-50/80 py-16 dark:border-white/10 dark:bg-white/[0.025]">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-sm font-black text-brand-500">ອັບເດດຈາກທີມງານ</p><h2 class="mt-2 text-3xl font-black tracking-tight">ຂ່າວສານລ່າສຸດ</h2></div>
                <a href="{{ route('page.show', 'news') }}" class="text-sm font-black text-brand-500 hover:underline">ເບິ່ງຂ່າວທັງໝົດ →</a>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach ($news as $item)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-slate-900/70">
                        <div class="flex items-center justify-between gap-3 text-xs"><span class="font-bold text-brand-500">{{ $item->subheading ?: 'ປະກາດ' }}</span><time class="text-slate-400">{{ $item->updated_at->format('d/m/Y') }}</time></div>
                        <h3 class="mt-4 text-lg font-black leading-7">{{ $item->heading }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 120) }}</p>
                        <a href="{{ route('page.show', 'news') }}#news-{{ $item->id }}" class="mt-4 inline-flex text-sm font-bold text-brand-500">ອ່ານຕໍ່ →</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@foreach ($texts as $text)
    <section class="prose prose-slate mx-auto max-w-3xl px-6 py-12 dark:prose-invert">
        @if ($text->heading)<h2>{{ $text->heading }}</h2>@endif
        {!! $text->body !!}
    </section>
@endforeach

<section class="mx-auto max-w-7xl px-6 py-20">
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-950 px-6 py-14 text-center text-white sm:px-10 sm:py-20">
        <div class="absolute left-1/2 top-0 h-80 w-80 -translate-x-1/2 rounded-full bg-brand-500/30 blur-3xl"></div>
        <div class="relative mx-auto max-w-3xl">
            <p class="text-sm font-black uppercase tracking-[0.16em] text-brand-300">READY TO GO LIVE?</p>
            <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">ປ່ຽນຜູ້ຊົມໃຫ້ເປັນກຳລັງໃຈ</h2>
            <p class="mx-auto mt-5 max-w-xl leading-8 text-slate-300">ເປີດໜ້າໂດເນດຂອງທ່ານ ແລະ ສ້າງປະສົບການໄລຟ໌ທີ່ດີກວ່າດ້ວຍ TIPLAO DONATE.</p>
            @guest
                <a href="{{ route('register') }}" class="mt-8 inline-flex rounded-xl bg-white px-8 py-3.5 text-base font-black text-slate-950 transition hover:-translate-y-0.5">ສະໝັກໃຊ້ງານຟຣີ</a>
            @else
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="mt-8 inline-flex rounded-xl bg-white px-8 py-3.5 text-base font-black text-slate-950 transition hover:-translate-y-0.5">ເຂົ້າແດຊບອດ</a>
            @endguest
        </div>
    </div>
</section>

@foreach ($ctas as $cta)
    <section class="mx-auto max-w-7xl px-6 pb-12 text-center">
        <h2 class="text-2xl font-black">{{ $cta->heading }}</h2>
        @if ($cta->subheading)<p class="mt-3 text-slate-500 dark:text-slate-400">{{ $cta->subheading }}</p>@endif
        @if ($cta->link_url)<a href="{{ $cta->link_url }}" class="nl-btn-primary mt-6">{{ $cta->link_label ?: 'ເລີ່ມໃຊ້ງານ' }}</a>@endif
    </section>
@endforeach
@endsection
