<?php namespace App\Rules;

use Inhere\Validate\FieldValidation;

class UserRules extends FieldValidation
{
    public function rules(): array
    {
        return [
//            ['id', 'required', 'on' => 'view, update, delete'],
//            ['username', 'required|string:3,12', 'on' => 'create, update'],
//            ['password', 'required|string:3,15', 'on' => 'create, update']
            ['id', 'required'],
            ['username', 'required|string:3,12'],
            ['password', 'required|string:3,15']
        ];
    }

    public function scenarios(): array
    {
        return [
            'create' => ['username', 'password'],
            'update' => ['id', 'username', 'password'],
            'view' => ['id'],
            'delete' => ['id']
        ];
    }

    public function translates(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [
            'id.required' => '用户id必填',
            'username.required' => '用户名必填',
            'username.string' => '用户名的长度范围在3~12长度之间',
            'password.required' => '密码必填',
            'password.string' => '密码的长度范围在3~15长度之间',
        ];
    }
}