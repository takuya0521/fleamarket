<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'payment_method' => $this->faker->randomElement(['カード', 'コンビニ']),
            'shipping_postal_code' => '1234567',
            'shipping_address' => '東京都渋谷区1-1-1',
            'shipping_building' => 'テストビル101',
            'stripe_session_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['paid', 'pending']),
            'purchased_at' => now(),
        ];
    }
}
