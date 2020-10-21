<?php namespace App\HttpController\Common;

use App\Base\Validators;
use App\Exception\AuthException;
use App\HttpController\User\Controller\Admin;
use App\HttpModels\Admin\AdminModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{
    public $who;
    public $sessionKey = 'plat_session';
    protected $access = [];       //auth 验证
    protected $filter = [];       //控制器层-参数过滤
    protected $params;            //参数

    public function onRequest(?string $action): ?bool
    {
        if (!$this->access()) {
            throw new AuthException('401 or 403', 401);
        }

        $this->params = $this->request()->getRequestParam();

        //filter
        if (($valid = $this->filter()) !== true) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, $valid);
        }

        return parent::onRequest($action); // TODO: Change the autogenerated stub
    }

    public function access()
    {
        $access = isset($this->access[$this->getActionName()]) ? $this->access[$this->getActionName()] : (isset($this->access['*']) ? $this->access['*'] : null);

        if (!$access) {
            return true;
        }

        //allow anyone both ? and @
        if (in_array('*', $access, true)) {
            return true;
        }

        //登录获取用户身份信息
        $who = $this->getWho();

        $role = $who ? ($who['group'] == 0 ? 'admin' : null ) : null;

        if (!$role) {
            return false;
        }

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

    public function filter()
    {
        $role = $this->getWho();

        $filter = isset($this->filter[$this->getActionName()]) ? $this->filter[$this->getActionName()] : (isset($this->filter['*']) ? $this->filter['*'] : null);
        if (!$filter) {
            return true;
        }

        $filter = (isset($filter[$role]) ? $filter[$role] : []) + (isset($filter['*']) ? $filter['*'] : []);
        if (!$filter) {
            return true;
        }

        //require
        if (isset($filter['require'])) {
            $valid = Validators::requireValidate($filter['require'], $this->params);
            if ($valid !== true) {
                return $valid;
            }
        }
    }
    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的标头名称
     * @return string
     */
    protected function clientRealIP($headerName = 'x-real-ip')
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $client = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri = $this->request()->getHeader($headerName);
        $xff = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }


    public function getWho():?AdminModel
    {
        if ($this->who instanceof Admin) {
            return $this->who;
        }
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }
        if (empty($sessionKey)) {
            return null;
        }
        $adminModel = new AdminModel();
        $adminModel->session = $sessionKey;
        $this->who = $adminModel->getOneBySession();
        return $this->who;
    }
}