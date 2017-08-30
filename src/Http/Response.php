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

    protected $headers = ['Server' => 'Swoole-MVC'];

    protected $statusCode = 200;

    protected $cookies = [];

    protected $request = null;

    protected $swResponse = null;

    /**
     * Response constructor.
     *
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Response $response)
    {
        $this->swResponse = $response;
    }

    public function addHeader(array $headers)
    {
        // 第一个字母大写
        foreach ($headers as $key => $value) {
            $headers [ strtolower(str_replace('_', '-', $key)) ] = $value;
        }
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
     * @param string $key
     * @param string $value
     * @param int    $expire
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     * @return $this
     */
    public function addCookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false)
    {
        $this->cookies [] = ['key'      => $key,
                             'value'    => $value,
                             'expire'   => $expire,
                             'path'     => $path,
                             'domain'   => $domain,
                             'secure'   => $secure,
                             'httponly' => $httponly,
        ];

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


    public function sendHeader()
    {
        foreach ($this->getHeader() as $key => $value) {
            $this->swResponse->header($key, $value);
        }
    }

    public function sendCookies()
    {
        if(!empty($this->cookies))
        {
            foreach ($this->cookies as $key => $val)
            {
                $this->swResponse->cookie(
                    $val ['key'] ,
                    $val ['value'],
                    (int) $val ['expire'],
                    $val ['path'] === '' ? '/' : $val ['path'] ,
                    $val ['domain'] === '' ? '' : $val ['domain'] ,
                    $val['secure'] ,
                    $val ['httponly']
                );
            }
        }
    }

    public function sendStatus()
    {
        $this->swResponse->status(200);
    }

    public function respond($text)
    {
        $this->swResponse->end($text);
    }

}
