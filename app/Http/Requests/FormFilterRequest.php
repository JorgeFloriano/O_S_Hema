<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_start' => 'nullable|date_format:Y-m-d',
            'date_end' => 'required|date_format:Y-m-d',
            'finished' => 'required|numeric|min:0|max:2',
            'client' => ['required','numeric',Rule::in(session('client_ids'))],
        ];
    }

    public function messages(): array
    {
        return [
            'date_end.required' => 'Selecione uma data final',
            'date_start.date_format' => 'Data inicial inválida',
            'date_end.date_format' => 'Data final inválida',
            'client.required' => 'Selecione um cliente',
            'finished.required' => 'Selecione um status de Ordem',
            'client.numeric' => 'Selecione um cliente válido',
            'client.in' => 'Selecione um cliente cadastrado',
        ];
    }
}
