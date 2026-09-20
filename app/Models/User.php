<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\TwoFactorAuthentication;

class User extends Authenticatable implements TwoFactorAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthentication;

    public const ROLE_EMPLOYEE = 'employee';
    public const ROLE_RECEPTIONIST = 'receptionist';
    public const ROLE_ADMIN_OFFICER = 'admin_officer';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_LEGAL_OFFICER = 'legal_officer';
    public const ROLE_SYS_ADMIN = 'sys_admin';

    public const ROLES = [
        self::ROLE_EMPLOYEE => 'Employee / Staff',
        self::ROLE_RECEPTIONIST => 'Receptionist',
        self::ROLE_ADMIN_OFFICER => 'Administrative Officer',
        self::ROLE_MANAGER => 'General Manager',
        self::ROLE_LEGAL_OFFICER => 'Legal Officer',
        self::ROLE_SYS_ADMIN => 'System Administrator',
    ];

    protected $fillable = [
        'full_name', 'email', 'password', 'app_role',
        'department', 'job_title', 'phone', 'is_active', 'force_password_change',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'force_password_change' => 'boolean',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->app_role, $roles, true);
    }

    public function isSysAdmin(): bool
    {
        return $this->app_role === self::ROLE_SYS_ADMIN;
    }
}