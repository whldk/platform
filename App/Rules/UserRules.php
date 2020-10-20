<?php namespace App\Rules;

use Inhere\Validate\FieldValidation;

class UserRules extends FieldValidation
{
    public function rules(): array
    {
        return [
            ['username', 'required|string:3,12'],
            ['password', 'required|string:3,15']
        ];
    }

    public function scenarios(): array
    {
        return [
            'create' => ['username', 'password'],
            'update' => ['username', 'password', 'resetPwd']
        ];
    }

    public function translates(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名必填',
            'username.string' => '用户名的长度范围在3~12长度之间',
            'password.required' => '密码必填',
            'password.string' => '密码的长度范围在3~15长度之间',
        ];
    }
}