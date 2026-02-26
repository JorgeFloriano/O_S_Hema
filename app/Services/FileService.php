<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Faz o upload de múltiplos arquivos e os vincula ao modelo.
     */
    public function storeMultipleFiles($model, array $uploadedFiles, string $folder = 'attachments')
    {
        $savedFiles = [];

        foreach ($uploadedFiles as $file) {
            if ($file instanceof UploadedFile) {
                // Validação manual dentro do loop ou via Validator
                $this->validateFile($file);

                $path = $file->store($folder, 'public');

                $savedFiles[] = $model->files()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return $savedFiles;
    }

    /**
     * Valida individualmente cada arquivo (Ex: Máximo 5MB, PDF/JPG/PNG)
     */
    protected function validateFile(UploadedFile $file)
    {
        $validator = Validator::make(['file' => $file], [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Deleta todos os arquivos vinculados a um modelo específico.
     */
    public function deleteAllFiles($model)
    {
        // 1. Buscamos todos os arquivos vinculados (Note, Sat, etc.)
        $files = $model->files;

        foreach ($files as $file) {
            // 2. Removemos o arquivo físico do storage
            // O caminho está salvo na coluna 'path' (ex: notes/arquivo.jpg)
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            // 3. Removemos o registro do banco de dados
            $file->delete();
        }

        return true;
    }

    /**
     * Deleta um único arquivo pelo ID.
     */
    public function deleteSingleFile(int $fileId)
    {
        $file = \App\Models\File::findOrFail($fileId);

        if (Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        return $file->delete();
    }
}
