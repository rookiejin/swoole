<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:47
 */

namespace Rookiejin\Swoole\Http;


use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Exception\InitException;
use Rookiejin\Swoole\Helper\Collection;
use Rookiejin\Swoole\Helper\Dispatcher;
use Swoole\Http\Server;

class Router
{

    protected $defaultNamespace = 'App\\Controller\\';

    protected $routes;

    protected $allow_method = ['GET','POST','PUT','DELETE','HEAD','PATCH','OPTIONS'];


    public function init(Collection $config)
    {
        $this->routes = $config ;
        $this->routes = $this->check() ;
    }

    public function getRoutes()
    {
        return $this->routes ;
    }

    /**
     * 将路由转换成程序 易于识别的数组, 长期跑在内存
     */
    private function check()
    {
        $routes = new Collection();
        foreach ($this->routes as $uri => $route)
        {
            $newRoute = [];

            if(count($route) < 2)
            {
                throw new InitException("路由配置错误:" . __CLASS__ . "::" . __METHOD__ . "{$uri}");
            }

            if(is_string($route[0]))
            {
                $route [0] = explode(',',strtoupper($route[0])) ;
            }

            if(is_array($route[0]))
            {
                foreach ($route[0] as $key => $method)
                {
                    $route[0] [$key] = strtoupper($method);
                    if(! in_array($route[0] [$key] , $this->allow_method))
                    {
                        throw new InitException("路由方法配置错误::" . $method);
                    }
                }
            }
            else{
                throw new InitException("路由配置错误::" . $route [0]) ;
            }

            $newRoute ['method'] = $route [0];

            if(strstr($route[1],'@') === false){
                throw new InitException("路由配置错误:" . __CLASS__ . "::" . __METHOD__ . "{$uri}");
            }

            list($controller,$action) = explode('@',$route[1]) ;

            if(strstr($controller,'\\') === false)// 使用默认命名空间
            {
                $controller = $this->defaultNamespace . $controller ;
            }
            if(! class_exists($controller)){
                throw new InitException("控制器不存在::" . $controller);
            }
            if(! method_exists($controller ,$action)){
                throw new InitException("控制器方法不存在::" . $controller . "::" . $action);
            }
            $newRoute ['controller'] = $controller ;
            $newRoute ['action'] = $action ;
            $routes[$uri] = $newRoute ;
        }
        return $routes ;
    }

    public function dispatch(Request $request)
    {
        try{
            $uri = $this->parseUri($request->getServer());
            $method = $this->parseMethod($request->getServer());
            $route = $this->getRoute($uri,$method);
            return Dispatcher::dispatch($route['controller'] ,$route ['action']);
        }catch (HttpNotFoundException $e){
            return $e->getMessage();
        }
    }

    public function parseUri($server)
    {
        return substr($server['request_uri'],1,strlen($server['request_uri']) - 1) ;
    }

    public function parseMethod($server)
    {
        return strtoupper($server['request_method']);
    }

    public function getRoute($uri,$method)
    {
        if(isset($this->routes[$uri]))
        {
            $uri = $this->routes[$uri];
            if(in_array($method ,$uri ['method']))
            {
                return $uri ;
            }
        }
        throw new HttpNotFoundException("404 Notfound");
    }

}