<?php

/*
$arr = [1, 2, 3];
$arr1 = [4, 5, 6];
$after = array_map(function ($a, $b) {
    var_dump($a, $b);exit;
}, $arr, $arr1);
*/

function func($name){
    return function ($part1, $part2) use ($name) {
        return sprintf('LoveLive-%s! %s, %s', $name, $part1, $part2);
    };
}
// $live = func('kotori');
// var_dump($live('umi', 'honoka'));

// https://blog.csdn.net/qq_38287952/article/details/83104718
class App
{

    protected $routes = [];
    protected $responseStatus = '200 OK';
    protected $responseContentType = 'text/html; charset=utf8';
    protected $responseBody = 'LoveLive';
    protected $responseXPowerBy = 'TypeMoon';

    public function addRoute($routePath, $routeCallback)
    {
        $this->routes[$routePath] = $routeCallback->bindTo($this, __CLASS__);
    }

    public function dispath($currentPath)
    {
        foreach ($this->routes as $routePath => $callback) {
            if ($routePath == $currentPath) {
                $callback();
            }
        }
        header('HTTP/1.1 ' . $this->responseStatus);
        header('Content-Type:' . $this->responseContentType);
        // header('Content-Length: ' . $this->responseBody);
        header("X-Powered-By:" . $this->responseXPowerBy);
        header("Server:Tinkle");
        echo __CLASS__;
    }
}

$app = new App();
$app->addRoute('/moon/akiha', function () {
    // Content-Type来表示具体请求中的媒体类型信息
    $this->responseContentType = 'application/json; charset=utf8';
    // $this->responseBody = '{"name": "tooho akiha"}';
});
$app->dispath('/moon/akiha1');