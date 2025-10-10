<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class NoEqualSequence implements ValidationRule
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
        // Check for sequential numbers (ascending or descending)
        for ($i = 0; $i < strlen($value) - 2; $i++) {
            $current = ord($value[$i]);
            $next = ord($value[$i + 1]);
            $nextNext = ord($value[$i + 2]);

            // Check for repeated numbers (e.g., 111)
            if ($current == $next && $next == $nextNext) {
                $fail('A senha não pode conter sequências óbvias como como 111, ggg, 555 etc');
                return;
            }
        }
    }
}