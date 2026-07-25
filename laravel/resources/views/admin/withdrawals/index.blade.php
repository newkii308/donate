@extends('layouts.app')
@section('title', 'ຄຳຂໍຖອນເງິນ')

@section('content')
@php
    use App\Enums\WithdrawalStatus;
    use App\Support\Money;
@endphp
<div class="space-y-6">
    <div class="nl-card p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-bold">ຈັດການການຈ່າຍໃຫ້ຄຣີເອເຕີ</h2>
                <p class="mt-1 text-sm leading-6 text-slate-400">ກວດຕົວຕົນ, ຍອດກະເປົາ ແລະ ຂໍ້ມູນບັນຊີກ່ອນອະນຸມັດ. ຫຼັງໂອນຈິງຕ້ອງບັນທຶກເລກອ້າງອີງ.</p>
            </div>
            <form method="GET" class="flex gap-2">
                <select name="status" class="nl-input min-w-48">
                    <option value="">ທຸກສະຖານະ</option>
                    @foreach ($statuses as $item)<option value="{{ $item->value }}" @selected($status === $item->value)>{{ $item->label() }}</option>@endforeach
                </select>
                <button class="nl-btn-primary">ກັ່ນຕອງ</button>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($withdrawals as $w)
            @php($statusClass = match($w->status) {
                WithdrawalStatus::Paid => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                WithdrawalStatus::Rejected, WithdrawalStatus::Cancelled => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
                WithdrawalStatus::Approved => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
                default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            })
            <article class="nl-card p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-lg font-black">#{{ $w->id }}</span>
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $w->status->label() }}</span>
                    <span class="text-xs text-slate-400">{{ $w->requested_at->format('d/m/Y H:i:s') }}</span>
                </div>

                <div class="mt-5 grid gap-5 xl:grid-cols-[1fr_1fr_1.1fr]">
                    <section>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">ຄຣີເອເຕີ</p>
                        <p class="mt-2 font-bold">{{ $w->streamer?->display_name }}</p>
                        <p class="text-sm text-slate-400">{{ $w->streamer?->user?->email }}</p>
                        @if ($w->creator_note)<p class="mt-3 rounded-xl bg-slate-50 p-3 text-sm dark:bg-white/5">{{ $w->creator_note }}</p>@endif
                    </section>
                    <section>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">ບັນຊີຮັບເງິນ (ສຳເນົາຕອນຍື່ນ)</p>
                        <dl class="mt-2 space-y-1.5 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-slate-400">ທະນາຄານ</dt><dd class="font-semibold">{{ $w->bank_name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-slate-400">ຊື່ບັນຊີ</dt><dd class="font-semibold">{{ $w->account_name }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-slate-400">ເລກບັນຊີ</dt><dd class="font-mono font-bold">{{ $w->account_number }}</dd></div>
                        </dl>
                    </section>
                    <section class="rounded-2xl bg-slate-50 p-4 dark:bg-white/5">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div><p class="text-xs text-slate-400">ຫັກກະເປົາ</p><p class="mt-1 font-bold">{{ Money::format($w->amount) }}</p></div>
                            <div><p class="text-xs text-slate-400">ຄ່າທຳນຽມ</p><p class="mt-1 font-bold">{{ Money::format($w->fee) }}</p></div>
                            <div><p class="text-xs text-slate-400">ໂອນສຸດທິ</p><p class="mt-1 font-black text-emerald-500">{{ Money::format($w->net_amount) }}</p></div>
                        </div>
                        @if ($w->payment_reference)<p class="mt-3 border-t border-slate-200 pt-3 text-xs dark:border-white/10">ເລກອ້າງອີງ: <strong class="font-mono">{{ $w->payment_reference }}</strong></p>@endif
                        @if ($w->admin_note)<p class="mt-2 text-xs text-slate-400">{{ $w->admin_note }}</p>@endif
                    </section>
                </div>

                @if ($w->status === WithdrawalStatus::Pending)
                    <div class="mt-5 grid gap-3 border-t border-slate-100 pt-5 sm:grid-cols-2 dark:border-white/5">
                        <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" class="flex gap-2" onsubmit="return confirm('ຢືນຢັນວ່າກວດສອບຄຳຂໍແລ້ວ?')">
                            @csrf @method('PATCH')
                            <input name="admin_note" class="nl-input flex-1" maxlength="1000" placeholder="ໝາຍເຫດ (ຖ້າມີ)">
                            <button class="nl-btn-primary">ອະນຸມັດ</button>
                        </form>
                        <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}" class="flex gap-2" onsubmit="return confirm('ປະຕິເສດແລະຄືນຍອດໃຫ້ຄຣີເອເຕີ?')">
                            @csrf @method('PATCH')
                            <input name="admin_note" class="nl-input flex-1" maxlength="1000" required placeholder="ເຫດຜົນທີ່ປະຕິເສດ">
                            <button class="nl-btn-ghost text-rose-500">ປະຕິເສດ</button>
                        </form>
                    </div>
                @elseif ($w->status === WithdrawalStatus::Approved)
                    <form method="POST" action="{{ route('admin.withdrawals.paid', $w) }}" class="mt-5 grid gap-3 border-t border-slate-100 pt-5 sm:grid-cols-[1fr_1fr_auto] dark:border-white/5" onsubmit="return confirm('ຢືນຢັນວ່າໄດ້ໂອນຈິງແລ້ວ?')">
                        @csrf @method('PATCH')
                        <input name="payment_reference" class="nl-input font-mono" maxlength="120" required placeholder="ເລກອ້າງອີງການໂອນ *">
                        <input name="admin_note" class="nl-input" maxlength="1000" placeholder="ໝາຍເຫດ (ຖ້າມີ)">
                        <button class="nl-btn-primary">ບັນທຶກວ່າໂອນແລ້ວ</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="nl-card px-6 py-12 text-center text-slate-400">ຍັງບໍ່ມີຄຳຂໍຖອນເງິນ</div>
        @endforelse
    </div>

    {{ $withdrawals->links() }}
</div>
@endsection
