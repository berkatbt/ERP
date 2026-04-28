<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $logs = AuditLog::latest()
            ->whereIn('action', ['login', 'logout'])
            ->with('user')
            ->paginate(15);

        return view('logs.audit_log', [
            'logs' => $logs,
        ]);
    }
}
