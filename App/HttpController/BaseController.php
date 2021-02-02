<?php namespace App\HttpController;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

class BaseController extends Controller
{
    const PAGESIZE = 10;
    const PAGE_PARAM = 'page';
    const PAGESIEZE_PARAM = 'pagesize';
    const MAX_PAGESIZE = 100;

    protected $page = 0;
    protected $pagesize = null;

    protected $access = [];
    protected $filter = [];
    protected $params;
    protected $cookie;
    protected $header;

    protected function onRequest(?string $action): ?bool
    {
        $before = $this->beforeRun();
        if ($before !== true) {
            $this->writeJson(401,null,'请先登录');
            return false;
        }
        return $before;

        return parent::onRequest($action); // TODO: Change the autogenerated stub
    }

    public function beforeRun()
    {


//        if (!$this->access()) {
//            return false;
//        }

        //params 若POST与GET存在同键名参数，则以GET为准
        $request = $this->request();
        $this->params = $request->getRequestParam();
        $this->params += $request->getQueryParams();
        $this->params += $request->getUploadedFiles();
        $this->params += $request->getQueryParams();
        $this->params += $request->getParsedBody();
        $content = $request->getBody()->__toString();
        //$this->params +=  $content ? json_decode($content, true) : [];
        $this->header = $request->getHeaders();
        $this->cookie = $request->getCookieParams();

        var_dump($this->params, $this->cookie);
        return true;
     }

    public function afterAction(?string $actionName): void
    {
        parent::afterAction($actionName); // TODO: Change the autogenerated stub
    }

    protected function access()
    {
        $action = $this->getActionName();
        $access = isset($this->access[$action]) ? $this->access[$action] : (isset($this->access['*']) ? $this->access['*'] : null);
        if (!$access) {
            return false;
        }

        //获取cookie 查看用户身份权限
        $instance = Config::getInstance();
        $cookie = $this->request()->getCookieParams($instance->getConf('COOKIE_NAME'));
        $this->checkSession($cookie);

        if (in_array('*', $access, true)) {
            return true;
        }

        //var_dump($cookie);
        $role = $cookie ? $cookie : '?';

        if ($role !== '?') {
            //var_dump($role);
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

    public function params()
    {
        return $this->params;
    }

    public function seed($res, $statusCode)
    {
        $this->response()->withHeader('Content-type','application/json;charset=utf-8');
        $this->response()->withStatus($statusCode);
        $this->response()->write($res);
    }

    public function checkSession($sid)
    {
        $client = new \EasySwoole\Mysqli\Client(Config::getInstance()->getConf('MYSQL'));
        //查询sid
        $client->queryBuilder()->where('id', $sid)->get('session');
        $res = $client->execBuilder();
        $res = $res ? $res[0] : null;
        if ($res !== null) {
            //过期时间接近5分钟内,则更新
            if ($res['expire'] < time() + 60 * 5) {
                //更新时长
                $client->queryBuilder()->where('expire', time() + 7200)->update('updateTable', ['id' => $sid]);
                $client->execBuilder();
            }
        } else {
            //不存在则插入
            $client->queryBuilder()->insert('session', ['id' => $sid, 'expire' => time() + 7200]);
            $client->execBuilder();
        }

        //自动删除过期的session
        $client->queryBuilder()->where('expire', time(), '<=')->delete('session');
        $client->execBuilder();
        //echo $client->queryBuilder()->getLastQuery();

        $client->queryBuilder()->where('id', $sid)->get('session');
        $res = $client->execBuilder();

        var_dump($res);
    }

    function index()
    {
        $this->actionNotFound('index');
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

    protected function input($name, $default = null)
    {
        $value = $this->request()->getRequestParam($name);
        return $value ?? $default;
    }
    


}