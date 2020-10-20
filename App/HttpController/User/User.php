<?php namespace App\HttpController\User;

use App\HttpController\Common\BaseController;

use App\Rules\UserRules;

class User extends BaseController
{

    protected $access = [];

    protected $filter = [];

    public function category()
    {
        return $this->writeJson(200,'category', 'success');
    }

    public function list()
    {

    }

    public function addOne()
    {
        var_dump($this->params);
        $valid = UserRules::check($this->params);
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