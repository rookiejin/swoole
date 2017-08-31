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


    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}