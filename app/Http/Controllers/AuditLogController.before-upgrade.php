<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::when($request->filled('module'), fn ($q) => $q->where('module', $request->string('module')))
            ->when($request->filled('actor_email'), fn ($q) => $q->where('actor_email', 'like', '%'.$request->string('actor_email').'%'))
            ->orderByDesc('created_at')
            ->paginate(30)->withQueryString();

        return view('audit.index', compact('logs'));
    }
}
