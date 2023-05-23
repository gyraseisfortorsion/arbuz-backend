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
        'name',
        'weight',
        'quantity',
        'price'
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

    // Расчет цены за индивидуальное колво/вес продукта
    public function calculatePrice()
    {
        $product = $this->product;

        if ($product->price_per_kilo && $this->weight) {
            $this->price = $product->price_per_kilo * $this->weight;
        } elseif ($product->price_per_item && $this->quantity) {
            $this->price = $product->price_per_item * $this->quantity;
        }
        $this->name=$product->name;
    }

    // Override the save method to automatically calculate the price before saving
    public function save(array $options = [])
    {
        $this->calculatePrice();
        
        return parent::save($options);
    }
}
