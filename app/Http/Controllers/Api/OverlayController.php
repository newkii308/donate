<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Streamer;
use App\Services\NotificationQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OverlayController extends Controller
{
    public function __construct(private readonly NotificationQueueService $queue)
    {
    }

    /**
     * Polling endpoint hit by the overlay every few seconds.
     * GET /api/overlay/{overlay_key}/check
     */
    public function check(Streamer $streamer): JsonResponse
    {
        abort_unless($streamer->is_active, 404);

        $notification = $this->queue->next($streamer);

        return response()->json([
            'notification' => $notification ? array_merge(
                ['id' => $notification->id],
                $notification->payload,
            ) : null,
            'poll_seconds' => (int) config('newlab.overlay.poll_seconds', 3),
        ]);
    }

    /**
     * Acknowledge that an alert finished playing.
     * POST /api/overlay/{overlay_key}/complete
     */
    public function complete(Request $request, Streamer $streamer): JsonResponse
    {
        abort_unless($streamer->is_active, 404);

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $ok = $this->queue->markCompleted($streamer, $validated['id']);

        return response()->json(['ok' => $ok]);
    }
}
