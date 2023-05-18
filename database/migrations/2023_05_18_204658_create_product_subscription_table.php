<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSubscriptionTable extends Migration
{
    public function up()
    {
        Schema::create('product_subscription', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('subscription_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_subscription');
    }
}
