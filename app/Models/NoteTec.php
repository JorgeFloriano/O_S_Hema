<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteTec extends Model
{
    use HasFactory;
    protected $table = "note_tec";
    protected $fillable = [
        'note_id',
        'tec_id',
        'signature',
    ];
}
