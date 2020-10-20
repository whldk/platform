<?php namespace App\HttpController\Common;

use App\Exception\AuthException;
use EasySwoole\Validate\Validate;

class Controller extends \EasySwoole\Http\AbstractInterface\Controller
{
    protected $access = [];       //auth 验证
    protected $filter = [];       //控制器层-参数过滤
    protected $params = [];

    protected function onRequest(string $action): ?bool
    {
        //access
        if (!$this->access()) {
            //$res = $this->user->getIdentity();
            throw new AuthException('401 or 403', 401);
        }
        
        //params
        $this->params = $this->request()->getRequestParam();

        //filter params
        

        return true;
    }
    
    public function access()
    {
        $action = $this->getActionName();
        $access = isset($this->access[$action]) ? $this->access[$action] : (isset($this->access['*']) ? $this->access['*'] : null);

        if (!$access) {
            return true;
        }

        //allow anyone both ? and @
        if (in_array('*', $access, true)) {
            return true;
        }
        //登录获取用户身份信息
        //$role = $this->user->getRole();
        $role = '?';
        if ($role == '?') {
            if (in_array($role, $access, true)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (in_array('@', $access, true) || in_array($role, $access, true)) {
                return true;
            } else {
                return false;
            }
        }

    }


}