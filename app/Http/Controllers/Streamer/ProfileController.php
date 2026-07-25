<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\StreamerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(private readonly StreamerService $streamers)
    {
    }

    public function edit(): View
    {
        return view('streamer.profile', [
            'streamer' => $this->streamer(),
            'currencies' => config('newlab.currencies'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $streamer = $this->streamer();
        $data = $request->safe()->except(['avatar', 'qr_code']);

        $this->streamers->updateProfile($streamer, $data);

        if ($request->hasFile('avatar')) {
            $this->streamers->updateAvatar($streamer, $request->file('avatar'));
        }

        if ($request->hasFile('qr_code')) {
            $this->streamers->updateQrCode($streamer, $request->file('qr_code'));
        }

        return back()->with('success', 'ບັນທຶກໂປຣໄຟລ໌ສຳເລັດແລ້ວ');
    }
}
