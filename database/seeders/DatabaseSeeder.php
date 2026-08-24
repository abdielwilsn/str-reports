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
     * Filenames that actually exist in the main Starbiit app's
     * storage/app/public/proofs — used here only so seeded demo deposits
     * resolve to a real image via Deposit::proofOfPaymentUrl() instead of
     * a 404. Not meaningful outside local development.
     */
    private const DEMO_PROOF_FILENAMES = [
        '1763710282_gCBwrTbWgD4x.png',
        '1764064194_UvE2XTQarGRI.png',
        '1764593389_9tAUoCocAndn.png',
        '1765366027_Gg1mhamD4C3u.jpeg',
        '1767388109_aLTn0G43Rwbb.jpeg',
        '1iHOr4Ff6ft2j3SpzGkCChGkyMuZgND6rtUaSEwk.png',
        'QfsgtEHKIFATpHzp9BfK4IFH3G2INDRj7x3i3YHy.jpg',
        'cW14MTVe7mVxHvFK4PqiL5zL0rWIXtu1AFMsbMMK.jpg',
    ];

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
                        'proof_of_payment' => fn () => fake()->boolean(70)
                            ? fake()->randomElement(self::DEMO_PROOF_FILENAMES)
                            : null,
                    ]);

                Withdrawal::factory()
                    ->count(fake()->numberBetween(0, 4))
                    ->for($user)
                    ->create();
            });
    }
}
