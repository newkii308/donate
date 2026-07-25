@extends('layouts.app')
@section('title', 'ປະຫວັດການໂດເນດ')

@section('content')
@php
    use App\Enums\DonationStatus;
@endphp
<div class="space-y-6">
    <div class="nl-card p-6">
        <form method="GET" action="{{ route('donations.index') }}" class="grid gap-3 sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="nl-label">ຄົ້ນຫາ</label>
                <input name="search" value="{{ $search }}" class="nl-input" placeholder="ຊື່ຜູ້ໂດເນດ ຫຼື ຂໍ້ຄວາມ">
            </div>
            <div>
                <label class="nl-label">ຕັ້ງແຕ່ວັນທີ</label>
                <input name="from" type="date" value="{{ $from }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">ຮອດວັນທີ</label>
                <input name="to" type="date" value="{{ $to }}" class="nl-input">
            </div>
            <div class="flex gap-2 sm:col-span-4">
                <button class="nl-btn-primary">🔍 ກັ່ນຕອງ</button>
                <a href="{{ route('donations.index') }}" class="nl-btn-ghost">ລ້າງຄ່າ</a>
                <a href="{{ route('donations.export', request()->query()) }}" class="nl-btn-ghost ml-auto">⬇ ສົ່ງອອກ CSV</a>
            </div>
        </form>
    </div>

    <div class="nl-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                    <tr>
                        <th class="px-6 py-3">ວັນທີ</th>
                        <th class="px-6 py-3">ຜູ້ໂດເນດ</th>
                        <th class="px-6 py-3">ຈຳນວນ</th>
                        <th class="px-6 py-3">ຂໍ້ຄວາມ</th>
                        <th class="px-6 py-3">ສະຖານະ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($donations as $d)
                        @php($statusClass = match($d->status) {
                            DonationStatus::Completed => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                            DonationStatus::Rejected, DonationStatus::Failed => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
                            default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                        })
                        <tr>
                            <td class="whitespace-nowrap px-6 py-3 text-slate-500">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 font-medium">
                                {{ $d->displayName() }}
                                @if ($d->is_anonymous)<span class="ml-1 text-[10px] text-slate-400">(ບໍ່ລະບຸຊື່)</span>@endif
                            </td>
                            <td class="px-6 py-3 font-semibold text-brand-500">
                                {{ \App\Support\Money::format($d->amount) }} {{ $d->currency }}
                                @if ($d->status === DonationStatus::Completed)<p class="text-[10px] font-normal text-slate-400">ເຂົ້າກະເປົາ {{ \App\Support\Money::format($d->net_amount) }}</p>@endif
                            </td>
                            <td class="max-w-xs truncate px-6 py-3 text-slate-500">{{ $d->message ?: '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $statusClass }}">{{ $d->status->label() }}</span>
                                @if ($d->rejection_reason)<p class="mt-1 max-w-xs text-xs text-rose-500">{{ $d->rejection_reason }}</p>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">ບໍ່ພົບລາຍການໂດເນດ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $donations->links() }}
</div>
@endsection
