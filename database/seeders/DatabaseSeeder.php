<?php

namespace Database\Seeders;

use App\Models\Cryptocurrency;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                    ])
                    ->each(function (Deposit $deposit) {
                        if (fake()->boolean(70)) {
                            $this->attachDemoProof($deposit);
                        }
                    });

                Withdrawal::factory()
                    ->count(fake()->numberBetween(0, 4))
                    ->for($user)
                    ->create();
            });
    }

    /**
     * Generate a placeholder "proof of payment" image so the deposit detail
     * page has something to display without needing real uploaded files.
     */
    private function attachDemoProof(Deposit $deposit): void
    {
        $filename = 'demo_'.$deposit->id.'_'.Str::random(8).'.png';

        $image = imagecreatetruecolor(640, 400);
        imagefill($image, 0, 0, imagecolorallocate($image, 15, 23, 32));
        imagefilledrectangle($image, 24, 24, 616, 96, imagecolorallocate($image, 16, 185, 129));
        $white = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 40, 48, 'PROOF OF PAYMENT', $white);
        imagestring($image, 4, 40, 140, 'Amount: $'.number_format((float) $deposit->amount, 2), $white);
        imagestring($image, 4, 40, 170, 'Deposit #'.$deposit->id, $white);
        imagestring($image, 3, 40, 200, 'Tx: '.Str::limit((string) $deposit->transaction_hash, 40), $white);

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put('proofs/'.$filename, $contents);

        $deposit->update(['proof_of_payment' => $filename]);
    }
}
