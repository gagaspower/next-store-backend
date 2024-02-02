<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'orders_detail';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_variants()
    {
        return $this->belongsTo(ProductVarianStock::class, 'product_variant_id', 'id');
    }
}
