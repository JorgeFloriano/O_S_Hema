<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormApiUserRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // Doesn't work yet, is validated in UserController
    public function rules(): array
    {
        $userId = $this->route('user');

        $rules = [
            'name' => 'required|max:20',
            'surname' => 'max:20',
            'email' => 'required|email|unique:users,email,' . $userId,
            'function' => 'nullable|required|max:20',
            'username' => ['required', Rule::unique('users')->ignore($userId), 'min:10', 'max:100'],
            'can_create_sat' => 'nullable|boolean',
            'can_see_sat' => 'nullable|boolean',
        ];

        if ($this->filled('password')) {
            $rules['password'] = 'required|min:5|confirmed';
            $rules['password_confirmation'] = 'required';
        } else {
            if (!$userId) {
                $rules['password'] = 'required|min:5|confirmed';
                $rules['password_confirmation'] = 'required';
            }
        }

        return $rules;
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
