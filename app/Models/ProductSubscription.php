<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSubscription extends Model
{
    use HasFactory;
    protected $table = 'product_subscription';

    protected $fillable = [
        'product_id',
        'subscription_id',
        'weight',
        'quantity',
    ];

    // Define the relationships with the Product and Subscription models
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
