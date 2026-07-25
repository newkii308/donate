<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\GlobalSettings;
use App\Services\StreamerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly StreamerService $streamers,
        private readonly ActivityLogService $activity,
        private readonly GlobalSettings $settings,
    ) {
    }

    public function create(): View|RedirectResponse
    {
        if (! $this->settings->get('registration_open', true)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'ຂະນະນີ້ປິດຮັບສະໝັກສະມາຊິກໃໝ່']);
        }

        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        abort_unless($this->settings->get('registration_open', true), 403, 'ຂະນະນີ້ປິດຮັບສະໝັກສະມາຊິກ');

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => Role::Streamer->value,
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $this->streamers->createForUser($user, [
            'username' => $data['username'],
            'display_name' => $data['name'],
        ]);

        $this->activity->log('user.registered', "New streamer registered: {$user->email}", $user, userId: $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'ສະໝັກສະມາຊິກສຳເລັດ ບັນຊີຂອງທ່ານພ້ອມໃຊ້ງານແລ້ວ');
    }
}
