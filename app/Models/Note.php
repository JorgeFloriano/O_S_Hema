<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'equip_mod',
        'equip_id',
        'equip_type',
        'situation',
        'cause',
        'services',
        'date',
        'go_start',
        'go_end',
        'start',
        'end',
        'back_start',
        'back_end',
        'first_tec',
        'sign_t_1',
        'second_tec',
        'sign_t_2',
    ];

    protected $table = "notes";
    protected $primaryKey = "id";

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function first_tec(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function second_tec(): HasOne
    {
        return $this->hasOne(User::class);
    }

}
