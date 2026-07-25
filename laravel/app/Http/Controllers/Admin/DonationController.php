<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationService;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function __construct(
        private readonly DonationService $donations,
        private readonly WalletService $wallet,
    ) {}

    public function index(Request $request): View
    {
        $donations = Donation::with('streamer')
            ->search($request->input('search'))
            ->when($request->filled('streamer_id'), fn ($q) => $q->where('streamer_id', $request->input('streamer_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.donations.index', [
            'donations' => $donations,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ]);
    }

    public function verify(Request $request, Donation $donation): RedirectResponse
    {
        $this->donations->verify($donation, $request->user(), $this->wallet);

        return back()->with('success', 'ຢືນຢັນຍອດແລ້ວ ລາຍຮັບຖືກເພີ່ມໃຫ້ຄຣີເອເຕີ ແລະ ສົ່ງແຈ້ງເຕືອນແລ້ວ');
    }

    public function reject(Request $request, Donation $donation): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_reason.required' => 'ກະລຸນາລະບຸເຫດຜົນທີ່ປະຕິເສດ',
        ]);

        $this->donations->reject($donation, $request->user(), $data['rejection_reason']);

        return back()->with('success', 'ປະຕິເສດລາຍການແລ້ວ ແລະ ບໍ່ໄດ້ເພີ່ມຍອດໃຫ້ຄຣີເອເຕີ');
    }
}
