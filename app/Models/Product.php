<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVarian::class, 'product_id');
    }

    public function variants_stock()
    {
        return $this->hasMany(ProductVarianStock::class, 'product_id');
    }

    public static function boot()
    {
        parent::boot();

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::creating(function ($item) {
            $item->product_slug = Str::slug($item->product_name);
        });

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::updating(function ($item) {
            $item->product_slug = Str::slug($item->product_name);
        });
    }
}
