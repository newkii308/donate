<?php

namespace App\Services;

use App\Enums\DonationStatus;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationService
{
    public function __construct(
        private readonly NotificationQueueService $queue,
        private readonly ActivityLogService $activity,
    ) {}

    /**
     * Record a transfer claim. Money and overlay credit remain pending until
     * an administrator confirms that the central account received the funds.
     *
     * @param  array{donor_name:string, amount:float|string, transfer_reference:string, message?:?string, is_anonymous?:bool}  $data
     */
    public function create(Streamer $streamer, array $data, ?string $ip = null): Donation
    {
        return DB::transaction(function () use ($streamer, $data, $ip) {
            $donation = $streamer->donations()->create([
                'donor_name' => $data['donor_name'],
                'amount' => $data['amount'],
                'currency' => $streamer->currency,
                'transfer_reference' => $data['transfer_reference'],
                'message' => $data['message'] ?? null,
                'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
                'status' => DonationStatus::Pending->value,
                'ip_address' => $ip,
            ]);

            $this->activity->log(
                action: 'donation.submitted',
                description: "Pending transfer claim of {$donation->amount} {$donation->currency} to {$streamer->display_name}",
                subject: $donation,
                properties: [
                    'amount' => (float) $donation->amount,
                    'currency' => $donation->currency,
                    'transfer_reference' => $donation->transfer_reference,
                ],
            );

            return $donation;
        });
    }

    public function verify(Donation $donation, User $admin, WalletService $wallet): Donation
    {
        return DB::transaction(function () use ($donation, $admin, $wallet) {
            $locked = Donation::query()->lockForUpdate()->findOrFail($donation->id);

            if ($locked->status !== DonationStatus::Pending) {
                throw ValidationException::withMessages([
                    'donation' => 'ກວດສອບໄດ້ສະເພາະລາຍການທີ່ຍັງລໍຖ້າ',
                ]);
            }

            $locked->update([
                'status' => DonationStatus::Completed,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'rejection_reason' => null,
            ]);

            $wallet->creditDonation($locked, $admin);

            if ($goal = $locked->streamer->donationGoal()->first()) {
                $goal->increment('current_amount', $locked->amount);
            }

            $this->queue->enqueueDonation($locked);
            $this->activity->log(
                'donation.verified',
                "Donation #{$locked->id} verified and credited",
                $locked,
                ['amount' => (float) $locked->amount, 'net_amount' => (float) $locked->net_amount],
            );

            return $locked;
        });
    }

    public function reject(Donation $donation, User $admin, string $reason): Donation
    {
        return DB::transaction(function () use ($donation, $admin, $reason) {
            $locked = Donation::query()->lockForUpdate()->findOrFail($donation->id);

            if ($locked->status !== DonationStatus::Pending) {
                throw ValidationException::withMessages([
                    'donation' => 'ປະຕິເສດໄດ້ສະເພາະລາຍການທີ່ຍັງລໍຖ້າ',
                ]);
            }

            $locked->update([
                'status' => DonationStatus::Rejected,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'rejection_reason' => $reason,
            ]);

            $this->activity->log(
                'donation.rejected',
                "Donation #{$locked->id} rejected",
                $locked,
                ['reason' => $reason],
            );

            return $locked;
        });
    }
}
