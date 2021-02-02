<?php
namespace App\HttpController;

use App\HttpModel\User\UserModel;
use Swoole\Http\Status;

class Site extends BaseController
{

    public function index()
    {
        $this->response()->write('site index');
    }

    public function login()
    {
        $param = $this->request()->getRequestParam();
        $model = new UserModel();
        $model->username = $param['username'];
        $model->password = md5($param['password']);
        $remember = $param['remember'] ?: 0;
        if ($user = $model->login()) {
            $sessionHash = md5(time() . $user->id);
            $user->update([
                'loginTime' => time(),
                'loginIp'   => $this->clientRealIP(),
                'session'    => $sessionHash
            ]);
            $rs = $user->toArray();
            unset($rs['password'], $rs['create_at'], $rs['update_at']);
            $rs['loginTime'] = date('Y-m-d H:i:s', time());
            $rs['session'] = $sessionHash;
            if ($remember) {
                $this->response()->setCookie($this->sessionKey, $sessionHash, time() + 86400, '/');
            } else {
                $this->response()->setCookie($this->sessionKey, $sessionHash, time() + 100, '/');
            }
            $this->writeJson(Status::OK, $rs, 'success');
        } else {
            $this->writeJson(Status::BAD_REQUEST, '', '密码错误');
        }
    }

    public function logout()
    {
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }
        if (empty($sessionKey)) {
            $this->writeJson(Status::UNAUTHORIZED, '', '尚未登入');
            return false;
        }
        $result = $this->getWho()->logout();
        if ($result) {
            $this->writeJson(Status::OK, '', "登出成功");
        } else {
            $this->writeJson(Status::UNAUTHORIZED, '', 'fail');
        }
    }

    public function profile()
    {
        $this->writeJson(200, $this->getWho()->toArray(), 'success');
    }
}