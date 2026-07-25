@extends('layouts.app')
@section('title', 'ຄັງສື່')

@section('content')
<div class="space-y-6">
    {{-- อัปโหลด --}}
    <div class="nl-card p-6" x-data="{ type: 'image' }">
        <h3 class="font-semibold">ອັບໂຫຼດສື່</h3>
        <p class="mt-1 text-xs text-slate-400">ຮູບ/GIF/ວິດີໂອ ໃຊ້ເປັນພາບແຈ້ງເຕືອນ · ໄຟລ໌ສຽງໃຊ້ເປັນສຽງແຈ້ງເຕືອນ</p>
        <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            <div class="sm:w-56">
                <label class="nl-label">ປະເພດ</label>
                <select name="type" x-model="type" class="nl-input">
                    <option value="image">ຮູບພາບ (PNG, JPG, WEBP)</option>
                    <option value="animation">ພາບເຄື່ອນໄຫວ (GIF, WEBM)</option>
                    <option value="audio">ສຽງ (MP3, WAV, OGG)</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="nl-label">ໄຟລ໌</label>
                <input type="file" name="file" required class="nl-input"
                       :accept="type === 'image' ? 'image/png,image/jpeg,image/webp' : (type === 'animation' ? 'image/gif,video/webm' : 'audio/mpeg,audio/wav,audio/ogg')">
            </div>
            <button class="nl-btn-primary">⬆ ອັບໂຫຼດ</button>
        </form>
        <p class="mt-2 text-xs text-slate-400">
            ຮູບພາບ ≤ {{ $rules['image']['max_kb'] / 1024 }}MB · ພາບເຄື່ອນໄຫວ ≤ {{ $rules['animation']['max_kb'] / 1024 }}MB · ສຽງ ≤ {{ $rules['audio']['max_kb'] / 1024 }}MB
        </p>
    </div>

    @foreach ([['ຮູບພາບ', $images, 'image'], ['ພາບເຄື່ອນໄຫວ', $animations, 'animation'], ['ສຽງ', $audio, 'audio']] as [$label, $items, $kind])
        <div class="nl-card p-6">
            <h3 class="font-semibold">{{ $label }} <span class="text-sm text-slate-400">({{ $items->count() }})</span></h3>
            @if ($items->isEmpty())
                <p class="mt-3 text-sm text-slate-400">ຍັງບໍ່ມີ{{ $label }}ທີ່ອັບໂຫຼດ</p>
            @else
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($items as $m)
                        <div class="group rounded-xl border border-slate-200 p-3 dark:border-white/10">
                            <div class="mb-2 grid h-28 place-items-center overflow-hidden rounded-lg bg-slate-100 dark:bg-white/5">
                                @if ($kind === 'audio')
                                    <audio controls src="{{ $m->url() }}" class="w-full"></audio>
                                @elseif (\Illuminate\Support\Str::endsWith($m->path, '.webm'))
                                    <video src="{{ $m->url() }}" class="h-full w-full object-contain" muted loop></video>
                                @else
                                    <img src="{{ $m->url() }}" class="h-full w-full object-contain">
                                @endif
                            </div>
                            <p class="truncate text-xs font-medium" title="{{ $m->original_name }}">{{ $m->original_name }}</p>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400">{{ $m->humanSize() }}</span>
                                <form method="POST" action="{{ route('media.destroy', $m) }}" onsubmit="return confirm('ລຶບໄຟລ໌ນີ້ບໍ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[11px] font-medium text-rose-500 hover:underline">ລຶບ</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
