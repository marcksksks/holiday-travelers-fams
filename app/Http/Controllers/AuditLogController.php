<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'actor_email' => ['nullable', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = AuditLog::query()
            ->when(
                $request->filled('module'),
                fn ($q) => $q->where('module', $request->string('module'))
            )
            ->when(
                $request->filled('action'),
                fn ($q) => $q->where('action', $request->string('action'))
            )
            ->when(
                $request->filled('actor_email'),
                fn ($q) => $q->where(
                    'actor_email',
                    'like',
                    '%'.$request->string('actor_email').'%'
                )
            )
            ->when(
                $request->filled('from'),
                fn ($q) => $q->where(
                    'created_at',
                    '>=',
                    $request->date('from')->startOfDay()
                )
            )
            ->when(
                $request->filled('to'),
                fn ($q) => $q->where(
                    'created_at',
                    '<=',
                    $request->date('to')->endOfDay()
                )
            );

        $logs = (clone $query)
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $moduleOptions = AuditLog::query()
            ->whereNotNull('module')
            ->where('module', '<>', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actionOptions = AuditLog::query()
            ->whereNotNull('action')
            ->where('action', '<>', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $stats = [
            'total' => AuditLog::count(),

            'today' => AuditLog::whereDate(
                'created_at',
                today()
            )->count(),

            'actors' => AuditLog::query()
                ->whereNotNull('actor_email')
                ->distinct('actor_email')
                ->count('actor_email'),

            'modules' => AuditLog::query()
                ->whereNotNull('module')
                ->distinct('module')
                ->count('module'),
        ];

        return view('audit.index', compact(
            'logs',
            'moduleOptions',
            'actionOptions',
            'stats'
        ));
    }
}