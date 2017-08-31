<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25
 * Time: 14:00
 */

namespace Rookiejin\Swoole\Container;


use Swoole\Http\Request;
use Swoole\Http\Response;

interface ContextInterface
{
    // 初始化 请求
    public function init(Request $request ,Response $response);

    // 路由
    public function route();

    // 响应
    public function response();

    // 结束生命周期
    public function kill();
}
