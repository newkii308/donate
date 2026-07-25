@inject('site', 'App\Services\GlobalSettings')
@php
    $brand = $site->get('brand_color');
    $logo = $site->get('logo_url');
    $siteName = $site->get('platform_name');
    $socials = array_filter([
        'facebook' => $site->get('social_facebook'),
        'youtube' => $site->get('social_youtube'),
        'tiktok' => $site->get('social_tiktok'),
        'discord' => $site->get('social_discord'),
        'telegram' => $site->get('social_telegram'),
        'line' => $site->get('social_line'),
    ]);
    $currentPage = request()->route('page');
    $navClass = fn (bool $active) => $active
        ? 'text-brand-600 dark:text-brand-400'
        : 'text-slate-600 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white';
@endphp
<!DOCTYPE html>
<html lang="lo" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $siteName) — {{ $site->get('tagline') }}</title>
    <meta name="description" content="{{ $site->get('meta_description') }}">
    @if ($site->get('favicon_url'))
        <link rel="icon" href="{{ $site->get('favicon_url') }}">
    @endif
    <script>
        (function () {
            const saved = localStorage.getItem('nl-theme');
            const dark = saved ? saved === 'dark' : true;
            if (dark) document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-accent', localStorage.getItem('nl-accent') || 'purple');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:300,400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($brand)
        <style>html[data-accent]{--color-brand-400:{{ $brand }};--color-brand-500:{{ $brand }};--color-brand-600:{{ $brand }};--color-brand-700:{{ $brand }};}</style>
    @endif
</head>
<body class="min-h-full bg-white text-slate-950 antialiased dark:bg-[#080810] dark:text-slate-100" style="font-family:'Noto Sans Lao',system-ui,sans-serif">
    <div class="pointer-events-none fixed inset-0 hidden overflow-hidden dark:block" aria-hidden="true">
        <div class="absolute -top-56 left-1/3 h-[34rem] w-[34rem] rounded-full bg-brand-600/15 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-sky-600/10 blur-3xl"></div>
    </div>

    @if ($site->get('announcement_enabled') && $site->get('announcement_text'))
        <div class="relative z-50 bg-brand-600 px-4 py-2 text-center text-sm font-semibold text-white">
            {{ $site->get('announcement_text') }}
        </div>
    @endif

    <header x-data="{ menu: false, policy: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl dark:border-white/10 dark:bg-[#080810]/88">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-5 px-5 sm:px-6">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $siteName }}">
                @if ($logo)
                    <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-10 w-auto">
                @else
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-brand-600 text-white shadow-lg shadow-brand-600/20">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M2 12h3l2.5-6 4 13 3-8 1.5 4H22"/></svg>
                    </span>
                    <span class="leading-none">
                        <span class="block text-base font-extrabold tracking-tight">TIPLAO</span>
                        <span class="mt-1 block text-[9px] font-bold uppercase tracking-[0.28em] text-brand-500">DONATE</span>
                    </span>
                @endif
            </a>

            <nav class="hidden items-center gap-5 text-sm font-semibold xl:flex" aria-label="ເມນູຫຼັກ">
                <a href="{{ route('home') }}" class="transition {{ $navClass(request()->routeIs('home')) }}">ໜ້າຫຼັກ</a>
                <a href="{{ route('page.show', 'news') }}" class="transition {{ $navClass($currentPage === 'news') }}">ຂ່າວສານ</a>
                <a href="{{ route('page.show', 'community') }}" class="transition {{ $navClass($currentPage === 'community') }}">ຄອມມູນິຕີ</a>
                <a href="{{ route('page.show', 'about') }}" class="transition {{ $navClass($currentPage === 'about') }}">ກ່ຽວກັບ</a>
                <div class="relative">
                    <button type="button" @click="policy = !policy" @click.outside="policy = false"
                            class="flex items-center gap-1 transition {{ $navClass(in_array($currentPage, ['terms', 'privacy', 'rules', 'withdrawal-terms', 'faq'], true)) }}">
                        ຂໍ້ມູນ ແລະ ນະໂຍບາຍ
                        <span class="text-[10px]" :class="policy && 'rotate-180'">▼</span>
                    </button>
                    <div x-show="policy" x-cloak x-transition
                         class="absolute left-1/2 mt-3 w-60 -translate-x-1/2 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-white/10 dark:bg-slate-900">
                        <a href="{{ route('page.show', 'terms') }}" class="block rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ເງື່ອນໄຂການນຳໃຊ້</a>
                        <a href="{{ route('page.show', 'privacy') }}" class="block rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ</a>
                        <a href="{{ route('page.show', 'rules') }}" class="block rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ກົດລະບຽບການນຳໃຊ້</a>
                        <a href="{{ route('page.show', 'withdrawal-terms') }}" class="block rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ເງື່ອນໄຂຖອນເງິນ</a>
                        <a href="{{ route('page.show', 'faq') }}" class="block rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ຄຳຖາມທີ່ພົບເລື້ອຍ</a>
                    </div>
                </div>
                <a href="{{ route('page.show', 'contact') }}" class="transition {{ $navClass($currentPage === 'contact') }}">ຕິດຕໍ່</a>
            </nav>

            <div class="hidden shrink-0 items-center gap-2 sm:flex">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="nl-btn-primary">
                        ເຂົ້າແດຊບອດ
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nl-btn-ghost">ເຂົ້າລະບົບ</a>
                    @if ($site->get('registration_open'))
                        <a href="{{ route('register') }}" class="nl-btn-primary">ສະໝັກເຂົ້າຮ່ວມ</a>
                    @endif
                @endauth
            </div>

            <button type="button" @click="menu = !menu" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 xl:hidden dark:border-white/10" aria-label="ເປີດເມນູ">
                <span class="text-xl leading-none" x-text="menu ? '×' : '☰'"></span>
            </button>
        </div>

        <div x-show="menu" x-cloak x-transition class="border-t border-slate-200 bg-white px-5 py-5 xl:hidden dark:border-white/10 dark:bg-slate-950">
            <nav class="mx-auto grid max-w-7xl gap-1 text-sm font-semibold">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ໜ້າຫຼັກ</a>
                <a href="{{ route('page.show', 'news') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ຂ່າວສານ</a>
                <a href="{{ route('page.show', 'community') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ຄອມມູນິຕີ</a>
                <a href="{{ route('page.show', 'about') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ກ່ຽວກັບພວກເຮົາ</a>
                <a href="{{ route('page.show', 'terms') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ເງື່ອນໄຂການນຳໃຊ້</a>
                <a href="{{ route('page.show', 'privacy') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ</a>
                <a href="{{ route('page.show', 'rules') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ກົດລະບຽບການນຳໃຊ້</a>
                <a href="{{ route('page.show', 'withdrawal-terms') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ເງື່ອນໄຂຖອນເງິນ</a>
                <a href="{{ route('page.show', 'faq') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ຄຳຖາມທີ່ພົບເລື້ອຍ</a>
                <a href="{{ route('page.show', 'contact') }}" class="rounded-xl px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-white/5">ຕິດຕໍ່ພວກເຮົາ</a>
            </nav>
            <div class="mx-auto mt-4 grid max-w-7xl gap-2 border-t border-slate-200 pt-4 sm:hidden dark:border-white/10">
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="nl-btn-primary justify-center">ເຂົ້າແດຊບອດ</a>
                @else
                    <a href="{{ route('login') }}" class="nl-btn-ghost justify-center">ເຂົ້າລະບົບ</a>
                    @if ($site->get('registration_open'))
                        <a href="{{ route('register') }}" class="nl-btn-primary justify-center">ສະໝັກເຂົ້າຮ່ວມ</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="relative">
        @yield('content')
    </main>

    <footer class="relative mt-20 border-t border-slate-200 bg-slate-50/70 py-12 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="mx-auto grid max-w-7xl gap-10 px-6 md:grid-cols-[1.2fr_2fr]">
            <div>
                <p class="text-lg font-extrabold">{{ $siteName }}</p>
                <p class="mt-2 max-w-sm text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $site->get('tagline') }}</p>
                @if (count($socials))
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach ($socials as $net => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                               class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-bold uppercase text-slate-500 transition hover:border-brand-500 hover:text-brand-500 dark:border-white/10 dark:text-slate-300">
                                {{ $net }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <p class="text-sm font-bold">ແພລດຟອມ</p>
                    <nav class="mt-3 grid gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <a href="{{ route('page.show', 'news') }}" class="hover:text-brand-500">ຂ່າວສານ</a>
                        <a href="{{ route('page.show', 'community') }}" class="hover:text-brand-500">ຄອມມູນິຕີ</a>
                        <a href="{{ route('page.show', 'about') }}" class="hover:text-brand-500">ກ່ຽວກັບ</a>
                    </nav>
                </div>
                <div>
                    <p class="text-sm font-bold">ຂໍ້ມູນສຳຄັນ</p>
                    <nav class="mt-3 grid gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <a href="{{ route('page.show', 'terms') }}" class="hover:text-brand-500">ເງື່ອນໄຂ</a>
                        <a href="{{ route('page.show', 'privacy') }}" class="hover:text-brand-500">ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ</a>
                        <a href="{{ route('page.show', 'rules') }}" class="hover:text-brand-500">ກົດລະບຽບການນຳໃຊ້</a>
                        <a href="{{ route('page.show', 'withdrawal-terms') }}" class="hover:text-brand-500">ເງື່ອນໄຂຖອນເງິນ</a>
                        <a href="{{ route('page.show', 'faq') }}" class="hover:text-brand-500">ຄຳຖາມທີ່ພົບເລື້ອຍ</a>
                    </nav>
                </div>
                <div>
                    <p class="text-sm font-bold">ຕິດຕໍ່</p>
                    <nav class="mt-3 grid gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <a href="{{ route('page.show', 'contact') }}" class="hover:text-brand-500">ສູນຊ່ວຍເຫຼືອ</a>
                        @if ($site->get('contact_email'))
                            <a href="mailto:{{ $site->get('contact_email') }}" class="break-all hover:text-brand-500">{{ $site->get('contact_email') }}</a>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
        <div class="mx-auto mt-10 max-w-7xl border-t border-slate-200 px-6 pt-6 text-xs text-slate-400 dark:border-white/10">
            {{ $site->get('footer_text') ?: '© '.date('Y').' '.$siteName.' — '.$site->get('tagline') }}
        </div>
    </footer>
</body>
</html>
