@extends('layouts.app')
@section('title', 'ໜ້າໂດເນດ')

@section('content')
<form method="POST" action="{{ route('donation-page.update') }}" class="mx-auto max-w-3xl space-y-6">
    @csrf @method('PUT')

    <div class="nl-card p-6">
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">ຕັ້ງຄ່າໜ້າໂດເນດ</h3>
            <a href="{{ route('donate.show', $streamer->username) }}" target="_blank" class="text-sm font-medium text-brand-500 hover:underline">ເບິ່ງຕົວຢ່າງ ↗</a>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="nl-label">ຫົວຂໍ້</label>
                <input name="title" value="{{ old('title', $page->title) }}" class="nl-input" placeholder="ສະໜັບສະໜູນຊ່ອງຂອງຂ້ອຍ">
            </div>
            <div class="sm:col-span-2">
                <label class="nl-label">ຄຳອະທິບາຍ</label>
                <textarea name="description" rows="2" class="nl-input">{{ old('description', $page->description) }}</textarea>
            </div>
            <div>
                <label class="nl-label">ຈຳນວນເງິນຂັ້ນຕ່ຳ (ກີບ)</label>
                <input name="min_amount" type="number" min="1" step="0.01" value="{{ old('min_amount', $page->min_amount) }}" class="nl-input" required>
            </div>
            <div>
                <label class="nl-label">ຈຳນວນເງິນສູງສຸດ <span class="text-slate-400">(ບໍ່ບັງຄັບ)</span></label>
                <input name="max_amount" type="number" min="1" step="0.01" value="{{ old('max_amount', $page->max_amount) }}" class="nl-input">
            </div>
            <div class="sm:col-span-2">
                <label class="nl-label">ປຸ່ມຈຳນວນເງິນດ່ວນ <span class="text-slate-400">(ຂັ້ນດ້ວຍຈຸດ)</span></label>
                <input name="quick_amounts" value="{{ old('quick_amounts', collect($page->quick_amounts ?? [])->implode(', ')) }}" class="nl-input" placeholder="20, 50, 100, 500, 1000">
            </div>
            <div class="sm:col-span-2">
                <label class="nl-label">ຂໍ້ຄວາມຂອບໃຈ (ຫຼັງໂດເນດສຳເລັດ)</label>
                <input name="thank_you_message" value="{{ old('thank_you_message', $page->thank_you_message) }}" class="nl-input" placeholder="ຂອບໃຈສຳລັບການສະໜັບສະໜູນ! 💜">
            </div>
            <div>
                <label class="nl-label">ທີມໜ້າໂດເນດ</label>
                <select name="theme" class="nl-input">
                    <option value="dark" @selected(old('theme', $page->theme) === 'dark')>ມືດ (Dark)</option>
                    <option value="light" @selected(old('theme', $page->theme) === 'light')>ແຈ້ງ (Light)</option>
                </select>
            </div>
            <div>
                <label class="nl-label">ສີຫຼັກ (Accent)</label>
                <input name="accent_color" type="color" value="{{ old('accent_color', $page->accent_color) }}" class="nl-input h-11 p-1">
            </div>
        </div>

        <div class="mt-4 space-y-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="allow_anonymous" value="1" @checked(old('allow_anonymous', $page->allow_anonymous)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                ອະນຸຍາດໃຫ້ໂດເນດແບບບໍ່ລະບຸຊື່
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="show_recent_donations" value="1" @checked(old('show_recent_donations', $page->show_recent_donations)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                ສະແດງລາຍການຜູ້ສະໜັບສະໜູນລ່າສຸດ
            </label>
        </div>
    </div>

    <div class="flex justify-end">
        <button class="nl-btn-primary">💾 ບັນທຶກການປ່ຽນແປງ</button>
    </div>
</form>
@endsection
