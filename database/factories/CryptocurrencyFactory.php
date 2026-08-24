<?php

namespace Database\Factories;

use App\Models\Cryptocurrency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cryptocurrency>
 */
class CryptocurrencyFactory extends Factory
{
    public function definition(): array
    {
        $coin = fake()->randomElement([
            ['name' => 'Bitcoin', 'symbol' => 'BTC', 'network' => 'bitcoin', 'type' => 'native'],
            ['name' => 'Ethereum', 'symbol' => 'ETH', 'network' => 'ethereum', 'type' => 'native'],
            ['name' => 'Tether', 'symbol' => 'USDT', 'network' => 'ethereum', 'type' => 'token'],
        ]);

        return [
            'name' => $coin['name'],
            'symbol' => $coin['symbol'],
            'network' => $coin['network'],
            'type' => $coin['type'],
            'contract_address' => $coin['type'] === 'token' ? fake()->regexify('0x[a-f0-9]{40}') : null,
            'decimals' => 18,
            'required_confirmations' => 12,
            'is_active' => true,
        ];
    }
}
