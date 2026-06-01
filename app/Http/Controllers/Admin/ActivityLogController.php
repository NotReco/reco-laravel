<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action_filter')) {
            $query->where('action', $request->input('action_filter'));
        }

        // Distinct actions for the filter dropdown
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.activity_logs.index', compact('logs', 'actions'));
    }
}
