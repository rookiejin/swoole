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
    /**
     * @param array | string $str
     * @return bool
     */
    public static function check($str) : bool
    {
        if(empty($str) && !is_numeric($str)){
            return true;
        }
        return false ;
    }

    /**
     * @param array $arr
     * @param       $key
     * @return bool
     */
    public static function checkExist(array $arr,$key) : bool
    {
        if(isset($arr[$key]) && self::check($arr[$key])){
            return true ;
        }
        return false ;
    }
}