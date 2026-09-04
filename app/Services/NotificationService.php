<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create ordinary notifications.
     *
     * @param array<int, array<string, mixed>> $items
     */
    public function notify(array $items): void
    {
        $rows = collect($items)
            ->filter(
                fn ($item) =>
                    ! empty($item['recipient_email'])
            )
            ->map(
                fn ($item) =>
                    $this->makeRow($item)
            )
            ->all();

        if ($rows) {
            AppNotification::insert($rows);
        }
    }


    /**
     * Create a notification only once for
     * each recipient and event key.
     *
     * @param array<int, array<string, mixed>> $items
     */
    public function notifyOnce(array $items): void
    {
        foreach ($items as $item) {

            if (
                empty($item['recipient_email']) ||
                empty($item['key'])
            ) {
                continue;
            }

            $dedupeKey = hash(
                'sha256',
                strtolower(
                    (string) $item['recipient_email']
                )
                . '|'
                . (string) $item['key']
            );

            $row = $this->makeRow($item);

            $row['dedupe_key'] = $dedupeKey;

            AppNotification::firstOrCreate(
                [
                    'dedupe_key' => $dedupeKey,
                ],
                $row
            );
        }
    }


    public function usersByRole(
        string $role
    ): Collection {
        return User::query()
            ->where(
                'app_role',
                $role
            )
            ->where(
                'is_active',
                true
            )
            ->get();
    }


    /**
     * Return active users who have
     * the requested RBAC permission.
     */
    public function usersWithPermission(
        string $permission
    ): Collection {
        return User::query()
            ->where(
                'is_active',
                true
            )
            ->get()
            ->filter(
                fn (User $user) =>
                    $user->can($permission)
            )
            ->values();
    }


    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    private function makeRow(
        array $item
    ): array {
        unset($item['key']);

        return array_merge(
            [
                'body' => null,
                'module' => 'system',
                'severity' => 'info',
                'link' => null,
                'is_read' => false,
                'dedupe_key' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            $item
        );
    }
}