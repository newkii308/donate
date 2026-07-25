<!DOCTYPE html>
<html lang="lo" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName }} — ປິດປັບປຸງຊົ່ວຄາວ</title>
    <script>document.documentElement.classList.add('dark');</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:400,600,800" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="grid min-h-full place-items-center bg-[#0b0b14] px-6 text-center text-slate-100" style="font-family:'Noto Sans Lao',sans-serif">
    <div class="max-w-md">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-brand-600 text-white">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8">
                <path d="M12 2a10 10 0 1 0 10 10M12 6v6l4 2"/>
            </svg>
        </div>
        <h1 class="mt-6 text-2xl font-extrabold">ປິດປັບປຸງຊົ່ວຄາວ</h1>
        <p class="mt-3 text-slate-400">{{ $message }}</p>
        <p class="mt-8 text-xs text-slate-600">{{ $siteName }}</p>
    </div>
</body>
</html>
