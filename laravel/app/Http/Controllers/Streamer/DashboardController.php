<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use App\Services\WalletService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use InteractsWithStreamer;

    public function __construct(
        private readonly StatisticsService $stats,
        private readonly WalletService $wallet,
    ) {}

    public function index(): View
    {
        $streamer = $this->streamer();

        return view('streamer.dashboard', [
            'streamer' => $streamer,
            'stats' => $this->stats->forStreamer($streamer),
            'recent' => $this->stats->recentDonations($streamer, 8),
            'chart' => $this->stats->dailyTotals($streamer, 7),
            'walletBalance' => $this->wallet->balance($streamer),
        ]);
    }
}
