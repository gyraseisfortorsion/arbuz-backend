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

        // Проверяем наличие выбранных продуктов
        $availableProducts = Product::whereIn('name', $products)->pluck('name');
        $unavailableProducts = array_diff($products, $availableProducts->toArray());

        if (!empty($unavailableProducts)) {
            return response()->json([
                'message' => 'Some products are unavailable: ' . implode(', ', $unavailableProducts)
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
        $subscription->products()->attach(Product::whereIn('name', $products)->pluck('id'));

        return response()->json([
            'message' => 'Subscription created successfully'
        ], 201);
    }

    public function updateSubscription(Request $request, $subscriptionId)
{
    $this->validate($request, [
        'products' => 'array',
    ]);

    $products = $request->input('products');

    $subscription = Subscription::find($subscriptionId);

    if (!$subscription) {
        return response()->json([
            'message' => 'Subscription not found'
        ], 404);
    }

    if ($request->has('products')) {
        // Проверяем наличие выбранных продуктов
        $availableProducts = Product::whereIn('name', $products)->pluck('name');
        $unavailableProducts = array_diff($products, $availableProducts->toArray());

        if (!empty($unavailableProducts)) {
            return response()->json([
                'message' => 'Some products are unavailable: ' . implode(', ', $unavailableProducts)
            ], 400);
        }

        // Обновляем выбранные продукты для подписки
        $subscription->products()->sync(Product::whereIn('name', $products)->pluck('id'));
    }

    return response()->json([
        'message' => 'Subscription updated successfully',
        'subscription' => $subscription
    ], 200);
}

}
