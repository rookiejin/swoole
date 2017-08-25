<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25
 * Time: 13:51
 */

namespace Rookiejin\Swoole\Container;
use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Http\Request;
use Rookiejin\Swoole\Http\Response;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Context
 * 全局共享Context .
 * 用于获取当前环境的上下文
 * 1. controller 中 -> context('request')->
 * 2. context('response')->setHeader();
 * 3. context('session')->set();
 * 4. context('cookie')->set();
 * 5. context('server')->getinfo();
 * @package Rookiejin\Swoole\Container
 */

class Context implements ContextInterface
{

    protected $request;

    protected $response;

    public function __construct(SwRequest $request,SwResponse $response)
    {
        $this->request = $this->initRequest($request);
        $this->response = $this->initResponse($response);
    }

    /**
     * @return mixed
     */
    public function init()
    {
        // todo

    }

    /**
     * @param SwRequest $swRequest
     * 将swRequest对象转成 Rookiejin\Swoole\Request 对象封装并返回
     * @return Request $request
     */
    protected function initRequest(SwRequest $swRequest)
    {
        /**
         * @var $request Request
         */
        return Application::getInstance()->cloneObject(Request::class,['request' => $swRequest]);
    }

    /**
     * @param SwResponse $swResponse
     * 将swResponse对象转成 Rookiejin\Swoole\Response 对象并返回
     * @return Response $response
     */
    protected function initResponse(SwResponse $swResponse)
    {
        return Application::getInstance()->cloneObject(Response::class,['response' => $swResponse]);
    }


    /**
     * @return mixed
     */
    public function kill()
    {
        // TODO: Implement kill() method.
    }


}