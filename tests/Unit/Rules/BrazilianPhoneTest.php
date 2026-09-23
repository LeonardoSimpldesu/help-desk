<?php

use App\Rules\BrazilianPhone;

/**
 * Run the rule against a value and return the failure messages it produced.
 *
 * @return list<string>
 */
function brazilianPhoneFailures(mixed $value): array
{
    $messages = [];

    (new BrazilianPhone)->validate('phone', $value, function (string $message) use (&$messages): void {
        $messages[] = $message;
    });

    return $messages;
}

it('accepts a phone with area code', function (string $phone) {
    expect(brazilianPhoneFailures($phone))->toBe([]);
})->with([
    'mobile' => '11987654321',
    'landline' => '1133334444',
    'highest area code' => '99912345678',
    'landline starting with 2' => '2122223333',
    'landline starting with 8' => '2188887777',
]);

it('rejects an invalid phone with the user-facing message', function (mixed $phone) {
    expect(brazilianPhoneFailures($phone))->toBe(['Informe um telefone válido com DDD (fixo ou celular).']);
})->with([
    'area code starting with 0' => '01987654321',
    'area code ending with 0' => '10987654321',
    'mobile without the leading 9' => '11887654321',
    'landline starting with 9' => '1193334444',
    'landline starting with 1' => '1113334444',
    'landline starting with 0' => '1103334444',
    'too short' => '113333444',
    'too long' => '119876543210',
    'without area code' => '987654321',
    'formatted with mask characters' => '(11) 98765-4321',
    'letters' => 'abcdefghijk',
    'empty string' => '',
    'integer instead of string' => 11987654321,
]);
