<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'description',
    ];

    protected $table = "order_types";
    protected $primaryKey = "id";  
}
