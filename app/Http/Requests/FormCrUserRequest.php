<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormCrUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'function' => 'required|max:20',
            'username' => 'min:10|max:100|unique:users',
            'password' => 'min:5|max:100|unique:users|confirmed',
            'tec' => 'required_without_all:adm,sup'
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'password.unique' => 'A senha digitada está em uso, por favor escolha outra.',
            'password.confirmed' => 'As senhas digitadas deveriam ser identicas.',
            'tec.required_without_all' => 'Selecione pelo menos um acesso para o usuário.',
        ];
    }
}
