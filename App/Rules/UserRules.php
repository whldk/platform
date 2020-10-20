<?php namespace App\Rules;

use Inhere\Validate\FieldValidation;

class UserRules extends FieldValidation
{
    public function rules(): array
    {
        return [
            ['username', 'required|string:3,12'],
            ['password', 'required|string:3,12']
        ];
    }

    public function scenarios(): array
    {
        return [];
    }

    public function translates(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}