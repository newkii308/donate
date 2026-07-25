<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\View\View;

class DonationGoalController extends Controller
{
    /**
     * OBS browser-source donation goal widget: /widget/goal/{overlay_key}
     */
    public function show(Streamer $streamer): View
    {
        abort_unless($streamer->is_active, 404);

        // อ้างอิงจาก streamer_id เท่านั้น ค่าอื่นใช้ default ของ DB
        // (อย่าใส่ attribute ตายตัว มิฉะนั้นเมื่อสตรีมเมอร์แก้เป้าหมายแล้ว
        //  firstOrCreate จะหาไม่เจอและพยายามสร้างแถวซ้ำ ชน unique streamer_id → 500)
        $goal = $streamer->donationGoal()->firstOrCreate([]);

        return view('widget.goal', [
            'streamer' => $streamer,
            'goal' => $goal,
            'pollSeconds' => (int) config('newlab.overlay.poll_seconds', 3),
        ]);
    }
}
