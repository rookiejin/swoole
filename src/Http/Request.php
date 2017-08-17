<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:10
 */

namespace Rookiejin\Swoole\Http;

use Rookiejin\Swoole\Http\Filter\CookieFilter;
use Rookiejin\Swoole\Http\Filter\FileFilter;
use Rookiejin\Swoole\Http\Filter\GetFilter;
use Rookiejin\Swoole\Http\Filter\HeaderFilter;
use Rookiejin\Swoole\Http\Filter\MethodFilter;
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
        var_dump($request);
        isset($request->get) && $this->_get = GetFilter::parse($request->get);
        isset($request->_post) && $this->_post = PostFilter::parse($request->post);
        isset($request->_files) && $this->_files = FileFilter::parse($request->files);
        isset($request->_cookies) && $this->_cookies = CookieFilter::parse($request->cookie);
        isset($request->_headers) && $this->_headers = HeaderFilter::parse($request->header);
        $this->_raw    = RawFilter::parse($request->rawContent());
        $this->_server = ServerFilter::parse($request->server);
//        $response->end('1');

//        return new Response();
    }
}