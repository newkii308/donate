@extends('layouts.app')
@section('title', 'ຄັງສື່ທັງໝົດ')

@section('content')
<div class="nl-card p-6">
    <h3 class="font-semibold">ໄຟລ໌ທີ່ອັບໂຫຼດທັງໝົດ</h3>
    @if ($media->isEmpty())
        <p class="mt-3 text-sm text-slate-400">ຍັງບໍ່ມີສື່ທີ່ອັບໂຫຼດ</p>
    @else
        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($media as $m)
                <div class="rounded-xl border border-slate-200 p-3 dark:border-white/10">
                    <div class="mb-2 grid h-24 place-items-center overflow-hidden rounded-lg bg-slate-100 dark:bg-white/5">
                        @if ($m->type->value === 'audio')
                            <span class="text-2xl">🎵</span>
                        @elseif (\Illuminate\Support\Str::endsWith($m->path, '.webm'))
                            <video src="{{ $m->url() }}" class="h-full w-full object-contain" muted></video>
                        @else
                            <img src="{{ $m->url() }}" class="h-full w-full object-contain">
                        @endif
                    </div>
                    <p class="truncate text-xs font-medium" title="{{ $m->original_name }}">{{ $m->original_name }}</p>
                    <p class="text-[10px] text-slate-400">{{ $m->streamer?->display_name }} · {{ $m->humanSize() }}</p>
                    <form method="POST" action="{{ route('admin.media.destroy', $m) }}" onsubmit="return confirm('ລຶບໄຟລ໌ນີ້ບໍ?')" class="mt-1">
                        @csrf @method('DELETE')
                        <button class="text-[11px] font-medium text-rose-500 hover:underline">ລຶບ</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="mt-6">{{ $media->links() }}</div>
@endsection
