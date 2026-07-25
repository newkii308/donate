<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Streamer;
use Illuminate\Http\JsonResponse;

class DonationGoalController extends Controller
{
    /**
     * Polling endpoint hit by the goal widget every few seconds.
     * GET /api/widget/goal/{overlay_key}/check
     */
    public function check(Streamer $streamer): JsonResponse
    {
        abort_unless($streamer->is_active, 404);

        // อ้างอิงจาก streamer_id เท่านั้น ค่าอื่นใช้ default ของ DB
        // (อย่าใส่ attribute ตายตัว มิฉะนั้นเมื่อสตรีมเมอร์แก้เป้าหมายแล้ว
        //  firstOrCreate จะหาไม่เจอและพยายามสร้างแถวซ้ำ ชน unique streamer_id → 500)
        $goal = $streamer->donationGoal()->firstOrCreate([]);

        return response()->json([
            'goal' => [
                'title' => $goal->title,
                'target_amount' => (float) $goal->target_amount,
                'current_amount' => (float) $goal->current_amount,
                'bottom_text' => $goal->bottom_text,
                'theme_id' => $goal->theme_id,
                'progress_style' => $goal->progress_style,
                'effects' => $goal->effects ?? [],
                'show_title' => (bool) $goal->show_title,
                'show_current' => (bool) $goal->show_current,
                'show_target' => (bool) $goal->show_target,
                'show_percentage' => (bool) $goal->show_percentage,
                'show_bottom_text' => (bool) $goal->show_bottom_text,
                'celebration_enabled' => (bool) $goal->celebration_enabled,
                'celebration_type' => $goal->celebration_type,
                'celebration_message' => $goal->celebration_message,
                
                // Custom theme colors/styles
                'primary_color' => $goal->primary_color,
                'secondary_color' => $goal->secondary_color,
                'background_style' => $goal->background_style,
                'border_style' => $goal->border_style,
                'border_radius' => (int) $goal->border_radius,
                'shadow_style' => $goal->shadow_style,
                'font_family' => $goal->font_family,
                'font_weight' => $goal->font_weight,
                'font_color' => $goal->font_color,
            ],
            'poll_seconds' => (int) config('newlab.overlay.poll_seconds', 3),
        ]);
    }
}
