<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValues extends Model
{
    use HasFactory;

    protected $table = 'attributes_value';

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
