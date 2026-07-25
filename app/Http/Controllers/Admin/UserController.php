<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly ActivityLogService $activity)
    {
    }

    public function index(Request $request): View
    {
        $users = User::with('streamer')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $request->input('search'),
            'role' => $request->input('role'),
        ]);
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'ທ່ານບໍ່ສາມາດລະງັບບັນຊີຂອງຕົນເອງໄດ້');

        $user->update(['is_active' => ! $user->is_active]);
        $user->streamer?->update(['is_active' => $user->is_active]);

        $this->activity->log(
            $user->is_active ? 'user.activated' : 'user.suspended',
            ($user->is_active ? 'Activated' : 'Suspended')." user {$user->email}",
            $user,
        );

        return back()->with('success', 'ອັບເດດສະຖານະຜູ້ໃຊ້ສຳເລັດແລ້ວ');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'ທ່ານບໍ່ສາມາດລຶບບັນຊີຂອງຕົນເອງໄດ້');

        $this->activity->log('user.deleted', "Deleted user {$user->email}", $user);
        $user->delete();

        return back()->with('success', 'ລຶບຜູ້ໃຊ້ສຳເລັດແລ້ວ');
    }
}
