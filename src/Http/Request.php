<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:10
 */

namespace Rookiejin\Swoole\Http;

use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Http\Filter\CookieFilter;
use Rookiejin\Swoole\Http\Filter\FileFilter;
use Rookiejin\Swoole\Http\Filter\SecurityFilter;
use Rookiejin\Swoole\Http\Filter\PostFilter;
use Rookiejin\Swoole\Http\Filter\RawFilter;
use Rookiejin\Swoole\Http\Filter\ServerFilter;
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
        $response->end(json_encode($request));
    }

    /**
     * 获取get参数
     * @param      $key
     * @param null $default
     * @param bool $filter
     * @throws RuntimeException
     * @return mixed $out
     */
    private function _fetch_from($arr , $key = null, $default = null, $filter = true)
    {
        if(is_array($key))
        {
            $out = [] ;
            foreach ($key as $value)
            {
                if(isset($arr[$value]))
                {
                    $out[$value] =  $filter ? SecurityFilter::parse($arr[$value]) : $arr[$value];
                }
            }
            if(is_array($default)){
                $out = array_merge($default,$out);
            }
        }
        elseif(is_string($key)){
            if(isset($arr[$key])){
                $out = $filter ? SecurityFilter::parse($arr[$key]) : $arr[$key];
            }
            else{
                if(isset($default)){
                    $out = $default ;
                }
            }
        }
        elseif(is_null($key)){
            $out = $filter ? SecurityFilter::parse($arr) : $arr ;
            if(is_array($default)){
                $out = array_merge($default,$out);
            }
        }
        if(!isset($out)){
            throw new RuntimeException("获取get参数失败" . __CLASS__ . '::' . __METHOD__ );
        }
        return $out ;
    }

    /**
     * @param null $key
     * @param null $default
     * @param bool $filter
     * @return mixed
     */
    public function get($key = null ,$default = null , $filter = true)
    {
        return $this->_fetch_from($this->_get ,$key ,$default ,$filter);
    }
    
    
}


























