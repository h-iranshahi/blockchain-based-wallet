<?php

namespace Iransh\Wallet\Database\Factories;

use Iransh\Wallet\Models\Wallet; 
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), 
            'balance' => $this->faker->numberBetween(0, 1000), 
        ];
    }
}
