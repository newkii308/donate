@extends('layouts.app')
@section('title', 'ຜູ້ໃຊ້ງານ')

@section('content')
<div class="space-y-6">
    <div class="nl-card p-6">
        <form method="GET" class="grid gap-3 sm:grid-cols-4">
            <div class="sm:col-span-2">
                <label class="nl-label">ຄົ້ນຫາ</label>
                <input name="search" value="{{ $search }}" class="nl-input" placeholder="ຊື່ ຫຼື ອີເມວ">
            </div>
            <div>
                <label class="nl-label">ບົດບາດ</label>
                <select name="role" class="nl-input">
                    <option value="">ທັງໝົດ</option>
                    <option value="admin" @selected($role === 'admin')>ຜູ້ດູແລລະບົບ</option>
                    <option value="streamer" @selected($role === 'streamer')>ສະຕຣີມເມີ</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="nl-btn-primary">ກັ່ນຕອງ</button>
                <a href="{{ route('admin.users.index') }}" class="nl-btn-ghost">ລ້າງຄ່າ</a>
            </div>
        </form>
    </div>

    <div class="nl-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase text-slate-400 dark:bg-white/5">
                    <tr><th class="px-6 py-3">ຊື່</th><th class="px-6 py-3">ອີເມວ</th><th class="px-6 py-3">ບົດບາດ</th><th class="px-6 py-3">ສະຖານະ</th><th class="px-6 py-3 text-right">ຈັດການ</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $user->email }}</td>
                            <td class="px-6 py-3">{{ $user->role->label() }}</td>
                            <td class="px-6 py-3">
                                @if ($user->is_active)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">ກຳລັງໃຊ້ງານ</span>
                                @else
                                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">ຖືກລະງັບ</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                            @csrf @method('PATCH')
                                            <button class="nl-btn-ghost px-3 py-1.5 text-xs">{{ $user->is_active ? 'ລະງັບ' : 'ເປີດໃຊ້ງານ' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('ລຶບຜູ້ໃຊ້ນີ້ບໍ?')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 text-xs font-medium text-rose-500 hover:underline">ລຶບ</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">ທ່ານ</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">ບໍ່ພົບຜູ້ໃຊ້ງານ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $users->links() }}
</div>
@endsection
