<?php
namespace EasySwoole\EasySwoole;

use App\Exception\ExceptionHandler;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        $config = new \EasySwoole\ORM\Db\Config(Config::getInstance()->getConf('MYSQL'));//ORM 的连接注册
        $config->setMaxObjectNum(20); //配置连接池最大数量
        DbManager::getInstance()->addConnection(new Connection($config));
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER,[ExceptionHandler::class,'handle']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $register->add($register::onWorkerStart, function () { //链接预热
            DbManager::getInstance()->getConnection()->getClientPool()->keepMin();
            DbManager::getInstance()->onQuery(function ($res, $builder, $start) {
                $sql = $builder->getLastQuery();
                $time = bcsub(time(), $start, 3);
                writeLog('执行时长'.$time.' s ' . $sql); //写入日志
            });
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}