<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WithdrawalStatus;
use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function index(Request $request): View
    {
        $withdrawals = Withdrawal::with(['streamer', 'reviewer'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('requested_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.withdrawals.index', [
            'withdrawals' => $withdrawals,
            'status' => $request->input('status'),
            'statuses' => WithdrawalStatus::cases(),
        ]);
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['nullable', 'string', 'max:1000']]);
        $this->wallet->approve($withdrawal, $request->user(), $data['admin_note'] ?? null);

        return back()->with('success', 'ອະນຸມັດຄຳຂໍຖອນເງິນແລ້ວ');
    }

    public function paid(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate([
            'payment_reference' => ['required', 'string', 'max:120'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'payment_reference.required' => 'ກະລຸນາປ້ອນເລກອ້າງອີງການໂອນ',
        ]);

        $this->wallet->markPaid(
            $withdrawal,
            $request->user(),
            $data['payment_reference'],
            $data['admin_note'] ?? null,
        );

        return back()->with('success', 'ບັນທຶກວ່າໂອນເງິນໃຫ້ຄຣີເອເຕີແລ້ວ');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate([
            'admin_note' => ['required', 'string', 'max:1000'],
        ], [
            'admin_note.required' => 'ກະລຸນາລະບຸເຫດຜົນທີ່ປະຕິເສດ',
        ]);

        $this->wallet->reject($withdrawal, $request->user(), $data['admin_note']);

        return back()->with('success', 'ປະຕິເສດຄຳຂໍ ແລະ ຄືນຍອດໃຫ້ຄຣີເອເຕີແລ້ວ');
    }
}
