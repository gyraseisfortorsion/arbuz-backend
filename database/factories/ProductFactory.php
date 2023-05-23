<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'weight' => $this->faker->randomFloat(2, 0.1, 10), // Генерация случайного веса
            'quantity' => $this->faker->numberBetween(1, 10),
            'price_per_kilo' => $this->faker->randomFloat(2, 0.1, 100),
            'price_per_item' => $this->faker->randomFloat(2, 0.1, 100),
        ];
    }
}