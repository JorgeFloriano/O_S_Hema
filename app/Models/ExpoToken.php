<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'value', 
    ];

    protected $table = "expo_tokens";
    protected $primaryKey = "id";
}
