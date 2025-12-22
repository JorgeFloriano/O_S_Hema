<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'value',
    ];

    protected $table = "expo_tokens";
    protected $primaryKey = "id";

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Escopo para pegar o token mais recente de um usuário
     */
    public function scopeLatestToken($query)
    {
        return $query->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc');
    }
}
