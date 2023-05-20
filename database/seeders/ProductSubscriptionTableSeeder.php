<?php

namespace Database\Seeders;

use App\Models\ProductSubscription;
use Illuminate\Database\Seeder;

class ProductSubscriptionTableSeeder extends Seeder
{
    public function run()
    {
        ProductSubscription::factory()
            ->count(10) // Adjust the count as per your requirement
            ->create();
    }
}
