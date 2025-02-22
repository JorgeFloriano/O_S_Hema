<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormCodeRequest extends FormRequest
{
   
    public function rules(): array
    {
        return [
            'id' => ['numeric','nullable','min:0', Rule::unique(session('table'))],
            'description' => 'required|max:25',
        ];
    }

    public function messages(): array
    {
        return [
            'id.unique' => 'O número do código digitado está em uso, por favor escolha outro.',
            'id.min' => 'O número do código deve ser maior que zero.',
            'id.numeric' => 'O campo código deve ser numérico.',
            'description.required' => 'O campo descrição deve ser preenchido.',
        ];
    }
}
