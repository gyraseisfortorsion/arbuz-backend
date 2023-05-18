<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Subscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'delivery_day',
        'delivery_period',
        'address',
        'phone',
        'subscription_duration',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
