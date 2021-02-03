<?php
namespace App\HttpController\User;

use App\HttpController\BaseController;
use App\HttpModel\User\UserModel;
use EasySwoole\Http\Message\Status;

class User extends BaseController
{

    public $access = [
        '*' => ['admin'],
        'list' => ['*']
    ];

    public function list()
    {
        $page = $this->params['page'] ?? 1;
        $limit = $this->params['pagesize'] ?? 10;
        $model = new UserModel();
        $data = $model->_list(
            $param['username']??null,
        $param['real_name']??null,
        $param['group'] ?? null,
            $page,
            $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    public function view()
    {
        // 获取get参数
        $id = $this->params['id'] ?: 1;
        $res = UserModel::create()->get($id);
        $this->seed($res, 201);
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