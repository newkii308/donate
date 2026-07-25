<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\View\View;

class OverlayController extends Controller
{
    /**
     * OBS browser-source overlay: /overlay/{overlay_key}
     * Transparent page that polls the API every few seconds.
     */
    public function show(Streamer $streamer): View
    {
        abort_unless($streamer->is_active, 404);

        return view('overlay.show', [
            'streamer' => $streamer,
            'pollSeconds' => (int) config('newlab.overlay.poll_seconds', 3),
            'themes' => (array) config('newlab.overlay.themes', []),
        ]);
    }
}
