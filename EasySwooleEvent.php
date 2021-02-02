<?php
namespace EasySwoole\EasySwoole;

use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Config;
use EasySwoole\Session\Session;
use EasySwoole\Session\SessionFileHandler;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $config = new Config();
        $config->setDatabase('platform');
        $config->setUser('root');
        $config->setPassword('123456');
        $config->setHost('127.0.0.1');
        //连接池配置
        $config->setGetObjectTimeout(3.0); //设置获取连接池对象超时时间
        $config->setIntervalCheckTime(30*1000); //设置检测连接存活执行回收和创建的周期
        $config->setMaxIdleTime(15); //连接池对象最大闲置时间(秒)
        $config->setMinObjectNum(5); //设置最小连接池存在连接对象数量
        $config->setMaxObjectNum(20); //设置最大连接池存在连接对象数量
        $config->setAutoPing(5); //设置自动ping客户端链接的间隔
        DbManager::getInstance()->addConnection(new Connection($config));

        // TODO: 注册 HTTP_GLOBAL_ON_REQUEST 回调，相当于原来的 onRequest 事件
        Di::getInstance()->set(\EasySwoole\EasySwoole\SysConst::HTTP_GLOBAL_ON_REQUEST, function (Request $request, Response $response): bool {
            $cookie = $request->getCookieParams('platform');
            if (empty($cookie)) {
                $sid = Session::getInstance()->sessionId();
                $response->setCookie('platform', $sid);
            } else {
                Session::getInstance()->sessionId($cookie);
            }
            return true;
        });

        // TODO: 注册 HTTP_GLOBAL_AFTER_REQUEST 回调，相当于原来的 afterRequest 事件
        Di::getInstance()->set(\EasySwoole\EasySwoole\SysConst::HTTP_GLOBAL_AFTER_REQUEST, function (Request $request, Response $response): void {

        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // 链接预热
        $register->add($register::onWorkerStart,function (){
            DbManager::getInstance()->getConnection()->__getClientPool()->keepMin();
        });
        // 可以自己实现一个标准的session handler
        $handler = new SessionFileHandler(EASYSWOOLE_TEMP_DIR);
        // 表示cookie name   还有 save path
        Session::getInstance($handler, 'platform', 'session_dir');
    }
}