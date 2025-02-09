<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'description',
        'unit'
    ];

    protected $table = "materials";
    protected $primaryKey = "id";

    public function notes() : BelongsToMany
    {
        return $this->belongsToMany(Note::class)->withPivot('quantity')->withPivot('id')->withTimestamps()->withTrashed()->orderBy('pivot_id');
    }
}
