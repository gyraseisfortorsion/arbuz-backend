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

        foreach ($products as $product) {
            $weight = $product['weight'] ?? null;
            $quantity = $product['quantity'] ?? null;

            $existingProduct = Product::where('name', $product['name'])->first();

            if (!$existingProduct || ($weight === 0 || $quantity === 0 || $weight === null || $quantity === null)) {
                $unavailableProducts[] = $product['name'];
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
        foreach ($products as $product) {
            $subscription->products()->attach($existingProduct->id, [
                'weight' => $product['weight'],
                'quantity' => $product['quantity']
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

            foreach ($products as $product) {
                $weight = $product['weight'] ?? null;
                $quantity = $product['quantity'] ?? null;

                $existingProduct = Product::where('name', $product['name'])->first();

                if (!$existingProduct || ($weight === 0 || $quantity === 0 || $weight === null || $quantity === null)) {
                    $unavailableProducts[] = $product['name'];
                }
            }

            if (!empty($unavailableProducts)) {
                return response()->json([
                    'message' => 'Some products are unavailable or do not exist: ' . implode(', ', $unavailableProducts)
                ], 400);
            }

            $subscription->products()->sync([]); // Удаляем все существующие связи

            foreach ($products as $product) {
                $subscription->products()->attach($existingProduct->id, [
                    'weight' => $product['weight'],
                    'quantity' => $product['quantity']
                ]);
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
