<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->input('action')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where('description', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'search' => $request->input('search'),
            'action' => $request->input('action'),
        ]);
    }
}
