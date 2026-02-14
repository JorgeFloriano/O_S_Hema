<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class StrongPass implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $errors = [];

        // Check for minimum length
        if (strlen($value) < 10) {
            $errors[] = ' Deve conter pelo menos 10 caracteres.';
        }

        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $value)) {
            $errors[] = ' Deve conter pelo menos uma letra maiúscula.';
        }

        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $value)) {
            $errors[] = ' Deve conter pelo menos uma letra minúscula.';
        }

        // Check for at least one number
        if (!preg_match('/\d/', $value)) {
            $errors[] = ' Deve conter pelo menos um número.';
        }

        // Check for at least one special character
        if (!preg_match('/[\W_]/', $value)) {
            $errors[] = ' Deve conter pelo menos um caractere especial.';
        }

        // Check for repeated numbers (e.g., 111, 222)
        if (preg_match('/(.)\1{2}/', $value)) {
            $errors[] = ' Não pode conter caracteres iguais em sequência (ex: 111, aaa, DDD, 222).';
        }

        // If there are errors, fail with all messages
        if (!empty($errors)) {
            $fail('Para maior segurança, a senha deve seguir os seguintes requisitos:');
            foreach ($errors as $error) {
                $fail($error);
            }
        }
    }
}