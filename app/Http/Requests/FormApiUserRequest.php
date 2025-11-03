<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FormApiUserRequest extends FormRequest
{

    // Doesn't work yet, is validated in UserController
    public function rules(): array
    {
        $userId = $this->route('user') ?? 0;
        return [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'email' => 'required|email|unique:users,email,'.$userId,
            'function' => 'required|max:20',
            'username' => ['required', Rule::unique('users')->ignore($userId), 'min:10', 'max:100'],
            'password' => 'min:5|max:20|confirmed',
        ];
    }

    protected function prepareForValidation()
    {
        // Trim all string inputs automatically
        $this->merge([
            'name' => trim($this->name),
            'surname' => $this->surname ? trim($this->surname) : null,
            'email' => trim($this->email),
            'username' => trim($this->username),
            'function' => $this->function ? trim($this->function) : null,
            'password' => $this->password ? trim($this->password) : null,
            'password_confirmation' => $this->password_confirmation ? trim($this->password_confirmation) : null,
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome deve ser preenchido.',
            'name.max' => 'O campo nome tem que ter no màximo 20 caracteres.',
            'surname.max' => 'O campo sobrenome tem que ter no màximo 20 caracteres.',
            'email.required' => 'O campo email deve ser preenchido.',
            'email.unique' => 'Este email já está em uso.',
            'email.email' => 'O email digitado é inválido.',
            'function.required' => 'O campo função deve ser preenchido.',
            'function.max' => 'O campo função tem que ter no màximo 20 caracteres.',
            'username.required' => 'O campo nome de usúario deve ser preenchido.',
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'username.min' => 'O nome de usúario deve ter pelo menos 10 caracteres.',
            'username.max' => 'O nome de usúario deve ter no màximo 100 caracteres.',
            'password.min' => 'Digite uma senha com pelo menos 5 caracteres',
            'password.max' => 'Digite uma senha com no màximo 20 caracteres',
            'password.confirmed' => 'As senhas digitadas devem ser identicas.',
        ];
    }
}
