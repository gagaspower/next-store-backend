<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    use HasFactory;

    protected $table = 'city';

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
}
