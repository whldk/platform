<?php namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        //默认路由 - 使用闭包的方式回调
        $routeCollector->get('/', function (Request $request, Response $response) {
            $response->write('site :' . $request->getUri()->getPath());
            return false;//不再往下请求,结束此次响应
        });
        //支持方式 GET 、 POST 、PUT 、DELETE 、patch
        //查询都是GET请求，新增都是POST，修改是PUT，删除是DELETE等
        $routeCollector->addGroup('/user', function (RouteCollector $collector) {
            $collector->get('/init', '/user/user/index');
        });
    }
}