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

    /**
     * Retorna a extensão do arquivo (ex: pdf, jpg, png)
     */
    public function type()
    {
        if (!$this->path) return null;

        // Pega a extensão após o último ponto e converte para minúsculo
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    /**
     * Atalho booleano para verificar se é imagem
     */
    public function isImage()
    {
        return in_array($this->type(), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Atalho booleano para verificar se é PDF
     */
    public function isPdf()
    {
        return $this->type() === 'pdf';
    }

    public function download()
    {
        $file = File::find($this->id);
        return response()->download(public_path('storage/' . $file->path));
    }
}
