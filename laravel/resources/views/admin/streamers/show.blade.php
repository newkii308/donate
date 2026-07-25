@extends('layouts.app')
@section('title', 'ສະຕຣີມເມີ · '.$streamer->display_name)

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="nl-card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="grid h-14 w-14 place-items-center rounded-full bg-brand-600 text-xl font-black text-white">
                    {{ mb_strtoupper(mb_substr($streamer->display_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $streamer->display_name }}</h2>
                    <p class="text-sm text-slate-400">/donate/{{ $streamer->username }} · {{ $streamer->user?->email }}</p>
                </div>
            </div>
            <a href="{{ route('donate.show', $streamer->username) }}" target="_blank" class="nl-btn-ghost">ເບິ່ງໜ້າໂດເນດ ↗</a>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div><p class="text-xs text-slate-400">ຈຳນວນໂດເນດ</p><p class="text-lg font-bold">{{ $streamer->donations_count }}</p></div>
            <div><p class="text-xs text-slate-400">ສະກຸນເງິນ</p><p class="text-lg font-bold">{{ $streamer->currency }}</p></div>
            <div><p class="text-xs text-slate-400">ສະຖານະ</p><p class="text-lg font-bold">{{ $streamer->is_active ? 'ກຳລັງໃຊ້ງານ' : 'ຖືກລະງັບ' }}</p></div>
            <div><p class="text-xs text-slate-400">ເຂົ້າຮ່ວມເມື່ອ</p><p class="text-lg font-bold">{{ $streamer->created_at->format('d/m/Y') }}</p></div>
        </div>
    </div>

    <div class="nl-card overflow-hidden">
        <h3 class="px-6 py-4 font-semibold">ການໂດເນດລ່າສຸດ</h3>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($recent as $d)
                    <tr>
                        <td class="px-6 py-3 font-medium">{{ $d->displayName() }}</td>
                        <td class="px-6 py-3 text-slate-400">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-3 text-right font-semibold text-brand-500">{{ \App\Support\Money::format($d->amount) }} {{ $d->currency }}</td>
                    </tr>
                @empty
                    <tr><td class="px-6 py-8 text-center text-slate-400">ຍັງບໍ່ມີໂດເນດ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
