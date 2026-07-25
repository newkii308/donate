<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Record an activity log entry.
     *
     * @param  array<string, mixed>  $properties
     */
    public function log(string $action, ?string $description = null, ?Model $subject = null, array $properties = [], ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 1000),
        ]);
    }
}
