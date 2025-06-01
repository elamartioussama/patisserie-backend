<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status', // en_attente, en_cours, prête, livrée
        'total_price',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function complaint()
    {
        return $this->hasOne(Complaint::class);
    }
        public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'delivery_person_id');
    }
public function products()
{
    return $this->belongsToMany(Product::class, 'order_items') // Spécifie ici la bonne table pivot
                ->withPivot('quantity') // Pour accéder à $product->pivot->quantity
                ->withTimestamps();
}


}
