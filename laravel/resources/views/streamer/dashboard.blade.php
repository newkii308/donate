@extends('layouts.app')
@section('title', 'ໜ້າຫຼັກ')

@section('content')
@php
    use App\Support\Money;
    $c = $stats['currency'];
    $curLabel = $c === 'LAK' ? 'ກີບ' : $c;

    $cards = [
        ['label'=>'ມື້ນີ້',       'value'=>Money::format($stats['today_total']),  'sub'=>$stats['today_count'].' ໂດເນດ', 'grad'=>'from-sky-500/20 to-sky-500/5',     'ring'=>'text-sky-400',     'icon'=>'<path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/>'],
        ['label'=>'ເດືອນນີ້',     'value'=>Money::format($stats['month_total']),  'sub'=>$stats['month_count'].' ໂດເນດ', 'grad'=>'from-amber-500/20 to-amber-500/5', 'ring'=>'text-amber-400',   'icon'=>'<rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9h18M8 2.5v4M16 2.5v4"/>'],
        ['label'=>'ທັງໝົດ',       'value'=>Money::format($stats['total']),        'sub'=>$stats['count'].' ໂດເນດ',       'grad'=>'from-brand-500/25 to-brand-500/5', 'ring'=>'text-brand-400',   'icon'=>'<path d="M3 17l6-6 4 4 8-8"/><path d="M21 7v5h-5"/>'],
        ['label'=>'ຍອດສູງສຸດ',  'value'=>Money::format($stats['largest']),      'sub'=>'ສະເລ່ຍ '.Money::format($stats['average']).' '.$curLabel, 'grad'=>'from-emerald-500/20 to-emerald-500/5', 'ring'=>'text-emerald-400', 'icon'=>'<path d="M12 2l2.4 6.9H22l-6 4.4 2.3 7L12 16l-6.3 4.3 2.3-7-6-4.4h7.6z"/>'],
    ];
    $svg = fn($p, $cls = 'h-6 w-6') => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="'.$cls.'">'.$p.'</svg>';
    $ic = [
        'link'  => '<path d="M9 15l6-6M10.5 6.5l1-1a3.5 3.5 0 0 1 5 5l-1 1M13.5 17.5l-1 1a3.5 3.5 0 0 1-5-5l1-1"/>',
        'eye'   => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
        'check' => '<path d="M5 13l4 4L19 7"/>',
        'bell'  => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.5 21a2 2 0 0 1-3 0"/>',
        'play'  => '<path d="M7 4v16l13-8L7 4Z"/>',
        'film'  => '<rect x="2.5" y="4" width="19" height="16" rx="2"/><path d="M7 4v16M17 4v16M2.5 9h4.5M2.5 15h4.5M17 9h4.5M17 15h4.5"/>',
        'cog'   => '<circle cx="12" cy="12" r="3"/><path d="M12 2.5v3M12 18.5v3M21.5 12h-3M5.5 12h-3M18.4 5.6l-1.5 1.5M7.1 16.9l-1.5 1.5M18.4 18.4l-1.5-1.5M7.1 7.1 5.6 5.6"/>',
        'mic'   => '<rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/>',
        'image' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
    ];
@endphp

