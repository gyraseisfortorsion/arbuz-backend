<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subscription;
use App\Models\ProductSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function getSubscriptions()
    {
        $subscriptions = Subscription::with('products')->get();

        return response()->json([
            'subscriptions' => $subscriptions
        ], 200);
    }

    public function getProductsSubscription($subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);
        $products = $subscription->products()->get();

        return response()->json($products);
    }

    public function createSubscription(Request $request)
    {
        $this->validate($request, [
            'products' => 'required|array',
            'products.*' => 'string', // Validate each product name as a string
            'weights' => 'array',
            'weights.*' => 'numeric',
            'quantities' => 'array',
            'quantities.*' => 'integer',
            'delivery_day' => 'required|integer|min:1|max:7',
            'delivery_period' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'subscription_duration' => 'required|string',
            'price_limit' => 'required|integer|min:0',
        ]);

        $products = $request->input('products');
        $weights = $request->input('weights');
        $quantities = $request->input('quantities');
        $deliveryDay = $request->input('delivery_day');
        $deliveryPeriod = $request->input('delivery_period');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $subscriptionDuration = $request->input('subscription_duration');
        $priceLimit = $request->input('price_limit');

        if (empty($weights) && empty($quantities)){
            return response()->json([
                'message' => 'Either weights or quantities must be provided' 
            ], 400);
        }
        // if (sizeof($products) !== sizeof($weights) && sizeof($products) !== sizeof($quantities)){
        //     return response()->json([
        //         'message' => 'The number of products must match the number of weights or quantities' 
        //     ], 400);
        // }
        // Проверяем наличие выбранных продуктов и их доступность
        $unavailableProducts = [];

        foreach ($products as $productName) {
            $existingProduct = Product::where('name', $productName)->first();

            if (!$existingProduct || $existingProduct->weight === 0 || $existingProduct->quantity === 0) {
                $unavailableProducts[] = $productName;
            }
        }

        if (!empty($unavailableProducts)) {
            return response()->json([
                'message' => 'Some products are unavailable or do not exist: ' . implode(', ', $unavailableProducts)
            ], 400);
        }

        // Создаем подписку
        $subscription = new Subscription();
        $subscription->delivery_day = $deliveryDay;
        $subscription->delivery_period = $deliveryPeriod;
        $subscription->address = $address;
        $subscription->phone = $phone;
        $subscription->subscription_duration = $subscriptionDuration;
        $subscription->price_limit = $priceLimit;
        $subscription->save();

        // Считаем общую стоимость продуктов и проверяем ее на соответствие лимиту
        $totalPrice = 0;

        foreach ($products as $index => $productName) {
            $existingProduct = Product::where('name', $productName)->first();

            $productSubscription = new ProductSubscription();
            $productSubscription->product_id = $existingProduct->id;
            $productSubscription->subscription_id = $subscription->id;

            if (!empty($weights[$index])) {
                $productSubscription->weight = $weights[$index];
                $productSubscription->quantity = 0;
                $totalPrice += $existingProduct->price_per_kilo * $weights[$index];

                if ($existingProduct->weight - $weights[$index]<0){
                    return response()->json([
                        'message' => 'Not enough weight/quantity of product: ' . $productName . ' in stock'
                    ], 400);
                }
                //отнимаем вес продукта в подписке из общего веса продукта
                $existingProduct->weight -= $weights[$index];
            } elseif (!empty($quantities[$index])) {
                $productSubscription->weight = 0;
                $productSubscription->quantity = $quantities[$index];
                $totalPrice += $existingProduct->price_per_item * $quantities[$index];

                if ($existingProduct->quantity - $quantities[$index]<0){
                    return response()->json([
                        'message' => 'Not enough weight/quantity of product: ' . $productName . ' in stock'
                    ], 400);
                }
                //отнимаем количество продукты в подписке из общего количества продукта
                $existingProduct->quantity -= $quantities[$index];
                
            }

            $productSubscription->save();
            $existingProduct->save();
        }
        // Проверка на соответствие лимиту
        if ($totalPrice > $priceLimit) {
            return response()->json([
                'message' => 'Total price exceeds the price limit'.implode(', ', $totalPrice, $priceLimit)
            ], 400);
        }

        // Сохраняем выбранные продукты для подписки
        // foreach ($products as $productName) {
        //     $existingProduct = Product::where('name', $productName)->first();

        //     $subscription->products()->attach($existingProduct->id, [
        //         'weight' => $existingProduct->weight,
        //         'quantity' => $existingProduct->quantity,
        //         'price' => $existingProduct->price_per_kilo * $existingProduct->weight + $existingProduct->price_per_item * $existingProduct->quantity
        //     ]);
        // }

        return response()->json([
            'message' => 'Subscription created successfully'
        ], 201);
    }

    
    public function updateSubscription(Request $request, $subscriptionId)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $products = $request->input('products');
        $deliveryDay = $request->input('delivery_day');
        $deliveryPeriod = $request->input('delivery_period');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $subscriptionDuration = $request->input('subscription_duration');
        $priceLimit = $request->input('price_limit');

        // Обновляем выбранные продукты для подписки, если они предоставлены
        if (!empty($products)) {
            $unavailableProducts = [];
    
            foreach ($products as $productName) {
                $existingProduct = Product::where('name', $productName)->first();
    
                if (!$existingProduct) {
                    $unavailableProducts[] = $productName;
                } else {
                    $subscription->products()->syncWithoutDetaching([$existingProduct->id => [
                        'weight' => $existingProduct->weight,
                        'quantity' => $existingProduct->quantity
                    ]]);
                }
            }
    
            if (!empty($unavailableProducts)) {
                return response()->json([
                    'message' => 'Some products are unavailable or do not exist: ' . implode(', ', $unavailableProducts)
                ], 400);
            }
        }

        // Обновляем остальные поля подписки, если они предоставлены
        if (!empty($deliveryDay)) {
            $subscription->delivery_day = $deliveryDay;
        }

        if (!empty($deliveryPeriod)) {
            $subscription->delivery_period = $deliveryPeriod;
        }

        if (!empty($address)) {
            $subscription->address = $address;
        }

        if (!empty($phone)) {
            $subscription->phone = $phone;
        }

        if (!empty($subscriptionDuration)) {
            $subscription->subscription_duration = $subscriptionDuration;
        }
        if (!empty($priceLimit)) {
            $subscription->price_limit = $priceLimit;
        }

        // Считаем общую стоимость продуктов и проверяем ее на соответствие лимиту
        foreach ($products as $index => $productName) {
            $existingProduct = Product::where('name', $productName)->first();

            $productSubscription = new ProductSubscription();
            $productSubscription->product_id = $existingProduct->id;
            $productSubscription->subscription_id = $subscription->id;

            if (!empty($weights[$index])) {
                $productSubscription->weight = $weights[$index];
                $productSubscription->quantity = 0;
                $totalPrice += $existingProduct->price_per_kilo * $weights[$index];

                if ($existingProduct->weight - $weights[$index]<0){
                    return response()->json([
                        'message' => 'Not enough weight/quantity of product: ' . $productName . ' in stock'
                    ], 400);
                }
                //отнимаем вес продукта в подписке из общего веса продукта
                $existingProduct->weight -= $weights[$index];
            } elseif (!empty($quantities[$index])) {
                $productSubscription->weight = 0;
                $productSubscription->quantity = $quantities[$index];
                $totalPrice += $existingProduct->price_per_item * $quantities[$index];

                if ($existingProduct->quantity - $quantities[$index]<0){
                    return response()->json([
                        'message' => 'Not enough weight/quantity of product: ' . $productName . ' in stock'
                    ], 400);
                }
                //отнимаем количество продукты в подписке из общего количества продукта
                $existingProduct->quantity -= $quantities[$index];
                
            }

            $productSubscription->save();
            $existingProduct->save();
        }

        // Проверка на соответствие лимиту
        if ($totalPrice > $subscription->price_limit) {
            return response()->json([
                'message' => 'Total price exceeds the price limit'.implode(', ', $totalPrice, $priceLimit)
            ], 400);
        }

        $subscription->save();

        return response()->json([
            'message' => 'Subscription updated successfully'
        ], 200);
    }


}
