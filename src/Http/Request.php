<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:10
 */

namespace Rookiejin\Swoole\Http;
use Rookiejin\Swoole\Http\Filter\HeaderFilter;
use \Swoole\Http\Request as SwRequest;
use \Swoole\Http\Response as SwResponse;

class Request
{

    protected $_get ;

    protected $_post ;

    protected $_cookies;

    protected $_session;

    protected $_files ;

    protected $_request;

    protected $_raw ;

    protected $_method ;

    protected $_headers ;

    const EOF = "\r\n\r\n";

    public function request(SwRequest $request,\Swoole\Http\Response $response) : \Swoole\Http\Response
    {
        $this->_headers = HeaderFilter::parse($request->header);
        $response->end("hahaha");
        return $response;
    }
}