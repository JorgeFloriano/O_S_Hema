<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cause extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'description',
    ];

    protected $table = "causes";
    protected $primaryKey = "id";
}
