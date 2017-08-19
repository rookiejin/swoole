<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:10
 */

namespace Rookiejin\Swoole\Http;

use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Http\Filter\CookieFilter;
use Rookiejin\Swoole\Http\Filter\FileFilter;
use Rookiejin\Swoole\Http\Filter\SecurityFilter;
use Rookiejin\Swoole\Http\Filter\PostFilter;
use \Swoole\Http\Request as SwRequest;
use \Swoole\Http\Response as SwResponse;

class Request
{

    protected $_get = [];

    protected $_post = [];

    protected $_cookies = [];

    protected $_files = [];

    protected $_raw = "";

    protected $_method = [];

    protected $_headers = [];

    protected $_session = [];

    protected $_server = null;

    /**
     * @var Router
     */
    protected $router ;

    const EOF = "\r\n\r\n";

    public function request(SwRequest $request, \Swoole\Http\Response $response)
    {
        isset($request->_headers) && $this->_headers = $request->header;
        isset($request->get) && $this->_get = $request->get;
        isset($request->_post) && $this->_post = $request->post;
        isset($request->_files) && $this->_files = $request->files;
        isset($request->_cookies) && $this->_cookies = $request->cookie;
        $this->_raw    = $request->rawContent();
        $this->_server = $request->server;
        $this->router = Application::getInstance()->make(Router::class);
        $response->end( $this->router->dispatch( $this ) );
//        $response->end(json_encode($request));
    }

    /**
     * 获取get参数
     *
     * @param      $key
     * @param null $default
     * @param bool $filter
     * @throws RuntimeException
     * @return mixed $out
     */
    private function _fetch_from_request($arr, $key = null, $default = null, $filter = true)
    {
        if (is_array($key)) {
            $out = [];
            foreach ($key as $value) {
                if (isset($arr[ $value ])) {
                    $out[ $value ] = $filter ? SecurityFilter::parse($arr[ $value ]) : $arr[ $value ];
                }
            }
            if (is_array($default)) {
                $out = array_merge($default, $out);
            }
        } elseif (is_string($key)) {
            if (isset($arr[ $key ])) {
                $out = $filter ? SecurityFilter::parse($arr[ $key ]) : $arr[ $key ];
            } else {
                if (isset($default)) {
                    $out = $default;
                }
            }
        } elseif (is_null($key)) {
            $out = $filter ? SecurityFilter::parse($arr) : $arr;
            if (is_array($default)) {
                $out = array_merge($default, $out);
            }
        }
        if (!isset($out)) {
            throw new RuntimeException("获取get参数失败" . __CLASS__ . '::' . __METHOD__);
        }

        return $out;
    }

    /**
     * @param null $key
     * @param null $default
     * @param bool $filter
     * @return mixed
     */
    public function get($key = null, $default = null, $filter = true)
    {
        return $this->_fetch_from_request($this->_get, $key, $default, $filter);
    }


    /**
     * @param null $key
     * @param null $default
     * @param bool $filter
     * @return mixed
     */
    public function post($key = null, $default = null, $filter = true)
    {
        return $this->_fetch_from_request($this->_post, $key, $default, $filter);
    }

    /**
     * 获取get . post 的所有参数
     *
     * @return array
     */
    public function all()
    {
        return array_merge($this->get(), $this->post());
    }

    /**
     * @param null $key
     * @return mixed|null'
     */
    public function file($key = null)
    {
        if (is_null($key)) {
            return FileFilter::parse($this->_files);
        }

        if (isset($this->_files [ $key ])) {
            return FileFilter::parse($this->_files[ $key ]);
        }

        return null;
    }

    public function cookie($key = null,$default = null)
    {
        if(is_null($key)){
            return CookieFilter::parse($this->_cookies);
        }

        if(isset($this->_cookies[$key])){
            return CookieFilter::parse($this->_cookies[$key]);
        }
        if(is_null($key)){
            return is_null($default) ?: $default ;
        }
    }

    /**
     * @return array
     */
    public function getGet()
    {
        return $this->_get;
    }

    /**
     * @param array $get
     */
    public function setGet($get)
    {
        $this->_get = $get;
    }

    /**
     * @return array
     */
    public function getPost()
    {
        return $this->_post;
    }

    /**
     * @param array $post
     */
    public function setPost($post)
    {
        $this->_post = $post;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->_cookies;
    }

    /**
     * @param array $cookies
     */
    public function setCookies($cookies)
    {
        $this->_cookies = $cookies;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->_files = $files;
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return $this->_raw;
    }

    /**
     * @param string $raw
     */
    public function setRaw($raw)
    {
        $this->_raw = $raw;
    }

    /**
     * @return array
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @param array $method
     */
    public function setMethod($method)
    {
        $this->_method = $method;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
    }

    /**
     * @return array
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @param array $session
     */
    public function setSession($session)
    {
        $this->_session = $session;
    }

    /**
     * @return null
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @param null $server
     */
    public function setServer($server)
    {
        $this->_server = $server;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }


}


























