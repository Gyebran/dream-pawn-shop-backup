<?php

namespace Database\Factories;

use App\Models\Admin\Admin;
use App\Models\Member\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'member',
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            if ($user->role === 'admin') {
                Admin::firstOrCreate(['user_id' => $user->id]);
            } elseif ($user->role === 'member') {
                Member::firstOrCreate(['user_id' => $user->id]);
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function member(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'member']);
    }
}
