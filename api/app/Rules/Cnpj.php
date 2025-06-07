<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cnpj implements Rule
{
    public function passes($attribute, $value)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $value);

        if (strlen($cnpj) != 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $peso1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $peso2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];

        for ($i = 0, $sum1 = 0; $i < 12; $i++) {
            $sum1 += $cnpj[$i] * $peso1[$i];
        }

        $digit1 = $sum1 % 11;
        $digit1 = $digit1 < 2 ? 0 : 11 - $digit1;

        if ($cnpj[12] != $digit1) {
            return false;
        }

        for ($i = 0, $sum2 = 0; $i < 13; $i++) {
            $sum2 += $cnpj[$i] * $peso2[$i];
        }

        $digit2 = $sum2 % 11;
        $digit2 = $digit2 < 2 ? 0 : 11 - $digit2;

        return $cnpj[13] == $digit2;
    }

    public function message()
    {
        return 'O campo :attribute não é um CNPJ válido.';
    }
}
