<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subscription;
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
            'delivery_day' => 'required|integer|min:1|max:7',
            'delivery_period' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'subscription_duration' => 'required|string',
        ]);

        $products = $request->input('products');
        $deliveryDay = $request->input('delivery_day');
        $deliveryPeriod = $request->input('delivery_period');
        $address = $request->input('address');
        $phone = $request->input('phone');
        $subscriptionDuration = $request->input('subscription_duration');

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
        $subscription->save();

        // Сохраняем выбранные продукты для подписки
        foreach ($products as $productName) {
            $existingProduct = Product::where('name', $productName)->first();

            $subscription->products()->attach($existingProduct->id, [
                'weight' => $existingProduct->weight,
                'quantity' => $existingProduct->quantity
            ]);
        }

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

        $subscription->save();

        return response()->json([
            'message' => 'Subscription updated successfully'
        ], 200);
    }


}
