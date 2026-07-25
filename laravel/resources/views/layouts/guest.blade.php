<!DOCTYPE html>
<html lang="lo" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — @yield('title', 'ຍິນດີຕ້ອນຮັບ')</title>
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
</head>
<body class="grid min-h-full place-items-center bg-gradient-to-br from-slate-100 via-white to-brand-100 px-4 py-10 dark:from-[#0b0b14] dark:via-slate-900 dark:to-brand-950">
    <div class="w-full max-w-md">
        <a href="{{ url('/') }}" class="mb-8 flex items-center justify-center gap-2.5">
            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-600 text-white dark:nl-glow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6"><path d="M2 12h3l2.5-6 4 13 3-8 1.5 4H22"/></svg>
            </span>
            <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">TIPLAO <span class="text-brand-500">DONATE</span></span>
        </a>
        <div class="nl-card-glow p-8">
            @yield('content')
        </div>
        <p class="mt-6 text-center text-xs text-slate-400">ແພລດຟອມຮັບໂດເນດສຳລັບສະຕຣີມເມີລາວ</p>
    </div>
    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
