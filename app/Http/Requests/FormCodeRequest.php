<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormCodeRequest extends FormRequest
{
   
    public function rules(): array
    {
        return [
            'id' => ['min:0', Rule::unique(session('table'))],
            'description' => 'required|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'O Código digitado está em uso, por favor escolha outro.',
            'id.min' => 'O campo Código deve ser maior que zero.',
            'description.required' => 'O campo descrição deve ser preenchido.',
        ];
    }
}
