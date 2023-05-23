<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = ['delivery_day', 'delivery_period', 'address', 'phone', 'subscription_period', 'price_limit'];

    public function products()
    {
        //return $this->belongsTo(Product::class)->withPivot('weight', 'quantity');
        return $this->hasMany(ProductSubscription::class, 'subscription_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

