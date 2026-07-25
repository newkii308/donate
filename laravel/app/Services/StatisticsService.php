<?php

namespace App\Services;

use App\Enums\DonationStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StatisticsService
{
    /**
     * Aggregate dashboard statistics for a streamer.
     *
     * @return array<string, mixed>
     */
    public function forStreamer(Streamer $streamer): array
    {
        $base = fn () => Donation::completed()->forStreamer($streamer->id);

        $today = (clone $base())->whereDate('created_at', Carbon::today());
        $month = (clone $base())->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);

        return [
            'currency' => $streamer->currency,
            'today_total' => (float) (clone $today)->sum('amount'),
            'today_count' => (clone $today)->count(),
            'month_total' => (float) (clone $month)->sum('amount'),
            'month_count' => (clone $month)->count(),
            'total' => (float) (clone $base())->sum('amount'),
            'count' => (clone $base())->count(),
            'average' => (float) (clone $base())->avg('amount'),
            'largest' => (float) (clone $base())->max('amount'),
        ];
    }

    /**
     * @return Collection<int, Donation>
     */
    public function recentDonations(Streamer $streamer, int $limit = 10): Collection
    {
        return Donation::completed()
            ->forStreamer($streamer->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Daily totals for the last N days (for a simple dashboard chart).
     *
     * @return array<int, array{date:string, total:float}>
     */
    public function dailyTotals(Streamer $streamer, int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $rows = Donation::completed()
            ->forStreamer($streamer->id)
            ->where('created_at', '>=', $start)
            ->get(['amount', 'created_at'])
            ->groupBy(fn (Donation $d) => $d->created_at->toDateString())
            ->map(fn ($group) => (float) $group->sum('amount'));

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $series[] = ['date' => $date, 'total' => $rows->get($date, 0.0)];
        }

        return $series;
    }

    /**
     * Admin-wide platform statistics.
     *
     * @return array<string, mixed>
     */
    public function platform(): array
    {
        $base = fn () => Donation::completed();

        return [
            'total_amount' => (float) (clone $base())->sum('amount'),
            'total_count' => (clone $base())->count(),
            'today_amount' => (float) (clone $base())->whereDate('created_at', Carbon::today())->sum('amount'),
            'streamers' => Streamer::count(),
            'active_streamers' => Streamer::active()->count(),
            'pending_donations' => Donation::where('status', DonationStatus::Pending->value)->count(),
            'wallet_liability' => (float) WalletTransaction::sum('amount'),
            'pending_withdrawals' => Withdrawal::whereIn('status', [
                WithdrawalStatus::Pending->value,
                WithdrawalStatus::Approved->value,
            ])->count(),
        ];
    }
}
