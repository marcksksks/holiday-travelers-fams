<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    public function log(
        User $actor,
        string $action,
        string $module,
        ?string $recordLabel = null,
        ?string $recordId = null,
        ?string $details = null
    ): AuditLog {
        return AuditLog::create([
            'actor_email' => $actor->email,
            'actor_role' => $actor->app_role,
            'action' => $action,
            'module' => $module,
            'record_label' => $recordLabel,
            'record_id' => $recordId,
            'details' => $details,
            'created_at' => now(),
        ]);
    }
}