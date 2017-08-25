<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:47
 */

namespace Rookiejin\Swoole\Http;


class Response
{

    protected $headers = [];

    protected $statusCode = 200;

    protected $cookies = [];

    protected $request = null;

    protected $swResponse = null ;

    /**
     * Response constructor.
     *
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Response $response)
    {
        $this->swResponse = $response ;
    }


    public function bootstrap()
    {
        $this->headers    = [];
        $this->statusCode = 200;
        $this->cookies    = [];
        $this->swResponse = null ;
    }


    public function addHeader(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function deleteHeader($key)
    {
        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[ $key ]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->headers;
    }

    /**
     * @param array $cookies
     * @return $this
     */
    public function addCookie(array $cookies)
    {
        $this->cookies = array_merge($this->cookies, $cookies);

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function deleteCookie($key)
    {
        if (array_key_exists($key, $this->cookies)) {
            unset($this->cookies [ $key ]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCookie(): array
    {
        return $this->cookies;
    }

    public function setStatucCode(int $code)
    {
        $this->statusCode = $code;
    }


    public function respond()
    {

    }

}