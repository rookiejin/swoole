<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 14:39
 */

namespace Rookiejin\Swoole\Helper;


class Str
{
    public static function obj2str($object)
    {
        $str = "";
        foreach ($object as $key => $val){
            $str .= "{$key} => {$val} " . PHP_EOL ;
        }
        return $str;
    }
}