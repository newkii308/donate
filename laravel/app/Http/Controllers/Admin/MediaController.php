<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ActivityLogService;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $media,
        private readonly ActivityLogService $activity,
    ) {
    }

    public function index(): View
    {
        return view('admin.media.index', [
            'media' => Media::with('streamer')->latest()->paginate(40),
        ]);
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->activity->log('media.deleted', "Deleted media #{$medium->id}", $medium);
        $this->media->delete($medium);

        return back()->with('success', 'ລຶບໄຟລ໌ສຳເລັດແລ້ວ');
    }
}
