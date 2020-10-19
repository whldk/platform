<?php namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        // TODO: Implement initialize() method.
        $routeCollector->addRoute('GET','/Banner/getAll');
        $routeCollector->addRoute('GET','/Banner/getOne/{id:\d+}');
        //闭包
        $routeCollector->addRoute('GET', '/test/{id:\d+}', function (Request $request, Response $response) {
            $id = $request->getQueryParam('id');
            $response->write('Userid : ' . $id);
            return false;
        });

        //闭包分组
        $routeCollector->addGroup('/admin', function (RouteCollector $collector){
            $collector->addRoute('GET', '/user', function (Request $request, Response $response){
                var_dump($request);
                return false;
            });
        });
    }
}