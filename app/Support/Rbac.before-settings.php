<?php

namespace App\Support;

use App\Models\User;

/**
 * Single source of truth for role-based navigation & permissions.
 * Mirrors the original React app's src/lib/rbac.js exactly.
 */
class Rbac
{
    public const ALL_ROLES = [
        User::ROLE_EMPLOYEE,
        User::ROLE_RECEPTIONIST,
        User::ROLE_ADMIN_OFFICER,
        User::ROLE_MANAGER,
        User::ROLE_LEGAL_OFFICER,
        User::ROLE_SYS_ADMIN,
    ];

    /** route name => allowed roles */
    public const NAV = [
        'dashboard' => self::ALL_ROLES,
        'facilities.index' => self::ALL_ROLES,
        'reservations.index' => self::ALL_ROLES,
        'appointments.index' => ['employee', 'receptionist', 'admin_officer', 'manager', 'sys_admin'],
        'visitors.index' => ['employee', 'receptionist', 'admin_officer', 'manager', 'sys_admin'],
        'documents.index' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'retention.index' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'legal.index' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'contracts.index' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'legal-dashboard.index' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'reports.index' => ['admin_officer', 'manager', 'sys_admin'],
        'audit-trail.index' => ['manager', 'sys_admin'],
        'users.index' => ['sys_admin'],
    ];

    /** permission key => allowed roles (matches PERMISSIONS in rbac.js) */
    public const PERMISSIONS = [
        'manageFacilities' => ['admin_officer', 'sys_admin'],
        'viewArchivedFacilities' => ['admin_officer', 'manager', 'sys_admin'],
        'decideReservations' => ['admin_officer', 'manager', 'sys_admin'],
        'operateVisitorDesk' => ['receptionist', 'admin_officer', 'sys_admin'],
        'useAiAssist' => ['receptionist', 'admin_officer', 'manager', 'sys_admin'],
        'manageDocuments' => ['admin_officer', 'sys_admin'],
        'viewDocuments' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'viewVisitors' => ['employee', 'receptionist', 'admin_officer', 'manager', 'sys_admin'],
        'viewAppointments' => ['employee', 'receptionist', 'admin_officer', 'manager', 'sys_admin'],
        'viewLegal' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'viewContracts' => ['admin_officer', 'manager', 'legal_officer', 'sys_admin'],
        'manageAppointments' => ['admin_officer', 'manager', 'sys_admin'],
        'viewConfidential' => ['admin_officer', 'legal_officer', 'manager', 'sys_admin'],
        'manageRetention' => ['admin_officer', 'sys_admin'],
        'manageLegal' => ['admin_officer', 'legal_officer', 'sys_admin'],
        'reviewLegal' => ['legal_officer', 'sys_admin'],
        'manageContracts' => ['admin_officer', 'sys_admin'],
        'approveContracts' => ['manager', 'sys_admin'],
        'manageUsers' => ['sys_admin'],
    ];

    public static function navFor(?string $role): array
    {
        $role = $role ?: 'employee';

        return array_keys(array_filter(self::NAV, fn ($roles) => in_array($role, $roles, true)));
    }

    public static function canAccessRoute(string $routeName, ?string $role): bool
    {
        $role = $role ?: 'employee';
        if (! array_key_exists($routeName, self::NAV)) {
            return true;
        }

        return in_array($role, self::NAV[$routeName], true);
    }

    public static function can(string $action, ?string $role): bool
    {
        return in_array($role, self::PERMISSIONS[$action] ?? [], true);
    }
}
