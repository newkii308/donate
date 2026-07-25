@extends('layouts.guest')
@section('title', 'ເຂົ້າລະບົບ')

@section('content')
    <h2 class="text-xl font-bold">ຍິນດີຕ້ອນຮັບກັບຄືນ 👋</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">ເຂົ້າລະບົບເພື່ອຈັດການແດຊບອດຂອງທ່ານ</p>

    <x-flash />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="nl-label" for="email">ອີເມວ</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="nl-input" placeholder="you@example.com">
        </div>
        <div>
            <label class="nl-label" for="password">ລະຫັດຜ່ານ</label>
            <input id="password" name="password" type="password" required class="nl-input" placeholder="••••••••">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            ຈື່ຂ້ອຍໄວ້
        </label>
        <button type="submit" class="nl-btn-primary w-full">ເຂົ້າລະບົບ</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
        ຍັງບໍ່ມີບັນຊີ?
        <a href="{{ route('register') }}" class="font-semibold text-brand-500 hover:underline">ສະໝັກສະມາຊິກ</a>
    </p>
@endsection
