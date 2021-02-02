<?php namespace App\HttpController;

use App\HttpModel\User\UserModel;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class BaseController extends Controller
{
    //public才会根据协程清除
    public $who;
    public $role;
    //session的cookie头
    protected $sessionKey = 'platform';
    //白名单
    protected $whiteList = ['login'];


    //参数配置
    protected $params = [];
    //权限配置
    protected $access = [];
    //参数配置
    protected $filter = [];

    protected $header;
    protected $cookie;

    const GROUP_ADMIN = 0;
    const GROUP_SCHOOL_ADMIN = 1;
    const GROUP_TEACHER = 2;
    const GROUP_STUDENT = 3;

    protected static $groups = [
        self::GROUP_ADMIN,
        self::GROUP_SCHOOL_ADMIN,
        self::GROUP_TEACHER,
        self::GROUP_STUDENT
    ];

    protected static $roles = [
        self::GROUP_ADMIN => 'admin',
        self::GROUP_SCHOOL_ADMIN => 'school_admin',
        self::GROUP_TEACHER => 'teacher',
        self::GROUP_STUDENT => 'student'
    ];

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

    public function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            //接受参数
            $request = $this->request();
            $this->params = $request->getRequestParam();
            $this->params += $request->getUploadedFiles();
            $this->params += $request->getQueryParams();
            $content = $request->getBody()->__toString();
            $raw_array = json_decode($content, true) ?? [];
            $this->params += $raw_array;
            $this->header = $request->getHeaders();
            $this->cookie = $request->getCookieParams();
            //白名单判断
            if (in_array($action, $this->whiteList)) {
                return true;
            }

            //权限验证
            $access = isset($this->access[$this->getActionName()]) ? $this->access[$this->getActionName()] : (isset($this->access['*']) ? $this->access['*'] : null);
            if (!$access) {
                return true;
            }
            //allow anyone both ? and @
            if (in_array('*', $access, true)) {
                return true;
            }

            //获取登入信息
            if (!$data = $this->getWho()) {
                $this->writeJson(Status::CODE_UNAUTHORIZED, '', '登入已过期');
                return false;
            }

            //刷新cookie存活
            $this->response()->setCookie($this->sessionKey, $data->session, time() + 7200, '/');


            $role = $this->getRole();
            if (in_array($role, $access, true)) {
                return true;
            } else if (in_array('@', $access, true) || in_array($role, $access, true)) {
               return true;
            } else {
                $this->writeJson(Status::CODE_FORBIDDEN, '', '没有访问权限');
                return false;
            }
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


    public function getRole()
    {
        $who = $this->getWho()->toArray();
        switch ($who['group']) {
            case self::GROUP_ADMIN :
                $this->role = 'admin';
                break;
            case self::GROUP_SCHOOL_ADMIN :
                $this->role = 'school_admin';
                break;
            case self::GROUP_TEACHER :
                $this->role = 'teacher';
                break;
            case self::GROUP_STUDENT :
                $this->role = 'student';
                break;
        }
        return $this->role;
    }

    public function validate(Validate $validate)
    {
        return $validate->validate($this->params);
    }
}