<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDonationPageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonationPageController extends Controller
{
    use InteractsWithStreamer;

    public function edit(): View
    {
        $streamer = $this->streamer();

        return view('streamer.donation-page', [
            'streamer' => $streamer,
            'page' => $streamer->donationPage()->firstOrCreate([]),
        ]);
    }

    public function update(UpdateDonationPageRequest $request): RedirectResponse
    {
        $streamer = $this->streamer();
        $streamer->donationPage()->firstOrCreate([])->update($request->validated());

        return back()->with('success', 'ບັນທຶກໜ້າໂດເນດສຳເລັດແລ້ວ');
    }
}
