<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    protected $fillable = ['path', 'original_name', 'fileable_id', 'fileable_type'];

    /**
     * Obtém o modelo pai (Note, Sat, etc.)
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
