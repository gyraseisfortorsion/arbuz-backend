<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionsTableSeeder extends Seeder
{
    public function run()
    {
        Subscription::factory()->count(5)->create();
    }
}
