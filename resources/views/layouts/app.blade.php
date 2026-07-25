<!DOCTYPE html>
<html lang="lo" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'ແດຊບອດ')</title>

    {{-- ใช้ธีม (มืด/สว่าง + สีหลัก) ที่บันทึกไว้ ก่อน paint เพื่อไม่ให้กระพริบ --}}
    <script>
        (function () {
            const saved = localStorage.getItem('nl-theme');
            const dark = saved ? saved === 'dark' : true; // ค่าเริ่มต้น = โหมดมืด (gaming)
            if (dark) document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-accent', localStorage.getItem('nl-accent') || 'purple');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:300,400,500,600,700,800" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-900 dark:bg-[#0b0b14] dark:text-slate-100">
@php
    $isAdmin = auth()->user()->isAdmin();

    // ไอคอนเส้น (heroicons outline) — เก็บเป็น path string เพื่อให้ markup สะอาด
    $icons = [
        'home'      => '<path d="M2.25 12 11.2 3.05a1.13 1.13 0 0 1 1.6 0L21.75 12M4.5 9.75v9.75a.75.75 0 0 0 .75.75H9.75v-6h4.5v6h4.5a.75.75 0 0 0 .75-.75V9.75"/>',
        'history'   => '<path d="M12 8.25v3.75l2.25 1.5M21 12a9 9 0 1 1-9-9c2.6 0 4.95 1.1 6.6 2.85M21 4.5V9h-4.5"/>',
        'link'      => '<path d="M13.19 8.69a4.5 4.5 0 0 1 0 6.36l-3 3a4.5 4.5 0 0 1-6.36-6.36l1.5-1.5M10.81 15.31a4.5 4.5 0 0 1 0-6.36l3-3a4.5 4.5 0 0 1 6.36 6.36l-1.5 1.5"/>',
        'overlay'   => '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="M2.5 9.5h19M6.5 14h7"/>',
        'media'     => '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m21 16-4.5-4.5L7 19.5"/>',
        'user'      => '<circle cx="12" cy="7.5" r="3.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'users'     => '<circle cx="9" cy="8" r="3"/><path d="M2.5 19a6.5 6.5 0 0 1 13 0M16 6a3 3 0 0 1 0 6m5 7a5.5 5.5 0 0 0-4-5.3"/>',
        'gamepad'   => '<path d="M7 11h-2m1-1v2M16 10.5h.01M18.5 12.5h.01"/><rect x="2.5" y="6.5" width="19" height="11" rx="4.5"/>',
        'money'     => '<rect x="2.5" y="5.5" width="19" height="13" rx="2.5"/><circle cx="12" cy="12" r="2.75"/><path d="M6 12h.01M18 12h.01"/>',
        'log'       => '<rect x="4.5" y="2.5" width="15" height="19" rx="2.5"/><path d="M8.5 7.5h7M8.5 11.5h7M8.5 15.5h4"/>',
        'cog'       => '<circle cx="12" cy="12" r="3"/><path d="M12 2.5v2.5M12 19v2.5M21.5 12H19M5 12H2.5M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8M18.7 18.7l-1.8-1.8M7.1 7.1 5.3 5.3"/>',
        'goal'      => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
        'doc'       => '<path d="M6 2.5h8l5 5v14h-13z"/><path d="M14 2.5V8h5.5M9 13h6M9 17h6"/>',
    ];

    $nav = $isAdmin ? [
        ['route' => 'admin.dashboard',       'label' => 'ໜ້າຫຼັກ',          'icon' => 'home'],
        ['route' => 'admin.streamers.index', 'label' => 'ສະຕຣີມເມີ',        'icon' => 'gamepad'],
        ['route' => 'admin.users.index',     'label' => 'ຜູ້ໃຊ້ງານ',          'icon' => 'users'],
        ['route' => 'admin.donations.index', 'label' => 'ລາຍການໂດເນດ',     'icon' => 'money'],
        ['route' => 'admin.withdrawals.index','label' => 'ຄຳຂໍຖອນເງິນ',       'icon' => 'money'],
        ['route' => 'admin.media.index',     'label' => 'ຄັງສື່',             'icon' => 'media'],
        ['route' => 'admin.logs.index',      'label' => 'ປະຫວັດການໃຊ້ງານ', 'icon' => 'log'],
        ['route' => 'admin.content.index',   'label' => 'ຈັດການເນື້ອຫາ',    'icon' => 'doc'],
        ['route' => 'admin.settings.edit',   'label' => 'ຕັ້ງຄ່າລະບົບ',      'icon' => 'cog'],
    ] : [
        ['route' => 'dashboard',             'label' => 'ໜ້າຫຼັກ',             'icon' => 'home'],
        ['route' => 'donations.index',       'label' => 'ປະຫວັດການໂດເນດ',  'icon' => 'history'],
        ['route' => 'wallet.index',          'label' => 'ກະເປົາ / ຖອນເງິນ',  'icon' => 'money'],
        ['route' => 'donation-page.edit',    'label' => 'ໜ້າໂດເນດ',           'icon' => 'link'],
        ['route' => 'overlay-settings.edit', 'label' => 'ຕັ້ງຄ່າ Overlay',     'icon' => 'overlay'],
        ['route' => 'donation-goal.edit',    'label' => 'ເປົ້າໝາຍໂດເນດ',     'icon' => 'goal'],
        ['route' => 'media.index',           'label' => 'ຄັງສື່',               'icon' => 'media'],
        ['route' => 'profile.edit',          'label' => 'ໂປຣໄຟລ໌',             'icon' => 'user'],
    ];

    $icon = fn (string $name) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 shrink-0">'.($icons[$name] ?? '').'</svg>';
@endphp

<div x-data="{ sidebar: false }" class="min-h-full lg:flex">
    {{-- แถบเมนูด้านข้าง --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-transform duration-200 lg:static lg:translate-x-0 dark:border-white/10 dark:bg-slate-900/60 dark:backdrop-blur-xl"
        :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        {{-- TIPLAO DONATE logo --}}
        <div class="flex h-16 items-center gap-2.5 px-6">
            <span class="relative grid h-9 w-9 place-items-center rounded-xl bg-brand-600 text-white dark:nl-glow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M2 12h3l2.5-6 4 13 3-8 1.5 4H22"/>
                </svg>
            </span>
            <span class="leading-none">
                <span class="block text-base font-extrabold tracking-tight">TIPLAO</span>
                <span class="-mt-0.5 flex items-center gap-1 text-[10px] font-bold uppercase tracking-[0.25em] text-brand-500">
                    <span class="nl-live-dot inline-block h-1.5 w-1.5 rounded-full bg-rose-500"></span>DONATE
                </span>
            </span>
        </div>

        <nav class="space-y-1 px-3 py-4">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                          {{ request()->routeIs($item['route']) ? 'bg-brand-600 text-white shadow dark:nl-glow' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/5' }}">
                    {!! $icon($item['icon']) !!}<span class="flex-1">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- การ์ดผู้ใช้ด้านล่าง --}}
        <div class="absolute inset-x-3 bottom-4">
            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 dark:border-white/10 dark:bg-white/5">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-600 text-sm font-bold text-white">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="flex items-center gap-1 text-[11px] text-emerald-500">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500"></span>ອອນລາຍ
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <div x-show="sidebar" @click="sidebar = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-cloak></div>

    {{-- คอลัมน์หลัก --}}
    <div class="flex min-h-full flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8 dark:border-white/10 dark:bg-[#0b0b14]/80">
            <div class="flex items-center gap-3">
                <button @click="sidebar = !sidebar" class="rounded-lg p-2 hover:bg-slate-100 lg:hidden dark:hover:bg-white/5" aria-label="ເປີດເມນູ">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base font-semibold lg:text-lg">@yield('title', 'ແດຊບອດ')</h1>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- ตัวเลือกสีธีม --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="grid h-9 w-9 place-items-center rounded-lg hover:bg-slate-100 dark:hover:bg-white/5" aria-label="ເລືອກສີທີມ" title="ເລືອກສີທີມ">
                        <span class="h-4 w-4 rounded-full ring-2 ring-white dark:ring-slate-900" style="background: var(--color-brand-500)"></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak x-transition.opacity
                         class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white p-3 shadow-lg dark:border-white/10 dark:bg-slate-900">
                        <p class="mb-2 text-xs font-semibold text-slate-400">ສີທີມ</p>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="a in $store.theme.accents" :key="a.key">
                                <button @click="$store.theme.setAccent(a.key)" :title="a.label"
                                        class="grid h-8 w-8 place-items-center rounded-lg transition hover:scale-110"
                                        :style="`background:${a.color}`">
                                    <svg x-show="$store.theme.accent === a.key" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- สลับโหมดมืด/สว่าง --}}
                <button @click="$store.theme.toggle()" class="grid h-9 w-9 place-items-center rounded-lg hover:bg-slate-100 dark:hover:bg-white/5" aria-label="ສະຫຼັບໂໝດມືດ/ແຈ້ງ" title="ສະຫຼັບໂໝດມືດ/ແຈ້ງ">
                    <svg x-show="!$store.theme.dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
                    <svg x-show="$store.theme.dark" x-cloak viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><circle cx="12" cy="12" r="4.5"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/></svg>
                </button>

                {{-- เมนูผู้ใช้ --}}
                <div x-data="{ open: false }" class="relative ml-1">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-slate-100 dark:hover:bg-white/5">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-brand-600 text-sm font-bold text-white">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-sm font-medium sm:block">{{ auth()->user()->name }}</span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak x-transition.opacity
                         class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-white/10 dark:bg-slate-900">
                        <a href="{{ route('home') }}" target="_blank" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5">ເປີດໜ້າເວັບຫຼັກ ↗</a>
                        @unless ($isAdmin)
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5">ໂປຣໄຟລ໌ຂອງຂ້ອຍ</a>
                            @if (auth()->user()->streamer)
                                <a href="{{ route('donate.show', auth()->user()->streamer->username) }}" target="_blank" class="block rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5">ເປີດໜ້າໂດເນດ ↗</a>
                            @endif
                        @endunless
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-white/5">ອອກຈາກລະບົບ</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 lg:px-8">
            <x-flash />
            @yield('content')
        </main>
    </div>
</div>
<style>[x-cloak]{display:none!important}</style>
@stack('scripts')
</body>
</html>
