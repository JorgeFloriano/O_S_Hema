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
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'password.confirmed' => 'As senhas digitadas deveriam ser identicas.',
        ];
    }
}
