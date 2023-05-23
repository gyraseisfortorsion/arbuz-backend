<?php

namespace App\Http\Controllers;

use App\Models\ProductSubscription;
use Illuminate\Http\Request;

class ProductSubscriptionController extends Controller
{
    public function getProductSubscriptions()
    {
        $productSubscriptions = ProductSubscription::all();

        return response()->json(
            $productSubscriptions
        , 200);
    }
}