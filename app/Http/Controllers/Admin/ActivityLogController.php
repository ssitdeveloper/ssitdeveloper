<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(50);
        return view('admin.activity-logs.index', ['logs' => $logs]);
    }

    public function show(ActivityLog $log)
    {
        return view('admin.activity-logs.show', ['log' => $log]);
    }

    public function clearOldLogs()
    {
        // Delete logs older than 90 days
        ActivityLog::where('created_at', '<', now()->subDays(90))->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Old logs cleared successfully');
    }
}
