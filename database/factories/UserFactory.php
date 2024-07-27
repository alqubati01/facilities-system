<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function newUser(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
              'name' => 'admin',
              'username' => 'admin',
              'email' => 'admin@gmail.com',
              'is_active' => 1,
            ];
        });
    }

  public function newUser2(): Factory
  {
    return $this->state(function (array $attributes) {
      return [
        'name' => 'احمد لطفي',
        'username' => 'ahmed',
//        'email' => 'ahmed@gmail.com',
        'email' => null,
        'is_active' => 1,
      ];
    });
  }

  public function newUser3(): Factory
  {
    return $this->state(function (array $attributes) {
      return [
        'name' => 'فتحي العكيش',
        'username' => 'fathi',
        'email' => 'fathi@gmail.com',
        'is_active' => 1,
      ];
    });
  }
}
