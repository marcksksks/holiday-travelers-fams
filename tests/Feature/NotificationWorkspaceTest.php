<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class NotificationWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()
            ->role(User::ROLE_EMPLOYEE)
            ->create([
                'email' => 'notification-user@example.test',
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function notification(
        User $user,
        string $title,
        bool $read = false,
        string $module = 'facilities'
    ): AppNotification {
        return AppNotification::create([
            'recipient_email' => $user->email,
            'title' => $title,
            'body' => 'Notification workspace regression message.',
            'module' => $module,
            'severity' => 'info',
            'link' => '/dashboard',
            'is_read' => $read,
        ]);
    }

    public function test_notification_center_uses_shared_workspace_hierarchy(): void
    {
        $user =
            $this->user();

        $this->notification(
            $user,
            'Unread Facility Alert'
        );

        $this->notification(
            $user,
            'Reviewed Facility Alert',
            true
        );

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route('notifications.index')
                );

        $response
            ->assertOk()
            ->assertSee('Activity Center')
            ->assertSee('Notification Center')
            ->assertSee('Activity Summary')
            ->assertSee('Total Notifications')
            ->assertSee('Unread')
            ->assertSee('Read')
            ->assertSee('Notification Feed')
            ->assertSee('Unread Facility Alert')
            ->assertSee('Reviewed Facility Alert');
    }

    public function test_notification_center_only_displays_current_users_notifications(): void
    {
        $user =
            $this->user();

        $this->notification(
            $user,
            'My Notification'
        );

        AppNotification::create([
            'recipient_email' => 'another-user@example.test',
            'title' => 'Another User Notification',
            'body' => 'Must not appear.',
            'module' => 'legal',
            'severity' => 'warning',
            'link' => '/legal',
            'is_read' => false,
        ]);

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route('notifications.index')
                );

        $response
            ->assertOk()
            ->assertViewHas(
                'notifications',
                function ($notifications): bool {
                    if (
                        ! $notifications
                        instanceof LengthAwarePaginator
                    ) {
                        return false;
                    }

                    $titles =
                        $notifications
                            ->getCollection()
                            ->pluck('title');

                    return $titles->contains(
                        'My Notification'
                    )
                        && ! $titles->contains(
                            'Another User Notification'
                        );
                }
            );
    }

    public function test_notification_center_preserves_status_filtering(): void
    {
        $user =
            $this->user();

        $this->notification(
            $user,
            'Unread Reservation Request'
        );

        $this->notification(
            $user,
            'Read Reservation Request',
            true
        );

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route(
                        'notifications.index',
                        [
                            'status' => 'unread',
                        ]
                    )
                );

        $response
            ->assertOk()
            ->assertViewHas(
                'status',
                'unread'
            )
            ->assertViewHas(
                'notifications',
                function ($notifications): bool {
                    if (
                        ! $notifications
                        instanceof LengthAwarePaginator
                    ) {
                        return false;
                    }

                    $titles =
                        $notifications
                            ->getCollection()
                            ->pluck('title');

                    return $titles->contains(
                        'Unread Reservation Request'
                    )
                        && ! $titles->contains(
                            'Read Reservation Request'
                        );
                }
            );
    }

    public function test_notification_center_preserves_search(): void
    {
        $user =
            $this->user();

        $this->notification(
            $user,
            'Contract Approval Ready',
            false,
            'contracts'
        );

        $this->notification(
            $user,
            'Visitor Checked In',
            false,
            'visitors'
        );

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route(
                        'notifications.index',
                        [
                            'q' => 'contract',
                        ]
                    )
                );

        $response
            ->assertOk()
            ->assertViewHas(
                'search',
                'contract'
            )
            ->assertViewHas(
                'notifications',
                function ($notifications): bool {
                    if (
                        ! $notifications
                        instanceof LengthAwarePaginator
                    ) {
                        return false;
                    }

                    $titles =
                        $notifications
                            ->getCollection()
                            ->pluck('title');

                    return $titles->contains(
                        'Contract Approval Ready'
                    )
                        && ! $titles->contains(
                            'Visitor Checked In'
                        );
                }
            )
            ->assertSee(
                'Search results for'
            );
    }

    public function test_notification_view_uses_shared_design_components(): void
    {
        $source =
            file_get_contents(
                resource_path(
                    'views/notifications/index.blade.php'
                )
            );

        $this->assertStringContainsString(
            '<x-page-header',
            $source
        );

        $this->assertStringContainsString(
            '<x-section-header',
            $source
        );

        $this->assertStringContainsString(
            '<x-metric-card',
            $source
        );
    }
}
