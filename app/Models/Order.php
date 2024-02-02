<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    public function orders_detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function expedisi()
    {
        return $this->hasOne(OrderExpedition::class, 'order_id', 'id');
    }

    public function payment_bank()
    {
        return $this->hasOne(OrderPayment::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
