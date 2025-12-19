<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'brand' => $this->faker->optional()->company(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 50000),
            'condition' => $this->faker->randomElement(['新品', '未使用に近い', '目立った傷や汚れなし']),
            'image_path' => 'items/sample.jpg',
        ];
    }
}
