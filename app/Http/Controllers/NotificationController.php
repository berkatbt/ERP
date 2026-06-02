<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = AuditLog::where('branch_id', $user->branch_id)
            ->where('action', 'stock_minimum')
            ->with(['branch', 'auditable'])
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }
}
