<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cli extends Model
{
    // For Clients users

    use SoftDeletes;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'client_id',
        'user_id',
        'is_admin',
        'can_create_sat',
        'can_see_sat',
    ];

    use HasFactory;
}
