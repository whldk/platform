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
        $routeCollector->get('/banner', '/Banner/getAll');
        $routeCollector->get('/banner-view/{id:\d+}', '/Banner/getOne');
        //允许多种请求方式
        $routeCollector->addRoute(['GET', 'POST'], '/banner-test', '/Banner/test');
        //路由分组
        $routeCollector->addGroup('/test', function (RouteCollector $collector) {
            $collector->get('GET', '/index', function (Request $request, Response $response) {
                $response->write('hello  user path 用户');
            });
            $collector->get('/info', function (Request $request, Response $response){
                $data = $request->getRequestParam();
                var_dump($data);
                $orderId = $request->getRequestParam('orderId');
                var_dump($orderId);
                $mixData = $request->getRequestParam("orderId","type");
                var_dump($mixData);
            });
        });

        $routeCollector->addGroup('/user', function (RouteCollector $collector) {

            $collector->get('GET', '/index', function (Request $request, Response $response) {
                $response->write('hello  user path 用户');
            });

            $collector->addRoute('GET', '/test', function (Request $request, Response $response) {
                $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
                $response->write('张帅');
            });

            $collector->get('/admin-list', '/user/controller/admin/list');
            $collector->get('/admin-search', '/controller/admin/search');
        });


    }
}