@extends('layouts.app')
@section('title', 'ກວດສອບເງິນໂດເນດ')

@section('content')
@php
    use App\Enums\DonationStatus;
    use App\Support\Money;
@endphp
<div class="space-y-6">
    <div class="nl-card p-6">
        <div class="mb-5">
            <h2 class="text-lg font-bold">ກວດລາຍການກັບບັນຊີກາງ</h2>
            <p class="mt-1 text-sm leading-6 text-slate-400">ກົດຢືນຢັນສະເພາະເມື່ອຈຳນວນ ແລະ ເລກອ້າງອີງກົງກັບຍອດເຂົ້າຈິງ. ການຢືນຢັນຈະເພີ່ມຍອດຄຣີເອເຕີ ແລະ ສົ່ງແຈ້ງເຕືອນ OBS.</p>
        </div>
        <form method="GET" class="grid gap-3 sm:grid-cols-[1fr_13rem_auto_auto]">
            <input name="search" value="{{ $search }}" class="nl-input" placeholder="ຊື່, ຂໍ້ຄວາມ ຫຼື ເລກອ້າງອີງ">
            <select name="status" class="nl-input">
                <option value="">ທຸກສະຖານະ</option>
                @foreach (DonationStatus::cases() as $item)<option value="{{ $item->value }}" @selected($status === $item->value)>{{ $item->label() }}</option>@endforeach
            </select>
            <button class="nl-btn-primary">ຄົ້ນຫາ</button>
            <a href="{{ route('admin.donations.index') }}" class="nl-btn-ghost text-center">ລ້າງຄ່າ</a>
        </form>
    </div>

    <div class="space-y-4">
        @forelse ($donations as $d)
            @php($statusClass = match($d->status) {
                DonationStatus::Completed => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                DonationStatus::Rejected, DonationStatus::Failed => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
                default => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
            })
            <article class="nl-card p-5">
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-start">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-lg font-black">#{{ $d->id }}</span>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $d->status->label() }}</span>
                            <span class="text-xs text-slate-400">{{ $d->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2 xl:grid-cols-4">
                            <div><p class="text-xs text-slate-400">ຄຣີເອເຕີ</p><p class="mt-1 font-semibold">{{ $d->streamer?->display_name }}</p></div>
                            <div><p class="text-xs text-slate-400">ຜູ້ແຈ້ງໂອນ</p><p class="mt-1 font-semibold">{{ $d->displayName() }}</p></div>
                            <div><p class="text-xs text-slate-400">ຈຳນວນ</p><p class="mt-1 text-lg font-black text-brand-500">{{ Money::format($d->amount) }} LAK</p></div>
                            <div><p class="text-xs text-slate-400">ເລກອ້າງອີງ</p><p class="mt-1 break-all font-mono font-bold">{{ $d->transfer_reference ?: 'ລາຍການເກົ່າ' }}</p></div>
                        </div>
                        @if ($d->message)<p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm dark:bg-white/5">“{{ $d->message }}”</p>@endif
                        @if ($d->status === DonationStatus::Completed)
                            <p class="mt-3 text-xs text-slate-400">ຄ່າບໍລິການ {{ Money::format($d->platform_fee) }} · ເຂົ້າກະເປົາ {{ Money::format($d->net_amount) }} LAK · ກວດເມື່ອ {{ $d->verified_at?->format('d/m/Y H:i') }}</p>
                        @elseif ($d->rejection_reason)
                            <p class="mt-3 text-sm text-rose-500">ເຫດຜົນ: {{ $d->rejection_reason }}</p>
                        @endif
                    </div>

                    @if ($d->status === DonationStatus::Pending)
                        <div class="flex min-w-64 flex-col gap-3">
                            <form method="POST" action="{{ route('admin.donations.verify', $d) }}" onsubmit="return confirm('ກວດແລ້ວວ່າຍອດເຂົ້າບັນຊີກາງຈິງ?')">
                                @csrf @method('PATCH')
                                <button class="nl-btn-primary w-full">✓ ຢືນຢັນຮັບເງິນ</button>
                            </form>
                            <form method="POST" action="{{ route('admin.donations.reject', $d) }}" class="space-y-2" onsubmit="return confirm('ຢືນຢັນປະຕິເສດລາຍການ?')">
                                @csrf @method('PATCH')
                                <input name="rejection_reason" class="nl-input text-sm" maxlength="1000" required placeholder="ເຫດຜົນທີ່ປະຕິເສດ">
                                <button class="nl-btn-ghost w-full text-rose-500">ປະຕິເສດ</button>
                            </form>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="nl-card px-6 py-12 text-center text-slate-400">ບໍ່ພົບລາຍການໂດເນດ</div>
        @endforelse
    </div>

    {{ $donations->links() }}
</div>
@endsection
