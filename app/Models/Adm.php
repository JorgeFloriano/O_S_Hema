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
    use HasFactory;
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function fileCountPages(string $path): int
    {
        $pdf = file_get_contents($path);
        return preg_match_all("/\/Page\W/", $pdf, $dummy);
    }

    protected $fillable = [
        'user_id',
        'main',
        'cli',
    ];
    
    public function isMain(): bool
    {
        return $this->main;
    }
}