<div class="space-y-6" x-data="dashboard()">
    {{-- หัวเรื่อง --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold lg:text-2xl">ສະບາຍດີ, {{ $streamer->display_name }}! 👋</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">ມາເບິ່ງກັນວ່າມີການສະໜັບສະໜູນເທົ່າໃດແລ້ວ</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="copyLink()" class="nl-btn-ghost inline-flex items-center gap-1.5 text-sm">
                <span x-show="!copied" class="inline-flex items-center gap-1.5">{!! $svg($ic['link'], 'h-4 w-4') !!} ຄັດລອກລິ້ງ</span><span x-show="copied" x-cloak class="inline-flex items-center gap-1.5">{!! $svg($ic['check'], 'h-4 w-4') !!} ຄັດລອກແລ້ວ</span>
            </button>
            <a href="{{ route('donate.show', $streamer->username) }}" target="_blank" class="nl-btn-primary inline-flex items-center gap-1.5 text-sm">{!! $svg($ic['eye'], 'h-4 w-4') !!} ເບິ່ງໜ້າໂດເນດ</a>
        </div>
    </div>

    <a href="{{ route('wallet.index') }}" class="block rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 p-5 text-white shadow-lg transition hover:-translate-y-0.5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-100">ຍອດທີ່ຖອນໄດ້</p>
                <p class="mt-1 text-2xl font-black">{{ Money::format($walletBalance) }} LAK</p>
                <p class="mt-1 text-xs text-emerald-100">ສະເພາະຍອດທີ່ແອັດມິນກວດແລ້ວ ຫັກຍອດທີ່ກຳລັງຖອນ</p>
            </div>
            <span class="shrink-0 rounded-xl bg-white/15 px-4 py-2 text-sm font-bold">ຖອນເງິນ →</span>
        </div>
    </a>

    {{-- การ์ดสถิติ --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($cards as $card)
            <div class="nl-card-glow relative overflow-hidden p-5">
                <div class="absolute inset-0 bg-gradient-to-br {{ $card['grad'] }}"></div>
                <div class="relative">
                    <div class="flex items-start justify-between">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                        <span class="{{ $card['ring'] }}">{!! $svg($card['icon']) !!}</span>
                    </div>
                    <p class="mt-2 text-2xl font-extrabold lg:text-3xl">{{ $card['value'] }} <span class="text-base font-semibold text-slate-400">{{ $curLabel }}</span></p>
                    <p class="mt-1 text-xs text-slate-400">{{ $card['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- กราฟ 7 วัน --}}
        <div class="nl-card p-6 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">ລາຍຮັບ 7 ມື້ລ່າສຸດ</h3>
                <span class="nl-chip">{{ $curLabel }}</span>
            </div>
            @php $max = max(1, collect($chart)->max('total')); @endphp
            <div class="mt-6 flex h-48 items-end gap-2.5">
                @foreach ($chart as $day)
                    <div class="group flex flex-1 flex-col items-center gap-2">
                        <span class="text-[10px] font-semibold text-slate-400 opacity-0 transition group-hover:opacity-100">{{ Money::format($day['total']) }}</span>
                        <div class="flex w-full items-end" style="height: 150px;">
                            <div class="w-full rounded-t-lg bg-gradient-to-t from-brand-600 to-brand-400 transition-all duration-500 group-hover:from-brand-500 group-hover:to-brand-300"
                                 style="height: {{ max(6, ($day['total'] / $max) * 150) }}px"></div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ ['Sun'=>'ອາ','Mon'=>'ຈ','Tue'=>'ອ','Wed'=>'ພ','Thu'=>'ພຫ','Fri'=>'ສ','Sat'=>'ສງ'][\Illuminate\Support\Carbon::parse($day['date'])->format('D')] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ทดสอบแจ้งเตือน --}}
        <div class="nl-card p-6" x-data="testNotification()">
            <h3 class="inline-flex items-center gap-2 font-semibold">{!! $svg($ic['bell'], 'h-5 w-5 text-brand-500') !!} ທົດສອບແຈ້ງເຕືອນ</h3>
            <p class="mt-1 text-xs text-slate-400">ສະແດງໃນ Overlay ຈິງ ໂດຍບໍ່ບັນທຶກເປັນໂດເນດ</p>
            <form @submit.prevent="send" class="mt-4 space-y-3">
                <input x-model="form.donor_name" class="nl-input" placeholder="ຊື່ຜູ້ໂດເນດ" required>
                <input x-model="form.amount" type="number" min="1000" step="1000" class="nl-input" placeholder="ຈຳນວນເງິນ" required>
                <input x-model="form.message" class="nl-input" placeholder="ຂໍ້ຄວາມ (ຖ້າມີ)">
                <button class="nl-btn-primary inline-flex w-full items-center justify-center gap-1.5" :disabled="loading">
                    <span x-show="!loading" class="inline-flex items-center gap-1.5">{!! $svg($ic['play'], 'h-4 w-4') !!} ສົ່ງທົດສອບ</span>
                    <span x-show="loading" x-cloak>ກຳລັງສົ່ງ…</span>
                </button>
                <p x-show="result" x-text="result" x-cloak class="text-center text-xs text-emerald-500"></p>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- การโดเนทล่าสุด --}}
        <div class="nl-card overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between px-6 py-4">
                <h3 class="font-semibold">ການໂດເນດລ່າສຸດ</h3>
                <a href="{{ route('donations.index') }}" class="text-sm font-medium text-brand-500 hover:underline">ເບິ່ງທັງໝົດ →</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($recent as $d)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-600/15 text-sm font-bold text-brand-400">
                            {{ mb_strtoupper(mb_substr($d->displayName(), 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $d->displayName() }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $d->message ?: '—' }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="font-bold text-brand-500">{{ Money::format($d->amount) }} {{ $curLabel }}</p>
                            <p class="text-[11px] text-slate-400">{{ $d->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-sm text-slate-400">ຍັງບໍ່ມີໂດເນດ — ແຊຣ໌ລິ້ງໜ້າໂດເນດຂອງທ່ານເພື່ອເລີ່ມຕົ້ນ! 💜</div>
                @endforelse
            </div>
        </div>

        {{-- วิดเจ็ต Overlay --}}
        <div class="nl-card p-6">
            <h3 class="inline-flex items-center gap-2 font-semibold">{!! $svg($ic['film'], 'h-5 w-5 text-brand-500') !!} ວິດເຈັດ Overlay</h3>
            <p class="mt-1 text-xs text-slate-400">ລິ້ງສຳລັບໃສ່ໃນ OBS (Browser Source)</p>
            <div class="mt-3 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-white/10 dark:bg-white/5">
                <span class="nl-live-dot inline-block h-2 w-2 shrink-0 rounded-full bg-rose-500"></span>
                <input readonly :value="overlayUrl" class="min-w-0 flex-1 bg-transparent font-mono text-xs outline-none" x-ref="ov">
                <button @click="copyOverlay()" class="shrink-0 text-xs font-semibold text-brand-500 hover:underline">
                    <span x-show="!ovCopied">ຄັດລອກ</span><span x-show="ovCopied" x-cloak>✓</span>
                </button>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('overlay-settings.edit') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium transition hover:border-brand-400 hover:bg-brand-50 dark:border-white/10 dark:hover:bg-white/5">
                    <span class="inline-flex items-center gap-2">{!! $svg($ic['cog'], 'h-4 w-4 text-slate-400') !!} ຕັ້ງຄ່າໜ້າຕາ ແລະ ທີມ</span><span class="text-slate-400">›</span>
                </a>
                <a href="{{ route('overlay-settings.edit') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium transition hover:border-brand-400 hover:bg-brand-50 dark:border-white/10 dark:hover:bg-white/5">
                    <span class="inline-flex items-center gap-2">{!! $svg($ic['mic'], 'h-4 w-4 text-slate-400') !!} ຕັ້ງຄ່າສຽງ ແລະ TTS</span><span class="text-slate-400">›</span>
                </a>
                <a href="{{ route('media.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-medium transition hover:border-brand-400 hover:bg-brand-50 dark:border-white/10 dark:hover:bg-white/5">
                    <span class="inline-flex items-center gap-2">{!! $svg($ic['image'], 'h-4 w-4 text-slate-400') !!} ຈັດການຄັງສື່</span><span class="text-slate-400">›</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboard() {
        return {
            copied: false, ovCopied: false,
            donateUrl: @js(route('donate.show', $streamer->username)),
            overlayUrl: @js(route('overlay.show', $streamer->overlay_key)),
            copyLink() { navigator.clipboard.writeText(this.donateUrl); this.copied = true; setTimeout(() => this.copied = false, 2000); },
            copyOverlay() { navigator.clipboard.writeText(this.overlayUrl); this.ovCopied = true; setTimeout(() => this.ovCopied = false, 2000); },
        };
    }

    function testNotification() {
        return {
            loading: false, result: '',
            form: { donor_name: 'ຜູ້ທົດສອບ', amount: 50000, message: 'ເປັນກຳລັງໃຈໃຫ້ ສູ້ໆ!' },
            async send() {
                this.loading = true; this.result = '';
                try {
                    const res = await fetch(@js(route('test-notification.store')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json();
                    this.result = data.message || (res.ok ? '✓ ສົ່ງແລ້ວ!' : 'ສົ່ງບໍ່ສຳເລັດ');
                } catch (e) { this.result = 'ສົ່ງບໍ່ສຳເລັດ'; }
                this.loading = false;
            },
        };
    }
</script>
@endsection
