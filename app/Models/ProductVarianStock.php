<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarianStock extends Model
{
    use HasFactory;

    protected $table = 'product_varian_stock';

    protected $hidden = ['id', 'product_id', 'created_at', 'updated_at'];
}
