<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarian extends Model
{
    use HasFactory;

    protected $table = 'product_varian';

    protected $hidden = ['id', 'product_id', 'created_at', 'updated_at'];
}
