<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\DonationService;
use App\Services\GlobalSettings;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
        private readonly GlobalSettings $settings,
    ) {}

    /**
     * Public donation page: /donate/{username}
     */
    public function show(Streamer $streamer): View
    {
        $this->ensureAvailable($streamer);

        $streamer->load('donationPage');
        $page = $streamer->donationPage;

        $recent = ($page?->show_recent_donations ?? true)
            ? Donation::completed()->forStreamer($streamer->id)->latest()->limit(8)->get()
            : collect();

        return view('donate.show', [
            'streamer' => $streamer,
            'page' => $page,
            'recent' => $recent,
            'centralAccount' => [
                'enabled' => (bool) $this->settings->get('central_payment_enabled', true),
                'bank_name' => $this->settings->get('central_bank_name'),
                'account_name' => $this->settings->get('central_account_name'),
                'account_number' => $this->settings->get('central_account_number'),
                'qr_url' => $this->settings->get('central_qr_url'),
            ],
        ]);
    }

    /**
     * Handle a public donation submission.
     */
    public function store(StoreDonationRequest $request, Streamer $streamer): RedirectResponse
    {
        $this->ensureAvailable($streamer);

        $data = $request->validated();

        $enabled = (bool) $this->settings->get('central_payment_enabled', true);
        $hasCentralAccount = filled($this->settings->get('central_account_number'))
            || filled($this->settings->get('central_qr_url'));

        if (! $enabled || ! $hasCentralAccount) {
            return back()->withInput()->with('error', 'ຊ່ອງທາງຊຳລະເງິນຍັງບໍ່ພ້ອມໃຊ້ງານ');
        }

        $this->donations->create($streamer, [
            'donor_name' => $data['donor_name'],
            'amount' => $data['amount'],
            'transfer_reference' => $data['transfer_reference'],
            'message' => $data['message'] ?? null,
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
        ], $request->ip());

        return redirect()
            ->route('donate.show', $streamer->username)
            ->with('success', 'ຮັບລາຍການ '.Money::format($data['amount']).' ກີບແລ້ວ! ກຳລັງກວດຢືນຢັນການຊຳລະ ແລະ ຈະສະແດງແຈ້ງເຕືອນເມື່ອສຳເລັດ.');
    }

    private function ensureAvailable(Streamer $streamer): void
    {
        abort_unless($streamer->is_active && ($streamer->user?->is_active ?? false), 404);
    }
}
