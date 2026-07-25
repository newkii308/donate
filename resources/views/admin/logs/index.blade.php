@extends('layouts.app')
@section('title', 'ປະຫວັດການໃຊ້ງານ')

@section('content')
<div class="space-y-6">
    <div class="nl-card p-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <input name="search" value="{{ $search }}" class="nl-input flex-1" placeholder="ຄົ້ນຫາຄຳອະທິບາຍ">
            <input name="action" value="{{ $action }}" class="nl-input sm:w-56" placeholder="Action (ຕົວຢ່າງ donation.created)">
            <button class="nl-btn-primary">ກັ່ນຕອງ</button>
            <a href="{{ route('admin.logs.index') }}" class="nl-btn-ghost">ລ້າງຄ່າ</a>
        </form>
    </div>

    <div class="nl-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                    <tr><th class="px-6 py-3">ເວລາ</th><th class="px-6 py-3">ຜູ້ໃຊ້</th><th class="px-6 py-3">ການກະທຳ</th><th class="px-6 py-3">ລາຍລະອຽດ</th><th class="px-6 py-3">IP</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-3 text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-3">{{ $log->user?->name ?? 'ລະບົບ' }}</td>
                            <td class="px-6 py-3"><code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs dark:bg-white/10">{{ $log->action }}</code></td>
                            <td class="px-6 py-3 text-slate-500">{{ $log->description }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">ຍັງບໍ່ມີບັນທຶກກິດຈະກຳ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $logs->links() }}
</div>
@endsection
