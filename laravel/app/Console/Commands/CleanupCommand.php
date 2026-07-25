<?php

namespace App\Console\Commands;

use App\Enums\NotificationStatus;
use App\Models\NotificationQueue;
use App\Services\TtsService;
use Illuminate\Console\Command;

class CleanupCommand extends Command
{
    protected $signature = 'newlab:cleanup {--days=2 : Remove completed notifications older than this many days}';

    protected $description = 'Prune expired TTS cache and old completed notifications (run from a Cron job)';

    public function handle(TtsService $tts): int
    {
        $prunedTts = $tts->pruneExpired();
        $this->info("Pruned {$prunedTts} expired TTS cache entries.");

        $deleted = NotificationQueue::where('status', NotificationStatus::Completed->value)
            ->where('created_at', '<', now()->subDays((int) $this->option('days')))
            ->delete();
        $this->info("Removed {$deleted} old completed notifications.");

        return self::SUCCESS;
    }
}
