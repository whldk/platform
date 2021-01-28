<?php


namespace EasySwoole\EasySwoole;

use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Db\Config;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        # 配置数据库连接
        $config = new Config();
        $config->setDatabase('platform');
        $config->setUser('root');
        $config->setPassword('123456');
        $config->setHost('127.0.0.1');
        $config->setPort('3306');
        $config->setCharset('utf8');
        $config->setReturnCollection(true);
        $config->setAutoPing(5);
        //连接池配置
        $config->setGetObjectTimeout(3.0); //设置获取连接池对象超时时间
        $config->setIntervalCheckTime(30*1000); //设置检测连接存活执行回收和创建的周期
        $config->setMaxIdleTime(15); //连接池对象最大闲置时间(秒)
        $config->setMinObjectNum(5); //设置最小连接池存在连接对象数量
        $config->setMaxObjectNum(20); //设置最大连接池存在连接对象数量
        $config->setAutoPing(5); //设置自动ping客户端链接的间隔
        DbManager::getInstance()->addConnection(new Connection($config));
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $register->add($register::onWorkerStart,function (){
            // 链接预热
            DbManager::getInstance()->getConnection()->__getClientPool()->keepMin();
        });
    }
}