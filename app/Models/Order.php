<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'finished',
        'equipment',
        'req_date',
        'req_descr',
    ];


    protected $table = "orders";
    protected $primaryKey = "id";
}
