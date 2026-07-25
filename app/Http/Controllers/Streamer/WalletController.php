<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\GlobalSettings;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(
        private readonly WalletService $wallet,
        private readonly GlobalSettings $settings,
    ) {}

    public function index(): View
    {
        $streamer = $this->streamer();

        return view('streamer.wallet', [
            'streamer' => $streamer,
            'balance' => $this->wallet->balance($streamer),
            'transactions' => $streamer->walletTransactions()->latest()->limit(30)->get(),
            'withdrawals' => $streamer->withdrawals()->latest('requested_at')->limit(20)->get(),
            'minimum' => (float) $this->settings->get('withdrawal_min_amount', 50000),
            'fee' => (float) $this->settings->get('withdrawal_fee', 0),
            'processingDays' => (int) $this->settings->get('withdrawal_processing_days', 3),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000000'],
            'creator_note' => ['nullable', 'string', 'max:500'],
            'accept_withdrawal_terms' => ['accepted'],
        ], [
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນທີ່ຕ້ອງການຖອນ',
            'accept_withdrawal_terms.accepted' => 'ກະລຸນາຍອມຮັບເງື່ອນໄຂການຖອນເງິນ',
        ]);

        $this->wallet->requestWithdrawal($this->streamer(), $data);

        return back()->with('success', 'ສົ່ງຄຳຂໍຖອນເງິນແລ້ວ ຍອດດັ່ງກ່າວຖືກກັນໄວ້ລະຫວ່າງການກວດສອບ');
    }

    public function cancel(Withdrawal $withdrawal): RedirectResponse
    {
        $this->wallet->cancel($withdrawal, $this->streamer());

        return back()->with('success', 'ຍົກເລີກຄຳຂໍ ແລະ ຄືນຍອດເຂົ້າກະເປົາແລ້ວ');
    }
}
