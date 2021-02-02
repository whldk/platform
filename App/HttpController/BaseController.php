<?php namespace App\HttpController;

use App\HttpModel\User\UserModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{
    //public才会根据协程清除
    public $who;
    //session的cookie头
    protected $sessionKey = 'platform';
    //白名单
    protected $whiteList = ['login'];

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

    public function index()
    {
        // TODO: Implement index() method.
        $this->actionNotFound('index');
    }

    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            //白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }
            //获取登入信息
            if (!$this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
                return false;
            }
            return true;
        }
        return false;
    }

    public function getWho(): ?UserModel
    {
        if ($this->who instanceof UserModel) {
            return $this->who;
        }
        $sessionKey = $this->request()->getRequestParam($this->sessionKey);
        if (empty($sessionKey)) {
            $sessionKey = $this->request()->getCookieParams($this->sessionKey);
        }
        if (empty($sessionKey)) {
            return null;
        }
        $adminModel = new UserModel();
        $adminModel->session = $sessionKey;
        $this->who = $adminModel->getOneBySession();
        return $this->who;
    }
}