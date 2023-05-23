<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {

        Product::factory()->count(10)->create();
        $productNames = [
            'Яблоко',
            'Молоко',
            'Хлеб',
            'Масло',
            'Сахар',
            'Мясо',
            'Рис',
            'Картофель',
            'Морковь',
            'Сыр',
        ];

        $products = DB::table('products')->take(20)->get();

        foreach ($products as $index => $product) {
            $newName = $productNames[$index] ?? null;

            if ($newName) {
                DB::table('products')->where('id', $product->id)->update(['name' => $newName]);
            }
        }
    }
}
