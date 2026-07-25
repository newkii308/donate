<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Streamer;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StreamerController extends Controller
{
    public function __construct(private readonly ActivityLogService $activity)
    {
    }

    public function index(Request $request): View
    {
        $streamers = Streamer::with('user')
            ->withCount('donations')
            ->withSum('donations', 'amount')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(fn ($w) => $w->where('display_name', 'like', "%{$term}%")->orWhere('username', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.streamers.index', [
            'streamers' => $streamers,
            'search' => $request->input('search'),
        ]);
    }

    public function show(Streamer $streamer): View
    {
        $streamer->loadCount('donations')->load('user', 'donationPage');

        return view('admin.streamers.show', [
            'streamer' => $streamer,
            'recent' => $streamer->donations()->latest()->limit(15)->get(),
        ]);
    }

    public function toggleActive(Streamer $streamer): RedirectResponse
    {
        $streamer->update(['is_active' => ! $streamer->is_active]);

        $this->activity->log(
            $streamer->is_active ? 'streamer.activated' : 'streamer.suspended',
            ($streamer->is_active ? 'Activated' : 'Suspended')." streamer {$streamer->username}",
            $streamer,
        );

        return back()->with('success', 'ອັບເດດສະຖານະສະຕຣີມເມີສຳເລັດແລ້ວ');
    }
}
