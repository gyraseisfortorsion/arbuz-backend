<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/subscriptions', 'App\Http\Controllers\SubscriptionController@getSubscriptions');
Route::get('/subscriptions/{subscriptionId}/products', 'App\Http\Controllers\SubscriptionController@getProductsSubscription');
Route::post('/subscriptions', 'App\Http\Controllers\SubscriptionController@createSubscription');
Route::put('/subscriptions/{subscriptionId}', 'App\Http\Controllers\SubscriptionController@updateSubscription');


Route::post('/users', 'App\Http\Controllers\UserController@createUser');
Route::get('/users', 'App\Http\Controllers\UserController@getUsers');
Route::get('/users/{id}', 'App\Http\Controllers\UserController@getUser');
Route::put('/users/{id}', 'App\Http\Controllers\UserController@updateUser');
Route::delete('/users/{id}', 'App\Http\Controllers\UserController@deleteUser');

Route::get('/products', 'App\Http\Controllers\ProductController@getProducts');