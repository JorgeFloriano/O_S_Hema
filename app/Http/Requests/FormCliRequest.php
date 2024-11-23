<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormCliRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:20',
            'cnpj_cpf' => 'max:20',
            'cep' => 'max:20',
            'unit' => 'max:20',
            'address' => 'max:80|required',
            'email' => 'email|max:50',
            'phone' => 'max:20|required',
            'contact' => 'max:20|required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Digite um e-mail válido',
            'cnpj_cpf.max' => 'Digite um CNPJ ou CPF válido',
        ];
    }
}
