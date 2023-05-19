<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'weight'];
    
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }
}