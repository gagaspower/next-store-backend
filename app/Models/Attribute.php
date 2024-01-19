<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    protected $table = 'attributes';

    public function attribute_values()
    {
        return $this->hasMany(AttributeValues::class, 'attribute_id');
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
            $item->attribute_slug = Str::slug($item->attribute_name);
        });

        /**
         * Write code on Method
         *
         * @return response()
         */
        static::updating(function ($item) {
            $item->attribute_slug = Str::slug($item->attribute_name);
        });
    }
}
