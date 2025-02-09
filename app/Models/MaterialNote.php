<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialNote extends Model
{
    use HasFactory;
    protected $table = "material_note";
    protected $fillable = [
        'material_id',
        'note_id',
        'quantity',
    ];
}
