<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/25
 * Time: 14:00
 */

namespace Rookiejin\Swoole\Container;


interface ContextInterface
{
    public function init();

    public function kill();
}