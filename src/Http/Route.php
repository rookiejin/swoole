<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/19
 * Time: 15:26
 */

namespace Rookiejin\Swoole\Http;


use Rookiejin\Swoole\Container\Context;

class Route
{
    /**
     * @var null array 请求方法
     */
    protected $method = null;

    /**
     * @var null 路由 request_uri
     */
    protected $uri = null;

    /**
     * @var null 参数绑定
     */
    protected $labels = null;

    /**
     * @var null 路由回调对象
     */
    protected $callable = null;


    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    public function setCallAble($callable)
    {
        $this->callable = $callable;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function execAction()
    {
        $callable = $this->getCallable();
        if ($callable instanceof \Closure)
        {
            $response = Context::invokeFunction($callable);
        }
        else{
            $response = Context::invokeMethod($callable);
        }

        return $response ;
    }
}
