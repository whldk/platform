<?php namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    public function initialize(RouteCollector $routeCollector)
    {
        $routeCollector->get('/banner', '/Banner/getAll');

        $routeCollector->addRoute(['GET', 'POST'], '/banner-one/{id:\d+}', '/Banner/getOne');
    }
}