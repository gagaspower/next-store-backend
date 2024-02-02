<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartTemp extends Model
{
    use HasFactory;

    protected $table    = 'cart_temp';
    protected $fillable = ['cart_date', 'product_id', 'product_qty', 'product_variant_stock_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variants_stock()
    {
        return $this->belongsTo(ProductVarianStock::class, 'product_variant_stock_id');
    }
}
