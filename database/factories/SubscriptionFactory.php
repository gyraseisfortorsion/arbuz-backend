<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'delivery_day' => $this->faker->numberBetween(1, 7), // Генерация случайного дня доставки (1-7)
            'delivery_period' => $this->faker->randomElement(['утро', 'обед', 'вечер']), // Генерация случайного периода доставки
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'subscription_duration' => $this->faker->randomElement(['1 месяц', '3 месяца', '6 месяцев', '1 год']), // Генерация случайной продолжительности подписки
            'price_limit' => $this->faker->randomElement([2500,5000,10000,15000,20000]),
        ];
    }
    
    
}
