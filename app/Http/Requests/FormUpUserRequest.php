<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormUpUserRequest extends FormRequest
{

    // Doesn't work yet, is validated in UserController
    public function rules($min, $id): array
    {
        return [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'function' => 'required|max:20',
            'username' => [Rule::unique('users')->ignore($id), 'min:10', 'max:100'],
            'password' => [$min, 'confirmed']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome deve ser preenchido.',
            'name.max' => 'O campo nome tem que ter no màximo 20 caracteres.',
            'surname.max' => 'O campo sobrenome tem que ter no màximo 20 caracteres.',
            'function.required' => 'O campo função deve ser preenchido.',
            'function.max' => 'O campo função tem que ter no màximo 20 caracteres.',
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'username.min' => 'O nome de usúario tem que ter pelo menos 10 caracteres.',
            'username.max' => 'O nome de usúario tem que ter no màximo 100 caracteres.',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'password.confirmed' => 'As senhas digitadas deveriam ser identicas.',
        ];
    }
}
