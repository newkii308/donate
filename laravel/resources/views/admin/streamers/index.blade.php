@extends('layouts.app')
@section('title', 'ສະຕຣີມເມີ')

@section('content')
<div class="space-y-6">
    <div class="nl-card p-6">
        <form method="GET" class="flex gap-3">
            <input name="search" value="{{ $search }}" class="nl-input" placeholder="ຄົ້ນຫາດ້ວຍຊື່ ຫຼື username">
            <button class="nl-btn-primary">ຄົ້ນຫາ</button>
            <a href="{{ route('admin.streamers.index') }}" class="nl-btn-ghost">ລ້າງຄ່າ</a>
        </form>
    </div>

    <div class="nl-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                    <tr><th class="px-6 py-3">ສະຕຣີມເມີ</th><th class="px-6 py-3">Username</th><th class="px-6 py-3">ໂດເນດ</th><th class="px-6 py-3">ຍອດລວມ</th><th class="px-6 py-3">ສະຖານະ</th><th class="px-6 py-3 text-right">ຈັດການ</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($streamers as $streamer)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $streamer->display_name }}</td>
                            <td class="px-6 py-3 text-slate-500">/{{ $streamer->username }}</td>
                            <td class="px-6 py-3">{{ $streamer->donations_count }}</td>
                            <td class="px-6 py-3 font-semibold text-brand-500">{{ \App\Support\Money::format($streamer->donations_sum_amount ?? 0) }} {{ $streamer->currency }}</td>
                            <td class="px-6 py-3">
                                @if ($streamer->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">ກຳລັງໃຊ້ງານ</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">ຖືກລະງັບ</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.streamers.show', $streamer) }}" class="nl-btn-ghost px-3 py-1.5 text-xs">ເບິ່ງ</a>
                                    <form method="POST" action="{{ route('admin.streamers.toggle', $streamer) }}">
                                        @csrf @method('PATCH')
                                        <button class="nl-btn-ghost px-3 py-1.5 text-xs">{{ $streamer->is_active ? 'ລະງັບ' : 'ເປີດໃຊ້ງານ' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-slate-400">ບໍ່ພົບສະຕຣີມເມີ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $streamers->links() }}
</div>
@endsection
