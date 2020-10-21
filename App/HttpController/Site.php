<?php namespace App\HttpController;

use App\HttpController\Common\BaseController;
use App\Models\Admin\AdminModel;
use EasySwoole\Http\Message\Status;


class Site extends BaseController
{

    public $access = [
        '*' => ['*']
    ];

    public $filter = [
        'adminLogin' => [
            '*' => [
                'require' => ['username', 'password', 'msg' => ['username' => '用户名不可为空', 'password' => '密码不可为空']],
            ]
        ]
    ];

    public function index()
    {
       $this->writeJson(Status::CODE_OK, 'site index');
    }

    public function adminLogin()
    {
        $model = new AdminModel;
        $model->username = $this->params['username'];
        $model->password = $this->params['password'];
        if ($admin_user = $model->login()) {
            $sessionHash = md5(time() . $admin_user->_id);
            $admin_user->update([
                'last_login_time' => time(),
                'last_login_ip' => $this->clientRealIP(),
                'session' => $sessionHash
            ]);
            $res = $admin_user->toArray();
            unset($res['password'], $res['_id'], $res['session']);
            $res[$this->sessionKey] = $sessionHash;
            $this->response()->setCookie($this->sessionKey, $sessionHash, time() + 3600, '/');
            $this->writeJson(Status::CODE_OK, $res);
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, '', '用户名不存在或密码错误');
        }
    }

    public function logout()
    {
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);

        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }

        if (empty($sessionKey)) {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', '尚未登入');
            return false;
        }
        $res = $this->getWho()->logout();
        if ($res) {
            $this->writeJson(Status::CODE_OK, '', "登出成功");
        } else {
            $this->writeJson(Status::CODE_UNAUTHORIZED, '', 'fail');
        }
    }

    public function getInfo()
    {
        $this->writeJson(Status::CODE_OK, $this->getWho()->toArray(), 'success');
    }
}