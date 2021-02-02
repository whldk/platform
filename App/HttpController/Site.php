<?php
namespace App\HttpController;

use App\HttpModel\User\UserModel;
use EasySwoole\Validate\Validate;
use Swoole\Http\Status;

class Site extends BaseController
{
    public $access = [
        '*' => ['@'],
        'index' => ['*'],
        'test' => ['admin']
    ];

    public function onRequest(?string $action): ?bool
    {
        $ret =  parent::onRequest($action);
        if($ret === false){
            return false;
        }
        $v = $this->validateRule($action);
        if($v){
            $ret = $this->validate($v);
            if($ret == false){
                $this->writeJson(Status::BAD_REQUEST,null,"{$v->getError()->getFieldAlias()}{$v->getError()->getErrorRuleMsg()}");
                return false;
            }
        }
        return true;
    }

    public function validateRule(?string $action):?Validate
    {
        $v = new Validate();
        switch ($action) {
            case 'login':
                $v->addColumn('username','用户名')->required('不能为空');
                $v->addColumn('password', '密码')->required('不能为空');
                break;
            case 'test':
                $v->addColumn('id', '用户id')->required('不能为空');
                break;
        }
        return $v;
    }

    public function test()
    {
        $this->response()->write('site test admin');
    }

    public function index()
    {
        $this->response()->write('site index');
    }

    public function login()
    {
        $model = new UserModel();
        $model->username = $this->params['username'];
        $model->password = md5( $this->params['password']);
        $remember = $this->params['remember'] ?? 0;
        if ($user = $model->login()) {
            $sessionHash = md5(time() . $user->id);
            $user->update([
                'loginTime' => time(),
                'loginIp'   => $this->clientRealIP(),
                'session'    => $sessionHash
            ]);
            $rs = $user->toArray();
            unset($rs['password']);
            $rs['loginTime'] = date('Y-m-d H:i:s', time());
            $rs['session'] = $sessionHash;
            if ($remember) {
                $this->response()->setCookie($this->sessionKey, $sessionHash, time() + 86400, '/');
            } else {
                $this->response()->setCookie($this->sessionKey, $sessionHash, time() + 7200, '/');
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
        $this->writeJson(200, $this->who, 'success');
    }
}