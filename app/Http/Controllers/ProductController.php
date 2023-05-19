<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProducts()
    {
        $users = Product::all();

        return response()->json([
            'products' => $users
        ], 200);
    }
}