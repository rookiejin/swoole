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


    public function init(SwRequest $request,SwResponse $response)
    {
        $this->request = $this->initRequest($request) ;

        $this->response = $this->initResponse($response) ;
    }


    public function route()
    {
        /**
         * @var Router
         */
        $router = Application::getInstance('router') ;

        $route = $router->match($this->request->getServer());

        return null ;
    }

    public function response()
    {
        
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

    /**
     * @param SwRequest  $swRequest
     * @param SwResponse $swResponse
     */
    public function request(SwRequest $swRequest ,SwResponse $swResponse)
    {
        $this->init($swRequest ,$swResponse);

        $route = $this->route() ;

    }

    /**
     * @return mixed
     */
    public function kill()
    {
        // TODO: Implement kill() method.
    }


}