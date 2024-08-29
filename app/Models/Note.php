<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
