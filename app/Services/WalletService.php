<?php

namespace App\Services;

use App\Enums\WalletTransactionType;
use App\Enums\WithdrawalStatus;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(
        private readonly GlobalSettings $settings,
        private readonly ActivityLogService $activity,
    ) {}

    public function balance(Streamer|int $streamer): float
    {
        $streamerId = $streamer instanceof Streamer ? $streamer->id : $streamer;

        return (float) WalletTransaction::where('streamer_id', $streamerId)->sum('amount');
    }

    public function creditDonation(Donation $donation, User $admin): WalletTransaction
    {
        if ($existing = WalletTransaction::where('donation_id', $donation->id)->first()) {
            return $existing;
        }

        $feePercent = max(0, min(100, (float) $this->settings->get('platform_fee_percent', 0)));
        $fee = round((float) $donation->amount * $feePercent / 100, 2);
        $net = round((float) $donation->amount - $fee, 2);

        $donation->forceFill([
            'platform_fee' => $fee,
            'net_amount' => $net,
        ])->save();

        return $this->append(
            streamerId: $donation->streamer_id,
            type: WalletTransactionType::DonationCredit,
            amount: $net,
            description: "Verified donation #{$donation->id}",
            userId: $admin->id,
            donationId: $donation->id,
        );
    }

    /**
     * @param  array{amount:float|int|string, creator_note?:?string}  $data
     */
    public function requestWithdrawal(Streamer $streamer, array $data): Withdrawal
    {
        return DB::transaction(function () use ($streamer, $data) {
            Streamer::query()->lockForUpdate()->findOrFail($streamer->id);

            $amount = round((float) $data['amount'], 2);
            $fee = max(0, round((float) $this->settings->get('withdrawal_fee', 0), 2));
            $minimum = max(0, (float) $this->settings->get('withdrawal_min_amount', 50000));

            if ($amount < $minimum) {
                throw ValidationException::withMessages([
                    'amount' => 'ຈຳນວນຖອນຂັ້ນຕ່ຳແມ່ນ '.number_format($minimum).' ກີບ',
                ]);
            }

            if ($amount <= $fee) {
                throw ValidationException::withMessages([
                    'amount' => 'ຈຳນວນຖອນຕ້ອງຫຼາຍກວ່າຄ່າທຳນຽມ',
                ]);
            }

            if ($this->balance($streamer) < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'ຍອດເງິນທີ່ຖອນໄດ້ບໍ່ພຽງພໍ',
                ]);
            }

            foreach (['bank_name', 'account_name', 'account_number'] as $field) {
                if (blank($streamer->{$field})) {
                    throw ValidationException::withMessages([
                        'amount' => 'ກະລຸນາຕື່ມຊື່ທະນາຄານ, ຊື່ບັນຊີ ແລະ ເລກບັນຊີໃນໂປຣໄຟລ໌ກ່ອນຖອນເງິນ',
                    ]);
                }
            }

            $withdrawal = $streamer->withdrawals()->create([
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => $amount - $fee,
                'currency' => 'LAK',
                'status' => WithdrawalStatus::Pending,
                'bank_name' => $streamer->bank_name,
                'account_name' => $streamer->account_name,
                'account_number' => $streamer->account_number,
                'payment_method' => $streamer->payment_method,
                'creator_note' => $data['creator_note'] ?? null,
                'requested_at' => now(),
            ]);

            $this->append(
                streamerId: $streamer->id,
                type: WalletTransactionType::WithdrawalReserve,
                amount: -$amount,
                description: "Reserved for withdrawal #{$withdrawal->id}",
                userId: $streamer->user_id,
                withdrawalId: $withdrawal->id,
            );

            $this->activity->log(
                'withdrawal.requested',
                "Withdrawal request of {$amount} LAK",
                $withdrawal,
                ['amount' => $amount, 'net_amount' => $amount - $fee],
                $streamer->user_id,
            );

            return $withdrawal;
        });
    }

    public function approve(Withdrawal $withdrawal, User $admin, ?string $note = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $admin, $note) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if ($locked->status !== WithdrawalStatus::Pending) {
                throw ValidationException::withMessages(['withdrawal' => 'ຄຳຂໍນີ້ບໍ່ຢູ່ໃນສະຖານະທີ່ອະນຸມັດໄດ້']);
            }

            $locked->update([
                'status' => WithdrawalStatus::Approved,
                'admin_note' => $note,
                'reviewed_at' => now(),
                'reviewed_by' => $admin->id,
            ]);

            $this->activity->log('withdrawal.approved', "Withdrawal #{$locked->id} approved", $locked);

            return $locked;
        });
    }

    public function markPaid(Withdrawal $withdrawal, User $admin, string $reference, ?string $note = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $admin, $reference, $note) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if ($locked->status !== WithdrawalStatus::Approved) {
                throw ValidationException::withMessages(['withdrawal' => 'ຕ້ອງອະນຸມັດຄຳຂໍກ່ອນບັນທຶກການໂອນ']);
            }

            $locked->update([
                'status' => WithdrawalStatus::Paid,
                'payment_reference' => $reference,
                'admin_note' => $note ?: $locked->admin_note,
                'paid_at' => now(),
                'reviewed_by' => $admin->id,
            ]);

            $this->activity->log(
                'withdrawal.paid',
                "Withdrawal #{$locked->id} paid",
                $locked,
                ['payment_reference' => $reference],
            );

            return $locked;
        });
    }

    public function reject(Withdrawal $withdrawal, User $admin, string $reason): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $admin, $reason) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if (! in_array($locked->status, [WithdrawalStatus::Pending, WithdrawalStatus::Approved], true)) {
                throw ValidationException::withMessages(['withdrawal' => 'ຄຳຂໍນີ້ບໍ່ສາມາດປະຕິເສດໄດ້']);
            }

            $locked->update([
                'status' => WithdrawalStatus::Rejected,
                'admin_note' => $reason,
                'reviewed_at' => now(),
                'reviewed_by' => $admin->id,
            ]);

            $this->reverseWithdrawal($locked, $admin->id);
            $this->activity->log('withdrawal.rejected', "Withdrawal #{$locked->id} rejected", $locked, ['reason' => $reason]);

            return $locked;
        });
    }

    public function cancel(Withdrawal $withdrawal, Streamer $streamer): Withdrawal
    {
        abort_unless($withdrawal->streamer_id === $streamer->id, 404);

        return DB::transaction(function () use ($withdrawal, $streamer) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if ($locked->status !== WithdrawalStatus::Pending) {
                throw ValidationException::withMessages(['withdrawal' => 'ຍົກເລີກໄດ້ສະເພາະຄຳຂໍທີ່ຍັງລໍຖ້າກວດສອບ']);
            }

            $locked->update(['status' => WithdrawalStatus::Cancelled]);
            $this->reverseWithdrawal($locked, $streamer->user_id);
            $this->activity->log('withdrawal.cancelled', "Withdrawal #{$locked->id} cancelled", $locked, [], $streamer->user_id);

            return $locked;
        });
    }

    private function reverseWithdrawal(Withdrawal $withdrawal, int $userId): void
    {
        if (WalletTransaction::where('withdrawal_id', $withdrawal->id)
            ->where('type', WalletTransactionType::WithdrawalReversal->value)->exists()) {
            return;
        }

        $this->append(
            streamerId: $withdrawal->streamer_id,
            type: WalletTransactionType::WithdrawalReversal,
            amount: (float) $withdrawal->amount,
            description: "Released reserved withdrawal #{$withdrawal->id}",
            userId: $userId,
            withdrawalId: $withdrawal->id,
        );
    }

    private function append(
        int $streamerId,
        WalletTransactionType $type,
        float $amount,
        ?string $description,
        ?int $userId,
        ?int $donationId = null,
        ?int $withdrawalId = null,
    ): WalletTransaction {
        $balance = (float) WalletTransaction::where('streamer_id', $streamerId)->sum('amount');

        return WalletTransaction::create([
            'streamer_id' => $streamerId,
            'donation_id' => $donationId,
            'withdrawal_id' => $withdrawalId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => round($balance + $amount, 2),
            'description' => $description,
            'created_by' => $userId,
        ]);
    }
}
