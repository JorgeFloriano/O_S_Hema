<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\StrongPass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormUpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'required|max:20',
            'surname' => 'nullable|max:20',
            'function' => 'nullable|max:20',
            'username' => ['required', Rule::unique('users')->ignore($userId), 'min:10', 'max:100'],
            'permissions' => 'nullable|array',
            'main_adm_tec_access' => 'nullable|boolean',
            'password' => [$this->filled('password') ? 'required' : 'nullable', 'confirmed', new StrongPass],
        ];
    }

    // Hook para validações personalizadas após as regras básicas
    public function withValidator($validator)
    {
        $userId = $this->route('user');
        $user = User::find($userId);

        $validator->after(function ($validator) use ($user) {
            $permissions = $this->input('permissions', []);

            // Verifica se existe alguma permissão marcada (valor > 0)
            $hasAnyHemaPermission = collect($permissions)->contains(fn($val) => $val > 0);

            if ($user->isCli() && ($hasAnyHemaPermission || $this->input('main_adm_tec_access'))) {
                $validator->errors()->add('user_client', 'Um usuário cliente não pode ter acessos de colaboradores Hema.');
            }

            if (!$user->isCli() && !$hasAnyHemaPermission && auth()->id() != $user->id) {
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
            '*.boolean' => 'Os campos de perfis de acesso devem ser apenas marcados ou desmarcados.',
        ];
    }
}
