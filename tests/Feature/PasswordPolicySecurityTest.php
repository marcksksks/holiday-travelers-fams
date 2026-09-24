<?php

namespace Tests\Feature;

use App\Http\Requests\RegisterRequest;
use App\Models\AccountRecoveryRequest;
use App\Models\User;
use App\Services\AccountRecoveryService;
use App\Services\UserManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PasswordPolicySecurityTest extends TestCase
{
    use RefreshDatabase;

    private const VALID_PASSWORD =
        'Aa1!23456789';

    private const CURRENT_PASSWORD =
        'CurrentPassword123!';

    public function test_default_password_policy_requires_twelve_characters_and_full_complexity(): void
    {
        foreach ([
            'Aa1!2345678',
            'lowercasepassword123!',
            'UPPERCASEPASSWORD123!',
            'NoNumberPassword!',
            'NoSymbolPassword123',
        ] as $password) {
            $validator =
                Validator::make(
                    [
                        'password' => $password,
                    ],
                    [
                        'password' => [
                            'required',
                            Password::defaults(),
                        ],
                    ]
                );

            $this->assertTrue(
                $validator->fails(),
                "Password should have been rejected: {$password}"
            );
        }

        $validator =
            Validator::make(
                [
                    'password' => self::VALID_PASSWORD,
                ],
                [
                    'password' => [
                        'required',
                        Password::defaults(),
                    ],
                ]
            );

        $this->assertFalse(
            $validator->fails()
        );
    }

    public function test_change_password_rejects_password_without_required_complexity(): void
    {
        $user =
            User::factory()
                ->create([
                    'password' => Hash::make(
                        self::CURRENT_PASSWORD
                    ),

                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->from(
                route(
                    'password.change'
                )
            )
            ->put(
                route(
                    'password.change.update'
                ),
                [
                    'current_password' => self::CURRENT_PASSWORD,

                    'password' => 'lowercasepassword123!',

                    'password_confirmation' => 'lowercasepassword123!',
                ]
            )
            ->assertRedirect(
                route(
                    'password.change'
                )
            )
            ->assertSessionHasErrors(
                'password'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                self::CURRENT_PASSWORD,
                $user->password
            )
        );
    }

    public function test_staff_creation_rejects_weak_temporary_password(): void
    {
        $administrator =
            User::factory()
                ->role(
                    User::ROLE_SYS_ADMIN
                )
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs(
                $administrator
            )
            ->from(
                route(
                    'users.index'
                )
            )
            ->post(
                route(
                    'users.store'
                ),
                [
                    'full_name' => 'Password Policy Test User',

                    'email' => 'password-policy@example.test',

                    'password' => 'lowercasepassword123!',

                    'app_role' => User::ROLE_EMPLOYEE,
                ]
            )
            ->assertRedirect(
                route(
                    'users.index'
                )
            )
            ->assertSessionHasErrors(
                'password'
            );

        $this->assertDatabaseMissing(
            'users',
            [
                'email' => 'password-policy@example.test',
            ]
        );
    }

    public function test_legacy_password_reset_rejects_weak_password(): void
    {
        $user =
            User::factory()
                ->create([
                    'password' => Hash::make(
                        self::CURRENT_PASSWORD
                    ),

                    'force_password_change' => false,
                ]);

        $token =
            PasswordBroker::broker()
                ->createToken(
                    $user
                );

        $this
            ->from(
                route(
                    'password.reset',
                    $token
                )
            )
            ->post(
                route(
                    'password.update'
                ),
                [
                    'token' => $token,

                    'email' => $user->email,

                    'password' => 'lowercasepassword123!',

                    'password_confirmation' => 'lowercasepassword123!',
                ]
            )
            ->assertSessionHasErrors(
                'password'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                self::CURRENT_PASSWORD,
                $user->password
            )
        );
    }

    public function test_secure_account_recovery_rejects_weak_password(): void
    {
        $user =
            User::factory()
                ->create([
                    'password' => Hash::make(
                        self::CURRENT_PASSWORD
                    ),

                    'force_password_change' => false,
                ]);

        $administrator =
            User::factory()
                ->role(
                    User::ROLE_SYS_ADMIN
                )
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->post(
                route(
                    'account-recovery.request'
                ),
                [
                    'email' => $user->email,
                ]
            )
            ->assertRedirect(
                route(
                    'account-recovery.status'
                )
            );

        $recovery =
            AccountRecoveryRequest::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->firstOrFail();

        app(
            AccountRecoveryService::class
        )->approve(
            $recovery,
            $administrator
        );

        $this
            ->post(
                route(
                    'account-recovery.update'
                ),
                [
                    'password' => 'lowercasepassword123!',

                    'password_confirmation' => 'lowercasepassword123!',
                ]
            )
            ->assertSessionHasErrors(
                'password'
            );

        $user->refresh();

        $this->assertTrue(
            Hash::check(
                self::CURRENT_PASSWORD,
                $user->password
            )
        );
    }

    public function test_user_management_service_enforces_central_password_policy(): void
    {
        $administrator =
            User::factory()
                ->role(
                    User::ROLE_SYS_ADMIN
                )
                ->create([
                    'force_password_change' => false,
                ]);

        try {
            app(
                UserManagementService::class
            )->create(
                $administrator,
                'direct-service-policy@example.test',
                'lowercasepassword123!',
                'Direct Service Policy Test',
                User::ROLE_EMPLOYEE
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'password',
                $exception->errors()
            );

            $this->assertDatabaseMissing(
                'users',
                [
                    'email' => 'direct-service-policy@example.test',
                ]
            );

            return;
        }

        $this->fail(
            'UserManagementService accepted a password that violates the centralized policy.'
        );
    }

    public function test_dormant_registration_request_uses_central_password_policy(): void
    {
        $request =
            new RegisterRequest;

        $validator =
            Validator::make(
                [
                    'full_name' => 'Dormant Registration Test',

                    'email' => 'dormant-registration@example.test',

                    'password' => 'Aa1!2345678',

                    'password_confirmation' => 'Aa1!2345678',
                ],
                $request->rules()
            );

        $this->assertTrue(
            $validator->fails()
        );

        $this->assertArrayHasKey(
            'password',
            $validator->errors()->toArray()
        );
    }

    public function test_active_password_interfaces_describe_twelve_character_policy(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $this
            ->actingAs($user)
            ->get(
                route(
                    'password.change'
                )
            )
            ->assertOk()
            ->assertSee(
                'at least 12 characters'
            );

        $activeViews = [
            resource_path(
                'views/auth/change-password.blade.php'
            ),

            resource_path(
                'views/auth/reset-password.blade.php'
            ),

            resource_path(
                'views/auth/account-recovery-reset.blade.php'
            ),

            resource_path(
                'views/users/_create-modal.blade.php'
            ),
        ];

        foreach ($activeViews as $view) {
            $source =
                file_get_contents(
                    $view
                );

            $this->assertIsString(
                $source
            );

            $this->assertStringNotContainsString(
                'Minimum 8 characters',
                $source
            );

            $this->assertStringNotContainsString(
                'at least 8 characters',
                $source
            );
        }
    }
}
