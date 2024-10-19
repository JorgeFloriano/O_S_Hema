<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'description',
    ];

    protected $table = "solutions";
    protected $primaryKey = "id";
}
