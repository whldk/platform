<?php
namespace App\Exception;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class ExceptionHandler
{
    public static function handle(\Throwable $exception, Request $request, Response $response) {
        $data=[];
        if ($exception instanceof  ApiException) {
            $code = $exception->getCode();
            $msg = $exception->getMessage();
        } else {
            $code = $exception->getCode();
            if(!isset($code) || $code < 0){
                $code = -1;
            }
            $msg = ($exception->getMessage()) ?? 'unknow';
        }
        $data['code'] = $code;
        $data['msg'] = $msg;
        $result = json_encode($data,JSON_UNESCAPED_UNICODE);
        return $response->withHeader("Content-Type","application/json;charset=UTF-8")
            ->withHeader("Access-Control-Allow-Origin","*")
            ->write($result);
    }
}