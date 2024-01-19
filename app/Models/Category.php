<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category';

    public function products()
    {
        $this->belongsTo(Product::class);
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
            $item->category_slug = Str::slug($item->category_name);
        });

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::updating(function ($item) {
            $item->category_slug = Str::slug($item->category_name);
        });
    }
}
