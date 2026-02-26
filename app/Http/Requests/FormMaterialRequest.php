<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormMaterialRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['numeric','nullable','min:0', Rule::unique(session('table'))],
            'description' => 'required|max:25',
            'code' => 'nullable|max:8',
            'unit' => 'required|max:5'
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'O número do código digitado está em uso, por favor escolha outro.',
            'id.numeric' => 'O código deve ser numérico.',
            'id.min' => 'O número do código deve ser maior que zero.',
            'description.required' => 'O campo descrição deve ser preenchido.',
            'description.max' => 'O campo descrição deve ter no máximo 25 caracteres.',
            'code.max' => 'O campo código deve ter no máximo 8 caracteres.',
            'unit.required' => 'O campo unidade de medida deve ser preenchido.',
        ];
    }
}
