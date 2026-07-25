<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Services\StatisticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly StatisticsService $stats)
    {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats->platform(),
            'recentDonations' => Donation::with('streamer')->latest()->limit(8)->get(),
            'recentActivity' => ActivityLog::with('user')->latest()->limit(10)->get(),
        ]);
    }
}
