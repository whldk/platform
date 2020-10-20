<?php namespace App\HttpController\User;

use App\User\UserModel;
use EasySwoole\Http\AbstractInterface\Controller;

class User extends Controller
{

    public function index()
    {
        $userModel = new UserModel();
        $res = $userModel->schemaInfo()->getColumns();
        $this->writeJson(200,$res);
    }

}