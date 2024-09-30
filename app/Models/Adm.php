<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

class Adm extends Model
{
    use SoftDeletes;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'main',
    ];

    use HasFactory;
}
