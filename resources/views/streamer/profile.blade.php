@extends('layouts.app')
@section('title', 'ໂປຣໄຟລ໌')

@section('content')
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-6">
    @csrf @method('PUT')

    <div class="nl-card p-6">
        <h3 class="font-semibold">ໂປຣໄຟລ໌ສາທາລະນະ</h3>
        <p class="text-sm text-slate-400">ຂໍ້ມູນນີ້ຈະສະແດງຢູ່ໜ້າໂດເນດຂອງທ່ານ</p>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຊື່ສະແດງ</label>
                <input name="display_name" value="{{ old('display_name', $streamer->display_name) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ຊື່ຜູ້ໃຊ້ (ສຳລັບລິ້ງ)</label>
                <input name="username" value="{{ old('username', $streamer->username) }}" class="nl-input" required>
            </div>
            <div class="sm:col-span-2">
                <label class="nl-label">ຄຳອະທິບາຍ</label>
                <textarea name="description" rows="3" class="nl-input" placeholder="ແນະນຳຕົວສັ້ນໆ ໃຫ້ຜູ້ສະໜັບສະໜູນ">{{ old('description', $streamer->description) }}</textarea>
            </div>
            <div>
                <label class="nl-label">ສະກຸນເງິນ</label>
                <select name="currency" class="nl-input">
                    @foreach ($currencies as $cur)
                        <option value="{{ $cur }}" @selected(old('currency', $streamer->currency) === $cur)>{{ $cur }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Lao bank transfer details --}}
    <div class="nl-card-glow p-6">
        <div class="flex items-center gap-2">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-500 text-lg">🇱🇦</span>
            <div>
                <h3 class="font-semibold">ບັນຊີຮັບເງິນຖອນ</h3>
                <p class="text-xs text-slate-400">ຂໍ້ມູນນີ້ໃຊ້ສຳລັບໃຫ້ TIPLAO ໂອນຍອດຖອນໃຫ້ທ່ານ ແລະ ຈະບໍ່ສະແດງໃນໜ້າໂດເນດສາທາລະນະ</p>
            </div>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຊ່ອງທາງຮັບເງິນຖອນ</label>
                <input name="payment_method" value="{{ old('payment_method', $streamer->payment_method) }}" class="nl-input" placeholder="Lao QR / ໂອນຜ່ານທະນາຄານ">
            </div>
            <div>
                <label class="nl-label">ຊື່ທະນາຄານ</label>
                <input name="bank_name" value="{{ old('bank_name', $streamer->bank_name) }}" class="nl-input" placeholder="ຕົວຢ່າງ: BCEL, LDB, JDB">
            </div>
            <div>
                <label class="nl-label">ຊື່ບັນຊີ</label>
                <input name="account_name" value="{{ old('account_name', $streamer->account_name) }}" class="nl-input">
            </div>
            <div>
                <label class="nl-label">ເລກບັນຊີ</label>
                <input name="account_number" value="{{ old('account_number', $streamer->account_number) }}" class="nl-input">
            </div>
        </div>
    </div>

    <div class="nl-card p-6">
        <h3 class="font-semibold">ຮູບພາບ</h3>
        <div class="mt-5 grid gap-6 sm:grid-cols-2">
            <div>
                <label class="nl-label">ຮູບໂປຣໄຟລ໌ (Avatar)</label>
                @if ($streamer->avatarUrl())
                    <img src="{{ $streamer->avatarUrl() }}" class="mb-3 h-20 w-20 rounded-full object-cover">
                @endif
                <input type="file" name="avatar" accept="image/*" class="nl-input">
            </div>
            <div>
                <label class="nl-label">Lao QR ສຳລັບຮັບຍອດຖອນ (ຖ້າມີ)</label>
                @if ($streamer->qrCodeUrl())
                    <img src="{{ $streamer->qrCodeUrl() }}" class="mb-3 h-20 w-20 rounded-xl object-contain">
                @endif
                <input type="file" name="qr_code" accept="image/*" class="nl-input">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button class="nl-btn-primary">💾 ບັນທຶກໂປຣໄຟລ໌</button>
    </div>
</form>
@endsection
