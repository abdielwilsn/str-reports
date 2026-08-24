<?php

namespace Database\Factories;

use App\Models\Cryptocurrency;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deposit>
 */
class DepositFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomFloat(8, 10, 5000);

        return [
            'user_id' => User::factory(),
            'cryptocurrency_id' => Cryptocurrency::factory(),
            'transaction_hash' => fake()->sha256(),
            'from_address' => fake()->regexify('0x[a-f0-9]{40}'),
            'to_address' => fake()->regexify('0x[a-f0-9]{40}'),
            'expected_amount' => $amount,
            'actual_amount' => $amount,
            'amount' => $amount,
            'status' => Deposit::STATUS_CONFIRMED,
            'confirmations' => fake()->numberBetween(12, 60),
            'verified_at' => fake()->dateTimeBetween('-6 months'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'confirmations' => 0,
            'verified_at' => null,
        ]);
    }
}
