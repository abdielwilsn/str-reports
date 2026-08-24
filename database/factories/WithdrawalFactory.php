<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 3000);
        $fee = round($amount * 0.02, 2);

        return [
            'user_id' => User::factory(),
            'amount' => $amount,
            'method' => fake()->randomElement(['crypto', 'bank_transfer']),
            'fee' => $fee,
            'net_amount' => $amount - $fee,
            'currency' => 'USD',
            'status' => Withdrawal::STATUS_COMPLETED,
            'wallet_address' => fake()->regexify('0x[a-f0-9]{40}'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}
