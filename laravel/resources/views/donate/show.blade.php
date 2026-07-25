@php
    use App\Support\Money;
    $theme = $page?->theme ?? 'dark';
    $accent = $page?->accent_color ?? '#7c3aed';
    $quick = $page?->quick_amounts ?? [10000, 20000, 50000, 100000, 200000];
    $accepts = $centralAccount['enabled'] && (filled($centralAccount['qr_url']) || filled($centralAccount['account_number']));
@endphp
<!DOCTYPE html>
<html lang="lo" class="h-full {{ $theme === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ສະໜັບສະໜູນ {{ $streamer->display_name }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans-lao:300,400,500,600,700,800" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>:root{--accent:{{ $accent }}}body{font-family:'Noto Sans Lao',sans-serif}</style>
</head>
<body class="min-h-full bg-slate-100 text-slate-900 dark:bg-[#0b0b14] dark:text-slate-100">
<div class="pointer-events-none fixed inset-0 hidden dark:block" aria-hidden="true">
    <div class="absolute -top-32 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full opacity-30 blur-3xl" style="background:var(--accent)"></div>
</div>

<div class="relative mx-auto grid min-h-full max-w-5xl gap-6 px-4 py-6 lg:grid-cols-2 lg:items-start lg:py-10">
    <div class="space-y-6">
        <div class="nl-card-glow overflow-hidden text-center">
            <div class="h-24 w-full" style="background:linear-gradient(120deg,var(--accent),transparent)"></div>
            <div class="-mt-12 px-6 pb-6">
                <div class="mx-auto h-24 w-24 overflow-hidden rounded-full border-4 border-white bg-slate-200 dark:border-[#0b0b14]">
                    @if ($streamer->avatarUrl())
                        <img src="{{ $streamer->avatarUrl() }}" alt="{{ $streamer->display_name }}" class="h-full w-full object-cover">
                    @else
                        <div class="grid h-full w-full place-items-center text-3xl font-black text-slate-400">{{ mb_strtoupper(mb_substr($streamer->display_name, 0, 1)) }}</div>
                    @endif
                </div>
                <h1 class="mt-3 text-2xl font-extrabold">{{ $streamer->display_name }} <span style="color:var(--accent)">💜</span></h1>
                @if ($streamer->description)<p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $streamer->description }}</p>@endif
            </div>
        </div>

        <div class="nl-card p-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-500 text-lg">🇱🇦</span>
                <div>
                    <h2 class="font-bold">ຊ່ອງທາງຊຳລະເງິນ</h2>
                    <p class="text-xs text-slate-400">ໂດເນດເພື່ອສະໜັບສະໜູນ {{ $streamer->display_name }}</p>
                </div>
            </div>

            @if ($accepts)
                @if ($centralAccount['qr_url'])
                    <div class="mt-5 flex justify-center">
                        <img src="{{ $centralAccount['qr_url'] }}" alt="Lao QR ສຳລັບໂດເນດ" class="h-64 w-64 rounded-2xl bg-white object-contain p-3 shadow-sm">
                    </div>
                    <p class="mt-3 text-center text-sm font-medium">ສະແກນ Lao QR ດ້ວຍແອັບທະນາຄານຂອງທ່ານ</p>
                @endif

                <dl class="mt-5 space-y-3 rounded-2xl bg-slate-100 p-4 text-sm dark:bg-white/5">
                    @if ($centralAccount['bank_name'])<div class="flex justify-between gap-4"><dt class="text-slate-500">ທະນາຄານ</dt><dd class="text-right font-semibold">{{ $centralAccount['bank_name'] }}</dd></div>@endif
                    @if ($centralAccount['account_name'])<div class="flex justify-between gap-4"><dt class="text-slate-500">ຊື່ບັນຊີ</dt><dd class="text-right font-semibold">{{ $centralAccount['account_name'] }}</dd></div>@endif
                    @if ($centralAccount['account_number'])
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-slate-500">ເລກບັນຊີ</dt>
                            <dd class="flex items-center gap-2 text-right font-bold">
                                <span>{{ $centralAccount['account_number'] }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText(@js($centralAccount['account_number']));this.textContent='✓';setTimeout(()=>this.textContent='ຄັດລອກ',1200)" class="rounded-lg px-2 py-1 text-xs text-white" style="background:var(--accent)">ຄັດລອກ</button>
                            </dd>
                        </div>
                    @endif
                </dl>

                <ol class="mt-5 space-y-2.5 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <li><strong>1.</strong> ສະແກນ Lao QR ຫຼື ໂອນເຂົ້າບັນຊີດ້ານເທິງ</li>
                    <li><strong>2.</strong> ນຳເລກອ້າງອີງຈາກຫຼັກຖານການໂອນມາປ້ອນໃນຟອມ</li>
                    <li><strong>3.</strong> ສົ່ງລາຍການ ແລະ ລໍຖ້າລະບົບກວດຢືນຢັນການຊຳລະ</li>
                </ol>
                <p class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-400">
                    ກະລຸນາກວດຊື່ບັນຊີ ແລະ ຈຳນວນເງິນກ່ອນໂອນ. ການສົ່ງຟອມຈະສຳເລັດຫຼັງກວດພົບການຊຳລະແລ້ວ.
                </p>
            @else
                <div class="mt-5 rounded-2xl bg-slate-100 p-5 text-center dark:bg-white/5">
                    <p class="text-3xl">🚧</p>
                    <p class="mt-2 font-bold">ຊ່ອງທາງຊຳລະຍັງບໍ່ພ້ອມ</p>
                    <p class="mt-1 text-sm text-slate-400">ກະລຸນາກັບມາໃໝ່ອີກຄັ້ງ</p>
                </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-center text-sm font-medium leading-6 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-center text-sm font-medium text-rose-800 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-300">⚠️ {{ session('error') }}</div>
        @endif

        @if ($accepts)
            <div class="nl-card-glow p-6" x-data="{anonymous:{{ old('is_anonymous') ? 'true' : 'false' }},amount:@js(old('amount', ''))}">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold">ແຈ້ງການໂອນ</h2>
                    <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">ກວດການຊຳລະ</span>
                </div>

                @if ($errors->any())
                    <div class="mt-3 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                        <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('donate.store', $streamer->username) }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="nl-label" for="donor_name">ຊື່ຜູ້ໂດເນດ</label>
                        <input id="donor_name" name="donor_name" value="{{ old('donor_name') }}" maxlength="60" class="nl-input" placeholder="ປ້ອນຊື່ຂອງທ່ານ" :disabled="anonymous" :class="anonymous&&'opacity-50'">
                    </div>
                    <div>
                        <label class="nl-label" for="amount">ຈຳນວນເງິນທີ່ໂອນ (ກີບ)</label>
                        <div class="relative">
                            <input id="amount" name="amount" type="number" min="{{ $page?->min_amount ?? 1000 }}" max="{{ $page?->max_amount ?? config('newlab.donation.max_amount') }}" step="1000" x-model="amount" required class="nl-input pr-16 text-lg font-bold" placeholder="50,000">
                            <span class="absolute inset-y-0 right-4 grid place-items-center text-sm text-slate-400">ກີບ</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($quick as $q)<button type="button" @click="amount={{ (float) $q }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold hover:border-brand-400 dark:border-white/10">{{ number_format($q) }}</button>@endforeach
                        </div>
                    </div>
                    <div>
                        <label class="nl-label" for="transfer_reference">ເລກອ້າງອີງການໂອນ <span class="text-rose-500">*</span></label>
                        <input id="transfer_reference" name="transfer_reference" value="{{ old('transfer_reference') }}" maxlength="120" required class="nl-input font-mono" placeholder="ຕົວຢ່າງ: 123456789012">
                        <p class="mt-1 text-xs text-slate-400">ຄັດລອກຈາກໃບຢືນຢັນໃນແອັບທະນາຄານ ແລະ ເກັບຫຼັກຖານໄວ້</p>
                    </div>
                    <div>
                        <label class="nl-label" for="message">ຂໍ້ຄວາມຫາຄຣີເອເຕີ <span class="text-slate-400">(ຖ້າມີ)</span></label>
                        <textarea id="message" name="message" rows="3" maxlength="255" class="nl-input" placeholder="ພິມຂໍ້ຄວາມໃຫ້ກຳລັງໃຈ...">{{ old('message') }}</textarea>
                    </div>
                    @if ($page?->allow_anonymous ?? true)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_anonymous" value="1" x-model="anonymous" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            ໂດເນດແບບບໍ່ລະບຸຊື່
                        </label>
                    @endif
                    <button type="submit" class="nl-btn w-full py-3.5 text-base font-bold text-white shadow-lg" style="background:var(--accent)">ສົ່ງລາຍການໃຫ້ກວດສອບ</button>
                    <p class="text-center text-xs leading-5 text-slate-400">ແຈ້ງເຕືອນຈະຂຶ້ນໃນໄລຟ໌ຫຼັງຈາກກວດການຊຳລະສຳເລັດ</p>
                </form>
            </div>
        @endif

        @if ($recent->isNotEmpty())
            <div class="nl-card p-6">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">ຜູ້ສະໜັບສະໜູນທີ່ກວດສອບແລ້ວ</h2>
                <ul class="mt-3 divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($recent as $d)
                        <li class="flex items-center justify-between gap-3 py-2.5">
                            <div class="min-w-0"><p class="truncate font-semibold">{{ $d->displayName() }}</p>@if ($d->message)<p class="truncate text-xs text-slate-400">{{ $d->message }}</p>@endif</div>
                            <span class="shrink-0 font-bold" style="color:var(--accent)">{{ Money::format($d->amount) }} ກີບ</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<footer class="relative pb-6 text-center text-xs text-slate-400">
    ຂັບເຄື່ອນໂດຍ <span class="font-semibold">{{ config('app.name') }}</span>
    · <a href="{{ route('page.show', 'withdrawal-terms') }}" class="hover:text-brand-500">ເງື່ອນໄຂຖອນເງິນ</a>
</footer>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
