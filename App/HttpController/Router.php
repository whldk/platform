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
        //路由分组-闭包
        $routeCollector->addGroup('/test', function (RouteCollector $collector) {
            $collector->addRoute('GET', '/test', function (Request $request, Response $response) {
                $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
                $response->write('张帅');
            });
            $collector->get('/info', function (Request $request, Response $response) {
                //获取所有参数
                $data = $request->getRequestParam();
                //获取指定参数
                $orderId = $request->getRequestParam('orderId');
                //获取多个参数
                $mixData = $request->getRequestParam("orderId","type");
            });
        });

        $routeCollector->addGroup('/user', function (RouteCollector $collector) {

            $collector->get('/index',  function (Request $request, Response $response) {
                $response->write('hello  user index');
            });

            $collector->get('/admin/index', '/controller/admin/index');
            $collector->get('/admin/list', '/controller/admin/list');
            $collector->get('/admin/search', '/controller/admin/search');
        });


    }
}