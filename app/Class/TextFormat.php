<?php

namespace App\Class;

class TextFormat {

    // Determine the quantity of zeros to create a number with four digits
    public function leftZeros ($number) {
        $zero = '';
        if ($number < 10) {
            $zero = '000';
        } elseif ($number < 100) {
            $zero = '00';
        } elseif ($number < 1000) {
            $zero = '0';
        }

        return $zero;
    }

    // Format a string so that there is always a space after a punctuation mark (like ,.;:?!)
    function spaceAfterPunctuation($input) {
        return preg_replace('/([,.;:?!])(?!\s)/', '$1 ', $input);
    }
}
