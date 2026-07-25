@extends('layouts.app')
@section('title', 'ກະເປົາ ແລະ ຖອນເງິນ')

@section('content')
@php
    use App\Enums\WithdrawalStatus;
    use App\Support\Money;
    $hasBank = filled($streamer->bank_name) && filled($streamer->account_name) && filled($streamer->account_number);
@endphp
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="nl-card-glow p-6">
            <p class="text-sm text-slate-400">ຍອດທີ່ຖອນໄດ້</p>
            <p class="mt-2 text-3xl font-black text-emerald-500">{{ Money::format($balance) }} <span class="text-base">LAK</span></p>
            <p class="mt-2 text-xs leading-5 text-slate-400">ເປັນຍອດໂດເນດທີ່ແອັດມິນກວດແລ້ວ ຫັກຄ່າບໍລິການ ແລະ ຍອດທີ່ກຳລັງຖອນ</p>
        </div>
        <div class="nl-card p-6">
            <p class="text-sm text-slate-400">ຖອນຂັ້ນຕ່ຳ</p>
            <p class="mt-2 text-2xl font-extrabold">{{ Money::format($minimum) }} LAK</p>
            <p class="mt-2 text-xs text-slate-400">ຄ່າທຳນຽມ {{ Money::format($fee) }} LAK / ຄັ້ງ</p>
        </div>
        <div class="nl-card p-6">
            <p class="text-sm text-slate-400">ໄລຍະດຳເນີນການປົກກະຕິ</p>
            <p class="mt-2 text-2xl font-extrabold">{{ $processingDays }} ວັນເຮັດວຽກ</p>
            <a href="{{ route('page.show', 'withdrawal-terms') }}" target="_blank" class="mt-2 inline-block text-xs font-bold text-brand-500 hover:underline">ອ່ານເງື່ອນໄຂຖອນເງິນ ↗</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[.9fr_1.1fr]">
        <div class="nl-card p-6">
            <h2 class="text-lg font-bold">ສ້າງຄຳຂໍຖອນເງິນ</h2>
            <p class="mt-1 text-sm leading-6 text-slate-400">ຍອດທີ່ຂໍຖອນຈະຖືກກັນໄວ້ທັນທີ ເພື່ອບໍ່ໃຫ້ຖອນຊ້ຳ.</p>

            @if (! $hasBank)
                <div class="mt-5 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-900 dark:border-amber-700/60 dark:bg-amber-950/30 dark:text-amber-200">
                    ກະລຸນາຕື່ມຊື່ທະນາຄານ, ຊື່ບັນຊີ ແລະ ເລກບັນຊີໃນ
                    <a href="{{ route('profile.edit') }}" class="font-bold underline">ໜ້າໂປຣໄຟລ໌</a> ກ່ອນ.
                </div>
            @else
                <dl class="mt-5 space-y-2 rounded-2xl bg-slate-50 p-4 text-sm dark:bg-white/5">
                    <div class="flex justify-between gap-4"><dt class="text-slate-400">ທະນາຄານ</dt><dd class="font-semibold">{{ $streamer->bank_name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-400">ຊື່ບັນຊີ</dt><dd class="font-semibold">{{ $streamer->account_name }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-400">ເລກບັນຊີ</dt><dd class="font-mono font-semibold">{{ $streamer->account_number }}</dd></div>
                </dl>
            @endif

            <form method="POST" action="{{ route('wallet.withdraw') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="nl-label">ຈຳນວນທີ່ຈະຫັກຈາກກະເປົາ (LAK)</label>
                    <input name="amount" type="number" min="{{ $minimum }}" max="{{ max(0, $balance) }}" step="1000" value="{{ old('amount') }}" class="nl-input text-lg font-bold" required @disabled(! $hasBank || $balance < $minimum)>
                    <p class="mt-1 text-xs text-slate-400">ຈະໄດ້ຮັບສຸດທິ = ຈຳນວນຖອນ − {{ Money::format($fee) }} LAK</p>
                </div>
                <div>
                    <label class="nl-label">ໝາຍເຫດເຖິງແອັດມິນ <span class="text-slate-400">(ຖ້າມີ)</span></label>
                    <textarea name="creator_note" rows="2" maxlength="500" class="nl-input">{{ old('creator_note') }}</textarea>
                </div>
                <label class="flex items-start gap-2 text-sm leading-6">
                    <input type="checkbox" name="accept_withdrawal_terms" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" required>
                    <span>ຂ້ອຍກວດຂໍ້ມູນບັນຊີແລ້ວ ແລະ ຍອມຮັບ <a href="{{ route('page.show', 'withdrawal-terms') }}" target="_blank" class="font-bold text-brand-500 hover:underline">ເງື່ອນໄຂການຖອນເງິນ</a></span>
                </label>
                <button class="nl-btn-primary w-full" @disabled(! $hasBank || $balance < $minimum)>ຢືນຢັນຄຳຂໍຖອນເງິນ</button>
            </form>
        </div>

        <div class="nl-card overflow-hidden">
            <div class="border-b border-slate-100 px-6 py-5 dark:border-white/5">
                <h2 class="font-bold">ປະຫວັດຄຳຂໍຖອນ</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                        <tr><th class="px-5 py-3">ເລກທີ</th><th class="px-5 py-3">ຍອດ / ສຸດທິ</th><th class="px-5 py-3">ສະຖານະ</th><th class="px-5 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($withdrawals as $w)
                            @php($statusClass = match($w->status) {
                                WithdrawalStatus::Paid => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                                WithdrawalStatus::Rejected, WithdrawalStatus::Cancelled => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
                                WithdrawalStatus::Approved => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
                                default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                            })
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-bold">#{{ $w->id }}</p>
                                    <p class="text-xs text-slate-400">{{ $w->requested_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="font-semibold">{{ Money::format($w->amount) }} LAK</p>
                                    <p class="text-xs text-slate-400">ຮັບ {{ Money::format($w->net_amount) }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $w->status->label() }}</span>
                                    @if ($w->payment_reference)<p class="mt-2 text-xs text-slate-400">Ref: {{ $w->payment_reference }}</p>@endif
                                    @if ($w->admin_note)<p class="mt-1 max-w-xs text-xs text-slate-400">{{ $w->admin_note }}</p>@endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($w->status === WithdrawalStatus::Pending)
                                        <form method="POST" action="{{ route('wallet.cancel', $w) }}" onsubmit="return confirm('ຢືນຢັນຍົກເລີກຄຳຂໍ?')">
                                            @csrf @method('PATCH')
                                            <button class="text-xs font-bold text-rose-500 hover:underline">ຍົກເລີກ</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">ຍັງບໍ່ມີຄຳຂໍຖອນເງິນ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="nl-card overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-5 dark:border-white/5">
            <h2 class="font-bold">ບັນຊີລາຍການກະເປົາ</h2>
            <p class="mt-1 text-xs text-slate-400">ທຸກການເພີ່ມ, ກັນ ຫຼື ຄືນຍອດຖືກບັນທຶກໄວ້</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                    <tr><th class="px-6 py-3">ວັນທີ</th><th class="px-6 py-3">ລາຍການ</th><th class="px-6 py-3">ຈຳນວນ</th><th class="px-6 py-3">ຍອດຫຼັງລາຍການ</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($transactions as $tx)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-3 text-slate-400">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3"><p class="font-semibold">{{ $tx->type->label() }}</p><p class="text-xs text-slate-400">{{ $tx->description }}</p></td>
                            <td class="whitespace-nowrap px-6 py-3 font-bold {{ (float) $tx->amount >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">{{ (float) $tx->amount >= 0 ? '+' : '−' }}{{ Money::format(abs((float) $tx->amount)) }}</td>
                            <td class="whitespace-nowrap px-6 py-3 font-semibold">{{ Money::format($tx->balance_after) }} LAK</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">ຍັງບໍ່ມີລາຍການ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
