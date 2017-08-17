<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:58
 */

namespace Rookiejin\Swoole\Http\Filter;


interface Filter
{
    public static function parse($var);
}