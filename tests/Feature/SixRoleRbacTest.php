<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SixRoleRbacTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_web_module_access_matches_role_matrix(): void
    {
        $matrix = [
            User::ROLE_EMPLOYEE => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/appointments',
                    '/visitors',
                ],
                'forbidden' => [
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                    '/reports',
                    '/audit-trail',
                    '/users',
                ],
            ],

            User::ROLE_RECEPTIONIST => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/appointments',
                    '/visitors',
                ],
                'forbidden' => [
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                    '/reports',
                    '/audit-trail',
                    '/users',
                ],
            ],

            User::ROLE_ADMIN_OFFICER => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/appointments',
                    '/visitors',
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                    '/reports',
                ],
                'forbidden' => [
                    '/audit-trail',
                    '/users',
                ],
            ],

            User::ROLE_MANAGER => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/appointments',
                    '/visitors',
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                    '/reports',
                    '/audit-trail',
                ],
                'forbidden' => [
                    '/users',
                ],
            ],

            User::ROLE_LEGAL_OFFICER => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                ],
                'forbidden' => [
                    '/appointments',
                    '/visitors',
                    '/reports',
                    '/audit-trail',
                    '/users',
                ],
            ],

            User::ROLE_SYS_ADMIN => [
                'allowed' => [
                    '/dashboard',
                    '/facilities',
                    '/reservations',
                    '/appointments',
                    '/visitors',
                    '/documents',
                    '/retention',
                    '/legal',
                    '/contracts',
                    '/reports',
                    '/audit-trail',
                    '/users',
                ],
                'forbidden' => [],
            ],
        ];

        foreach ($matrix as $role => $access) {
            $user = $this->user($role);

            foreach ($access['allowed'] as $url) {
                $this->actingAs($user)
                    ->get($url)
                    ->assertOk();
            }

            foreach ($access['forbidden'] as $url) {
                $this->actingAs($user)
                    ->get($url)
                    ->assertForbidden();
            }
        }
    }

    public function test_api_read_access_matches_role_matrix(): void
    {
        $matrix = [
            User::ROLE_EMPLOYEE => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/appointments',
                    '/api/visitors',
                ],
                'forbidden' => [
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                    '/api/users',
                ],
            ],

            User::ROLE_RECEPTIONIST => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/appointments',
                    '/api/visitors',
                ],
                'forbidden' => [
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                    '/api/users',
                ],
            ],

            User::ROLE_ADMIN_OFFICER => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/appointments',
                    '/api/visitors',
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                ],
                'forbidden' => [
                    '/api/users',
                ],
            ],

            User::ROLE_MANAGER => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/appointments',
                    '/api/visitors',
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                ],
                'forbidden' => [
                    '/api/users',
                ],
            ],

            User::ROLE_LEGAL_OFFICER => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                ],
                'forbidden' => [
                    '/api/appointments',
                    '/api/visitors',
                    '/api/users',
                ],
            ],

            User::ROLE_SYS_ADMIN => [
                'allowed' => [
                    '/api/facilities',
                    '/api/reservations',
                    '/api/appointments',
                    '/api/visitors',
                    '/api/documents',
                    '/api/legal-records',
                    '/api/contracts',
                    '/api/users',
                ],
                'forbidden' => [],
            ],
        ];

        foreach ($matrix as $role => $access) {
            $user = $this->user($role);

            foreach ($access['allowed'] as $url) {
                $this->actingAs($user, 'sanctum')
                    ->getJson($url)
                    ->assertOk();
            }

            foreach ($access['forbidden'] as $url) {
                $this->actingAs($user, 'sanctum')
                    ->getJson($url)
                    ->assertForbidden();
            }
        }
    }

    public function test_sensitive_permissions_match_separation_of_duties(): void
    {
        $roles = [
            User::ROLE_EMPLOYEE,
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        $matrix = [
            'manageFacilities' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'decideReservations' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_MANAGER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageAppointments' => [
                User::ROLE_RECEPTIONIST,
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_MANAGER,
                User::ROLE_SYS_ADMIN,
            ],

            'operateVisitorDesk' => [
                User::ROLE_RECEPTIONIST,
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageDocuments' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageRetention' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'approveRetentionDisposal' => [
                User::ROLE_MANAGER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageLegal' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_LEGAL_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'reviewLegal' => [
                User::ROLE_LEGAL_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageContracts' => [
                User::ROLE_ADMIN_OFFICER,
                User::ROLE_SYS_ADMIN,
            ],

            'approveContracts' => [
                User::ROLE_MANAGER,
                User::ROLE_SYS_ADMIN,
            ],

            'manageUsers' => [
                User::ROLE_SYS_ADMIN,
            ],
        ];

        foreach ($matrix as $permission => $allowedRoles) {
            foreach ($roles as $role) {
                $expected = in_array(
                    $role,
                    $allowedRoles,
                    true
                );

                $this->assertSame(
                    $expected,
                    Rbac::can(
                        $permission,
                        $role
                    ),
                    "{$role} has incorrect {$permission} permission."
                );
            }
        }
    }
}