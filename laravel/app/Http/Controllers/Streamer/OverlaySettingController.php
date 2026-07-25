<?php

namespace App\Http\Controllers\Streamer;

use App\Enums\MediaType;
use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOverlayRequest;
use App\Services\StreamerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OverlaySettingController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(private readonly StreamerService $streamers)
    {
    }

    public function edit(): View
    {
        $streamer = $this->streamer();

        return view('streamer.overlay', [
            'streamer' => $streamer,
            'settings' => $streamer->overlaySetting()->firstOrCreate([]),
            'images' => $streamer->media()->ofType(MediaType::Image)->latest()->get()
                ->merge($streamer->media()->ofType(MediaType::Animation)->latest()->get()),
            'sounds' => $streamer->media()->ofType(MediaType::Audio)->latest()->get(),
            'overlayUrl' => route('overlay.show', $streamer->overlay_key),
            'themes' => (array) config('newlab.overlay.themes', []),
        ]);
    }

    public function update(UpdateOverlayRequest $request): RedirectResponse
    {
        $streamer = $this->streamer();
        $streamer->overlaySetting()->firstOrCreate([])->update($request->validated());

        return back()->with('success', 'ບັນທຶກການຕັ້ງຄ່າ Overlay ສຳເລັດແລ້ວ');
    }

    public function regenerateKey(): RedirectResponse
    {
        $this->streamers->regenerateOverlayKey($this->streamer());

        return back()->with('success', 'ສ້າງລິ້ງ Overlay ໃໝ່ສຳເລັດແລ້ວ');
    }
}
