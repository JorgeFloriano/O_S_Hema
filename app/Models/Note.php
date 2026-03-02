<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'equip_mod',
        'equip_id',
        'equip_type',
        'note_type_id',
        'defect_id',
        'cause_id',
        'solution_id',
        'services',
        'date',
        'go_start',
        'go_end',
        'start',
        'end',
        'back_start',
        'back_end',
        'food',
        'km_start',
        'km_end',
        'expense',
        'obs',
    ];

    protected $table = "notes";
    protected $primaryKey = "id";

    protected static function booted()
    {
        static::deleting(function ($note) {
            // Isso garante que se você der um $note->delete(), 
            // a lógica de limpeza de arquivos seja disparada.
            app(\App\Services\FileService::class)->deleteAllFiles($note);
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tecs(): BelongsToMany
    {
        return $this->belongsToMany(Tec::class)->withPivot('signature', 'signature_path', 'id', 'is_primary')->withTimestamps()->withTrashed()->orderBy('pivot_id');
    }

    public function tecSignaturePath($tec)
    {
        // If you need to send signatures back to React Native as base64
        if ($this->tecs[$tec]->pivot->signature_path) {
            return $this->getSignatureAsBase64($this->tecs[$tec]->pivot->signature_path);
        }
    }

    private function getSignatureAsBase64($filePath)
    {
        if (Storage::disk('public')->exists($filePath)) {
            $fileContents = Storage::disk('public')->get($filePath);
            return 'data:image/png;base64,' . base64_encode($fileContents);
        }
        return null;
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(NoteType::class, 'note_type_id')->withTrashed();
    }

    public function defect(): BelongsTo
    {
        return $this->belongsTo(Defect::class)->withTrashed();
    }

    public function cause(): BelongsTo
    {
        return $this->belongsTo(Cause::class)->withTrashed();
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class)->withTrashed();
    }
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)->withPivot('quantity', 'id')->withTimestamps()->withTrashed()->orderBy('pivot_id');
    }

    /**
     * Obtém todos os arquivos da anotação.
     */
    public function files(): MorphMany
    {
        // O segundo parâmetro 'fileable' deve coincidir com o nome usado na migration
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Retorna apenas os arquivos que são imagens (filtros PDFs).
     * Ideal para usar no Blade do Relatório.
     */
    public function images()
    {
        // Usamos a propriedade dinâmica $this->files, que já traz a Collection
        // Verificamos se a coleção está vazia de forma segura
        if ($this->files->isEmpty()) {
            return collect(); // Retorna uma coleção vazia em vez de array para manter consistência
        }

        return $this->files->filter(function ($file) {
            // Usamos Str::endsWith de forma case-insensitive para segurança no Linux
            return !Str::endsWith(strtolower($file->path), '.pdf');
        });
    }

    public function pdfs()
    {
        // Verificamos se a coleção está vazia de forma segura
        if ($this->files->isEmpty()) {
            return collect(); // Retorna uma coleção vazia em vez de array para manter consistência
        }

        return $this->files->filter(function ($file) {
            return Str::endsWith(strtolower($file->path), '.pdf');
        });
    }
}
