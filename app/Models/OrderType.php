<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'description',
    ];

    protected $table = "order_types";
    protected $primaryKey = "id";  
}
