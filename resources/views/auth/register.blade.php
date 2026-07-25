@extends('layouts.guest')
@section('title', 'ສະໝັກສະມາຊິກ')

@section('content')
    <h2 class="text-xl font-bold">ສ້າງບັນຊີຂອງທ່ານ</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">ສະໝັກແລ້ວເຂົ້າໃຊ້ງານໄດ້ທັນທີ</p>

    <x-flash />

    <div class="mt-4 flex items-start gap-2 rounded-xl bg-emerald-50 px-3 py-2.5 text-xs text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 shrink-0"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
        <span>ລະບົບຈະສ້າງໜ້າໂດເນດ ແລະ ເຂົ້າສູ່ແດຊບອດໃຫ້ທ່ານທັນທີຫຼັງຈາກສະໝັກ</span>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4" x-data="{ name: @js(old('name')) }">
        @csrf
        <div>
            <label class="nl-label" for="name">ຊື່ສະແດງ</label>
            <input id="name" name="name" x-model="name" value="{{ old('name') }}" required autofocus class="nl-input" placeholder="ຊື່ຊ່ອງຂອງທ່ານ">
        </div>
        <div>
            <label class="nl-label" for="username">ຊື່ຜູ້ໃຊ້ (ລິ້ງໂດເນດ)</label>
            <div class="flex items-center gap-2">
                <span class="text-sm text-slate-400">/donate/</span>
                <input id="username" name="username" value="{{ old('username') }}" required class="nl-input" placeholder="your-name">
            </div>
        </div>
        <div>
            <label class="nl-label" for="email">ອີເມວ</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="nl-input" placeholder="you@example.com">
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="nl-label" for="password">ລະຫັດຜ່ານ</label>
                <input id="password" name="password" type="password" required class="nl-input">
            </div>
            <div>
                <label class="nl-label" for="password_confirmation">ຢືນຢັນລະຫັດຜ່ານ</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="nl-input">
            </div>
        </div>
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 text-xs leading-6 text-slate-600 dark:border-white/10 dark:text-slate-300">
            <input type="checkbox" name="terms" value="1" required class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <span>
                ຂ້ອຍອ່ານ ແລະ ຍອມຮັບ
                <a href="{{ route('page.show', 'terms') }}" target="_blank" class="font-bold text-brand-500 hover:underline">ເງື່ອນໄຂການນຳໃຊ້</a>
                ແລະ
                <a href="{{ route('page.show', 'privacy') }}" target="_blank" class="font-bold text-brand-500 hover:underline">ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ</a>
                ແລະ
                <a href="{{ route('page.show', 'rules') }}" target="_blank" class="font-bold text-brand-500 hover:underline">ກົດລະບຽບການນຳໃຊ້</a>
                ແລະ
                <a href="{{ route('page.show', 'withdrawal-terms') }}" target="_blank" class="font-bold text-brand-500 hover:underline">ເງື່ອນໄຂການຖອນເງິນ</a>
            </span>
        </label>
        <button type="submit" class="nl-btn-primary w-full">ສະໝັກ ແລະ ເລີ່ມໃຊ້ງານ</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
        ມີບັນຊີແລ້ວ?
        <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:underline">ເຂົ້າລະບົບ</a>
    </p>
@endsection
