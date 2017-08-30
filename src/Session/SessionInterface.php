<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 18:24
 */

namespace Rookiejin\Swoole\Session;


interface SessionInterface
{

    public function open();

    public function create_sid();

    public function destory();

    public function gc() ;

    public function read() ;

    public function write();
}