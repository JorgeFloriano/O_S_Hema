<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\StrongPass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormCreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'required|max:20',
            'surname' => 'nullable|max:20',
            'function' => 'nullable|max:20',
            'username' => ['required', Rule::unique('users'), 'min:10', 'max:100'],
            'permissions' => 'required_if:type_user,1|array',
            'client_id' => ['required_if:type_user,2', Rule::exists('clients', 'id'), 'unique:clis,client_id'],
            'type_user' => 'required|numeric|in:1,2',
            'password' => ['required', 'confirmed', new StrongPass],
        ];
    }

    // Hook para validações personalizadas após as regras básicas
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $type = $this->input('type_user');
            $permissions = $this->input('permissions', []);
            $hasAnyHemaPermission = collect($permissions)->contains(fn($val) => $val > 0);

            if ($type == 2 && $hasAnyHemaPermission) {
                $validator->errors()->add('type_user', 'Um usuário cliente não pode ter acessos de colaboradores Hema.');
            }

            if ($type == 1 && !$hasAnyHemaPermission) {
                $validator->errors()->add('permissions', 'Selecione pelo menos um acesso para o colaborador Hema.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome deve ser preenchido.',
            'name.max' => 'O campo nome deve ter no máximo 20 caracteres.',
            'surname.max' => 'O campo sobrenome deve ter no máximo 20 caracteres.',
            'function.max' => 'O campo função deve ter no.maxcdn 20 caracteres.',
            'username.required' => 'O campo nome de usuário deve ser preenchido.',
            'username.unique' => 'O nome de usúario digitado está em uso, por favor escolha outro.',
            'username.min' => 'O campo nome de usuário deve ter pelo menos 10 caracteres.',
            'username.max' => 'O campo nome de usuário deve ter no.maxcdn 100 caracteres.',
            'password.confirmed' => 'As senhas digitadas devem ser identicas.',
            'password.required' => 'A senha é obrigatória para novos cadastros.',
            '*.boolean' => 'Os campos de perfis de acesso devem ser apenas marcados ou desmarcados.',
            'client_id.required_if' => 'Para usuários do tipo Cliente, a seleção do cliente é obrigatória.',
            'client_id.exists' => 'O cliente selecionado não foi encontrado no sistema.',
            'client_id.unique' => 'Este cliente já possui um usuário vinculado.',
            'type_user.required' => 'O campo tipo de usuário deve ser preenchido.',
            'type_user.in' => 'O campo tipo de usuário deve ser apenas Usuário Hema ou Usuário Cliente.',
        ];
    }
}
