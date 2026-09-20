<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'app_role' => User::ROLE_EMPLOYEE,
            'is_active' => true,
            'force_password_change' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(
            function (User $user): void {
                if (
                    ! app()->runningUnitTests() ||
                    ! $user->requiresMandatoryMfa() ||
                    $user->hasTwoFactorEnabled()
                ) {
                    return;
                }

                $user->createTwoFactorAuth();

                $user->confirmTwoFactorAuth(
                    $user->makeTwoFactorCode()
                );
            }
        );
    }

    public function role(string $role): static
    {
        return $this->state(fn () => ['app_role' => $role]);
    }
}
