<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sup extends Model
{
    use SoftDeletes;
    use HasFactory;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reopenOrder($order_id)
    {
        $order = Order::find($order_id);
        $order_reopened = $order->finished = 0;
        $order_reopened = $order->save();
        return $order_reopened;
    }

    protected $fillable = [
        'user_id',
    ];
}
