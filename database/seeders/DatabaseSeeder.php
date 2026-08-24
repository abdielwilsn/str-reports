<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $cryptocurrencies = Cryptocurrency::factory(3)->create();

        User::factory(20)
            ->create()
            ->each(function (User $user) use ($cryptocurrencies) {
                Deposit::factory()
                    ->count(fake()->numberBetween(1, 6))
                    ->for($user)
                    ->create([
                        'cryptocurrency_id' => fn () => $cryptocurrencies->random()->id,
                    ]);

                Withdrawal::factory()
                    ->count(fake()->numberBetween(0, 4))
                    ->for($user)
                    ->create();
            });
    }
}
