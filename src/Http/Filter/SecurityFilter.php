<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:56
 */

namespace Rookiejin\Swoole\Http\Filter;


class SecurityFilter implements Filter
{
    /**
     * @param $var
     * @return mixed
     */
    public static function parse($var)
    {
        return $var ;
    }

}