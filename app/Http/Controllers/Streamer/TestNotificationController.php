<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestNotificationRequest;
use App\Services\NotificationQueueService;
use Illuminate\Http\JsonResponse;

class TestNotificationController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(private readonly NotificationQueueService $queue)
    {
    }

    /**
     * Queue a test alert (no real donation is created). The overlay picks it
     * up on its next poll and plays animation/image/sound/TTS.
     */
    public function store(TestNotificationRequest $request): JsonResponse
    {
        $notification = $this->queue->enqueueTest($this->streamer(), $request->validated());

        return response()->json([
            'ok' => true,
            'id' => $notification->id,
            'message' => '✓ ສົ່ງການແຈ້ງເຕືອນທົດສອບແລ້ວ — ເບິ່ງທີ່ Overlay ຂອງທ່ານ',
        ]);
    }
}
