<?php
namespace App\HttpController\User;

use App\HttpController\BaseController;
use App\HttpModel\User\UserModel;


class User extends BaseController
{

    public $access = [
        'userInfo' => ['*'],
        'create' => ['admin']
    ];

    public function requestTotal()
    {
        $this->response()->write('请求数+1'.PHP_EOL);
        // 还可以return，但不要两个方法互相调用，会死循环
    }

    public function userInfo()
    {
        // 获取get参数
        $id = $this->request()->getQueryParam('id');
        // 输出到终端
        // 返回给客户端
        $res = UserModel::create()->get($id);
        var_dump($res);
        $this->response()->write($res);
    }

    public function create()
    {
        $model = UserModel::create([
            'username' => 'whldk',
            'password' => md5('123456'),
            'real_name' => 'whldk'
        ]);
        $res = $model->save();
        $this->response()->write($res.PHP_EOL);
    }

}