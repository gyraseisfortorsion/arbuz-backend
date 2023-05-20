<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'weight', 'quantity'];

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)->withPivot('weight', 'quantity');
    }
}

