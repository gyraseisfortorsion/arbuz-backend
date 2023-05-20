<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = ['delivery_day', 'delivery_period', 'address', 'phone', 'subscription_period'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('weight', 'quantity');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

