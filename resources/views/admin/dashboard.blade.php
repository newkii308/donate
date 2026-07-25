@extends('layouts.app')
@section('title', 'ໜ້າຫຼັກ (ຜູ້ດູແລລະບົບ)')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([
            ['ຍອດລວມທັງໝົດ', \App\Support\Money::format($stats['total_amount']).' ກີບ'],
            ['ຈຳນວນໂດເນດ', number_format($stats['total_count'])],
            ['ມື້ນີ້', \App\Support\Money::format($stats['today_amount']).' ກີບ'],
            ['ສະຕຣີມເມີ', number_format($stats['streamers'])],
            ['ກຳລັງໃຊ້ງານ', number_format($stats['active_streamers'])],
            ['ລໍກວດຍອດ', number_format($stats['pending_donations']).' ລາຍການ'],
            ['ໜີ້ສິນກະເປົາ', \App\Support\Money::format($stats['wallet_liability']).' ກີບ'],
            ['ຄຳຂໍຖອນທີ່ເປີດ', number_format($stats['pending_withdrawals']).' ລາຍການ'],
        ] as $card)
            <div class="nl-card-glow p-5">
                <p class="text-xs font-medium text-slate-400">{{ $card[0] }}</p>
                <p class="mt-2 text-2xl font-extrabold">{{ $card[1] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="nl-card overflow-hidden">
            <h3 class="px-6 py-4 font-semibold">ການໂດເນດລ່າສຸດ</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($recentDonations as $d)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $d->displayName() }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ $d->streamer?->display_name }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-brand-500">{{ \App\Support\Money::format($d->amount) }} {{ $d->currency }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-8 text-center text-slate-400">ຍັງບໍ່ມີໂດເນດ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="nl-card overflow-hidden">
            <h3 class="px-6 py-4 font-semibold">ກິດຈະກຳລ່າສຸດ</h3>
            <ul class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($recentActivity as $log)
                    <li class="px-6 py-3 text-sm">
                        <p class="font-medium">{{ $log->description ?: $log->action }}</p>
                        <p class="text-xs text-slate-400">{{ $log->user?->name ?? 'ລະບົບ' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-slate-400">ຍັງບໍ່ມີບັນທຶກກິດຈະກຳ</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
