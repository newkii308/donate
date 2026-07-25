@extends('layouts.app')
@section('title', 'ແກ້ເນື້ອຫາ: '.$pageLabel)

@php
    $publicUrl = $page === 'welcome' ? route('home') : route('page.show', $page);
@endphp

@section('content')
<div class="mx-auto max-w-3xl space-y-5">

    {{-- หัวข้อ + ลิงก์ดูจริง --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.content.index') }}" class="text-xs text-slate-400 hover:underline">← ກັບໄປລາຍການໜ້າ</a>
            <h2 class="text-lg font-bold">ແກ້ເນື້ອຫາ: {{ $pageLabel }}</h2>
        </div>
        <a href="{{ $publicUrl }}" target="_blank" class="nl-btn-ghost text-sm">ເປີດເບິ່ງໜ້າຈິງ ↗</a>
    </div>

    @if (in_array($page, ['news', 'community', 'contact'], true))
        <div class="rounded-2xl border border-sky-500/20 bg-sky-500/10 px-4 py-3 text-sm leading-6 text-sky-700 dark:text-sky-300">
            @if ($page === 'news')
                ເພີ່ມບລັອກປະເພດ “ຂ່າວ / ປະກາດ”. ຂ່າວທີ່ອັບເດດຫຼ້າສຸດຈະສະແດງກ່ອນ ແລະ 3 ລາຍການລ່າສຸດຈະຂຶ້ນໜ້າຫຼັກອັດຕະໂນມັດ.
            @elseif ($page === 'community')
                ໃຊ້ປະເພດ “ຊ່ອງທາງຄອມມູນິຕີ” ແລະ ໃສ່ລິ້ງ Facebook, Discord, Telegram ຫຼື ຊ່ອງທາງອື່ນໃນຊ່ອງລິ້ງປຸ່ມ.
            @else
                ເພີ່ມຂໍ້ມູນການຊ່ວຍເຫຼືອໃນໜ້ານີ້. ອີເມວ ແລະ ລິ້ງໂຊຊຽວທາງການແກ້ໄຂໄດ້ທີ່ເມນູ “ຕັ້ງຄ່າລະບົບ”.
            @endif
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-600 dark:bg-emerald-500/10">{{ session('success') }}</div>
    @endif

    {{-- ===== เพิ่มบล็อกใหม่ ===== --}}
    <div x-data="{ open: false }" class="nl-card p-5">
        <button type="button" @click="open = !open" class="flex w-full items-center justify-between font-semibold">
            <span>+ ເພີ່ມບລັອກໃໝ່</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5 transition" :class="open && 'rotate-45'"><path d="M12 5v14M5 12h14"/></svg>
        </button>
        <form x-show="open" x-cloak method="POST" action="{{ route('admin.content.store') }}" class="mt-4 space-y-3">
            @csrf
            <input type="hidden" name="page" value="{{ $page }}">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="nl-label">ປະເພດບລັອກ</label>
                    <select name="type" class="nl-input">
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="nl-label">ຫົວຂໍ້ / ຄຳຖາມ</label>
                    <input name="heading" class="nl-input">
                </div>
            </div>
            <div>
                <label class="nl-label">ເນື້ອຫາ / ຄຳອະທິບາຍ / ຄຳຕອບ (ໃສ່ HTML ພື້ນຖານໄດ້)</label>
                <textarea name="body" rows="3" class="nl-input"></textarea>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <input name="link_label" class="nl-input" placeholder="ປ້າຍປຸ່ມ (ຖ້າມີ)">
                <input name="link_url" class="nl-input" placeholder="ລິ້ງປຸ່ມ ຕົວຢ່າງ /register">
            </div>
            <button class="nl-btn-primary text-sm">ເພີ່ມບລັອກ</button>
        </form>
    </div>

    {{-- ===== รายการบล็อก ===== --}}
    @forelse ($blocks as $block)
        <div class="nl-card p-5">
            <form method="POST" action="{{ route('admin.content.update', $block) }}" class="space-y-3">
                @csrf @method('PUT')

                <div class="flex flex-wrap items-center gap-3">
                    <span class="nl-chip">{{ $types[$block->type] ?? $block->type }}</span>
                    <label class="flex items-center gap-1.5 text-xs">
                        <span>ລຳດັບ</span>
                        <input name="sort_order" type="number" value="{{ $block->sort_order }}" class="nl-input w-20 py-1 text-sm">
                    </label>
                    <label class="flex items-center gap-1.5 text-xs">
                        <input type="checkbox" name="is_visible" value="1" @checked($block->is_visible) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        ສະແດງຜົນ
                    </label>
                    <select name="type" class="nl-input ml-auto w-auto py-1 text-sm">
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}" @selected($block->type === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="nl-label">ຫົວຂໍ້ / ຄຳຖາມ</label>
                    <input name="heading" value="{{ $block->heading }}" class="nl-input">
                </div>
                <div>
                    <label class="nl-label">ຄຳໂປຍ (subheading)</label>
                    <input name="subheading" value="{{ $block->subheading }}" class="nl-input">
                </div>
                <div>
                    <label class="nl-label">ເນື້ອຫາ / ຄຳອະທິບາຍ / ຄຳຕອບ (ໃສ່ HTML ພື້ນຖານໄດ້)</label>
                    <textarea name="body" rows="4" class="nl-input">{{ $block->body }}</textarea>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <input name="image_url" value="{{ $block->image_url }}" class="nl-input" placeholder="URL ຮູບ (ຖ້າມີ)">
                    <input name="link_label" value="{{ $block->link_label }}" class="nl-input" placeholder="ປ້າຍປຸ່ມ">
                    <input name="link_url" value="{{ $block->link_url }}" class="nl-input" placeholder="ລິ້ງປຸ່ມ">
                </div>

                <div class="flex items-center gap-2">
                    <button class="nl-btn-primary text-sm">ບັນທຶກ</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.content.destroy', $block) }}" class="mt-2"
                  onsubmit="return confirm('ລຶບບລັອກນີ້ບໍ?')">
                @csrf @method('DELETE')
                <button class="text-xs font-medium text-rose-500 hover:underline">ລຶບບລັອກນີ້</button>
            </form>
        </div>
    @empty
        <div class="nl-card p-8 text-center text-slate-400">ຍັງບໍ່ມີບລັອກໃນໜ້ານີ້ — ກົດ “ເພີ່ມບລັອກໃໝ່” ດ້ານເທິງ</div>
    @endforelse
</div>
@endsection
