<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDonationGoalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationGoalController extends Controller
{
    use InteractsWithStreamer;

    /**
     * Streamer dashboard: ตั้งค่าเป้าหมายโดเนท (Donation Goal Widget)
     */
    public function edit(): View
    {
        $streamer = $this->streamer();

        return view('streamer.donation-goal', [
            'streamer' => $streamer,
            'goal' => $streamer->donationGoal()->firstOrCreate([]),
            'widgetUrl' => route('widget.goal.show', $streamer->overlay_key),
        ]);
    }

    /**
     * บันทึกการตั้งค่าเป้าหมายโดเนททั้งหมด
     */
    public function update(UpdateDonationGoalRequest $request): RedirectResponse
    {
        $streamer = $this->streamer();

        $streamer->donationGoal()->firstOrCreate([])->update($request->validated());

        return back()->with('success', 'ບັນທຶກການຕັ້ງຄ່າເປົ້າໝາຍໂດເນດສຳເລັດແລ້ວ');
    }

    /**
     * เครื่องมือจำลองโดเนท: เพิ่มยอดปัจจุบันเข้า DB เพื่อทดสอบหลอด/เอฟเฟกต์
     */
    public function testUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000', 'max:100000000'],
        ]);

        $streamer = $this->streamer();

        $streamer->donationGoal()->firstOrCreate([])
            ->increment('current_amount', (float) $validated['amount']);

        return back()->with('success', 'ເພີ່ມຍອດທົດສອບເຂົ້າລະບົບສຳເລັດແລ້ວ');
    }
}
