<?php namespace App\HttpController\User;

use App\HttpController\Common\Controller;
use App\Rules\UserRules;

class User extends Controller
{

    protected $access = [
        '*' => ['*']
    ];

    protected $filter = [];

    public function category()
    {

    }

    public function list()
    {

    }

    public function create(UserRules $rules)
    {
        var_dump($this->params);
        $valid = $rules::check($this->params);
        if ($valid->isFail()) {
            var_dump($valid->getErrors());
            var_dump($valid->firstError());
        }
        // 验证成功 ...
        $safeData = $valid->getSafeData(); // 验证通过的安全数据
        // $postData = $v->all(); // 原始数据
        var_dump($safeData);
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function view()
    {

    }
}