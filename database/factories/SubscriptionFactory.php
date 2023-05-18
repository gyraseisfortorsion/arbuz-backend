<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'delivery_day' => $this->faker->numberBetween(1, 7), // Генерация случайного дня доставки (1-7)
            'delivery_period' => $this->faker->randomElement(['morning', 'afternoon', 'evening']), // Генерация случайного периода доставки
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'subscription_duration' => $this->faker->randomElement(['1 week', '2 weeks', '1 month']), // Генерация случайной продолжительности подписки
        ];
    }
}
