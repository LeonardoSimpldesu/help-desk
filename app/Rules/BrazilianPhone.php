<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class BrazilianPhone implements ValidationRule
{
    /**
     * Alpine mask expression shared by every phone input (landline or mobile, with area code).
     */
    public const string MASK = "\$input.replace(/\\D/g, '').length > 10 ? '(99) 99999-9999' : '(99) 9999-9999'";

    /**
     * Digits only: area code (11–99, no zeros) followed by a 9-digit mobile or an 8-digit landline.
     */
    public const string PATTERN = '/^[1-9]{2}(9\d{8}|[2-8]\d{7})$/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match(self::PATTERN, $value)) {
            $fail('Informe um telefone válido com DDD (fixo ou celular).');
        }
    }
}
