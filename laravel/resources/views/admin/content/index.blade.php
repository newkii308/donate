@extends('layouts.app')
@section('title', 'ຈັດການເນື້ອຫາໜ້າເວັບ')

@section('content')
<div class="mx-auto max-w-3xl space-y-4">
    <div class="nl-card p-6">
        <h3 class="font-semibold">ຈັດການເນື້ອຫາໜ້າເວັບ</h3>
        <p class="mt-1 text-sm text-slate-400">ແກ້ໄຂຂໍ້ຄວາມ/ບລັອກຂອງແຕ່ລະໜ້າໄດ້ ໂດຍບໍ່ຕ້ອງແກ້ໂຄດ</p>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        @foreach ($pages as $slug => $label)
            <a href="{{ route('admin.content.edit', $slug) }}"
               class="nl-card flex items-center justify-between p-5 transition hover:ring-2 hover:ring-brand-500">
                <div>
                    <p class="font-semibold">{{ $label }}</p>
                    <p class="text-xs text-slate-400">{{ $counts[$slug] ?? 0 }} ບລັອກ · /{{ $slug === 'welcome' ? '' : $slug }}</p>
                </div>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-slate-400"><path d="M9 6l6 6-6 6"/></svg>
            </a>
        @endforeach
    </div>
</div>
@endsection
