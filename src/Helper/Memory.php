<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 17:31
 */

namespace Rookiejin\Swoole\Helper;


class Memory
{
    public static $marks = [];

    public static function mark($mark)
    {
        if(isset(self::$marks [$mark]))
        {
            $r =  memory_get_usage() - self::$marks[$mark];
            unset(self::$marks[$mark]);
            return $r ;
        }
        else{
            self::$marks [$mark] = memory_get_usage();
        }
    }
}