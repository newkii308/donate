<?php

namespace App\Http\Controllers\Streamer;

use App\Enums\MediaType;
use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(): View
    {
        $streamer = $this->streamer();

        return view('streamer.media', [
            'streamer' => $streamer,
            'images' => $streamer->media()->ofType(MediaType::Image)->latest()->get(),
            'animations' => $streamer->media()->ofType(MediaType::Animation)->latest()->get(),
            'audio' => $streamer->media()->ofType(MediaType::Audio)->latest()->get(),
            'rules' => config('newlab.media'),
        ]);
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $streamer = $this->streamer();

        $this->media->store(
            $streamer,
            $request->file('file'),
            MediaType::from($request->validated('type')),
        );

        return back()->with('success', 'ອັບໂຫຼດໄຟລ໌ສຳເລັດແລ້ວ');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        abort_unless($medium->streamer_id === $this->streamer()->id, 403);

        $this->media->delete($medium);

        return back()->with('success', 'ລຶບໄຟລ໌ສຳເລັດແລ້ວ');
    }
}
