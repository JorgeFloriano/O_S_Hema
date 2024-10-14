<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormLoginRequest extends FormRequest
{
   
    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required|min:5'
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Digite seu nome de usuário',
            'password.required' => 'Digite sua senha',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres'
        ];
    }
}
