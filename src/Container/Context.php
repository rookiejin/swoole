<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25
 * Time: 13:51
 */

namespace Rookiejin\Swoole\Container;
use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Helper\Singleton;
use Rookiejin\Swoole\Http\Request;
use Rookiejin\Swoole\Http\Response;
use Rookiejin\Swoole\Http\Route;
use Rookiejin\Swoole\Http\Router;
use Rookiejin\Swoole\Http\Session;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Context
 * 全局共享Context .
 * 用于获取当前环境的上下文, 这是 一个请求生命周期的单例 ，请求完成，单例释放。
 * 1. controller 中 -> context('request')->
 * 2. context('response')->setHeader();
 * 3. context('session')->set();
 * 4. context('cookie')->set();
 * 5. context('server')->getinfo();
 * @package Rookiejin\Swoole\Container
 */

class Context implements ContextInterface
{

    use Singleton;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Session
     */
    protected $session ;


    public function __construct()
    {
        self::$instance = & $this;
    }


    public function init(SwRequest $request,SwResponse $response)
    {
        $this->request = $this->initRequest($request) ;

        $this->session = $this->initSession();

        $this->response = $this->initResponse($response) ;
    }

    /**
     * @return Route $route
     */
    public function route()
    {
        /**
         * @var Router
         */
        $router = Application::getInstance('router') ;

        /**
         * @var Route
         */
        return $router->match($this->request->getServer());
    }

    /**
     * @param SwRequest $swRequest
     * 将swRequest对象转成 Rookiejin\Swoole\Request 对象封装并返回
     * @return Request $request
     */
    protected function initRequest(SwRequest $swRequest)
    {
        return $this->make(Request::class,['request' => $swRequest]) ;
    }

    /**
     * @param SwResponse $swResponse
     * 将swResponse对象转成 Rookiejin\Swoole\Response 对象并返回
     * @return Response $response
     */
    protected function initResponse(SwResponse $swResponse)
    {
        return $this->make(Response::class,['response' => $swResponse]);
    }

    protected function initSession()
    {
        $config = app('config')->app ;
        if(isset($config ['session']['save_path']))
        {
            $path = $config ['session'] ['save_path'] ;
        }
        else{
            $path = storage_path() . 'framework/session' ;
        }
        if(!is_dir($path)){
            @mkdir($path,0777,true) ;
        }
    }

    /**
     * @param SwRequest  $swRequest
     * @param SwResponse $swResponse
     * @return null
     */
    public function request(SwRequest $swRequest ,SwResponse $swResponse)
    {
        $this->init($swRequest ,$swResponse);

        $route = $this->route() ;

        /**
         * 这里应该是纯字符串 html 等等
         */
        $response = $route->execAction($this);

        return $this->response( $response );
    }


    public function response($response)
    {
        /**
         * 发送响应头
         */
        $this->response->sendHeader() ;

        $this->response->sendCookies() ;

        $this->response->sendStatus() ;

        return $response ;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request ;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response ;
    }

    /**
     * @return mixed
     */
    public function kill()
    {
        return $this->clearInstance();
    }

}
