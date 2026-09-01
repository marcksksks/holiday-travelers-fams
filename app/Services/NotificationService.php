<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    /**
     * @param  array<int, array{recipient_email:?string,title:string,body?:string,module?:string,severity?:string,link?:string}>  $items
     */
    public function notify(array $items): void
    {
        $rows = collect($items)
            ->filter(fn ($i) => ! empty($i['recipient_email']))
            ->map(fn ($i) => array_merge([
                'body' => null,
                'module' => 'system',
                'severity' => 'info',
                'link' => null,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ], $i))
            ->all();

        if ($rows) {
            AppNotification::insert($rows);
        }
    }

    public function usersByRole(string $role): \Illuminate\Support\Collection
    {
        return User::where('app_role', $role)->where('is_active', true)->get();
    }
}
