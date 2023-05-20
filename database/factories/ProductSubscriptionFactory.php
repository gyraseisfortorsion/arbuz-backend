<?php

namespace Database\Factories;
use App\Models\ProductSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductSubscriptionFactory extends Factory
{
    protected $model = ProductSubscription::class;

    public function definition()
    {
        $product = \App\Models\Product::inRandomOrder()->first();

        return [
            'product_id' => $product->id,
            'subscription_id' => \App\Models\Subscription::inRandomOrder()->first()->id,
            'weight' => $product->weight,
            'quantity' => $product->quantity,
        ];
    }
}
