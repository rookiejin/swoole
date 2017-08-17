<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 15:57
 */

namespace Rookiejin\Swoole\Http\Filter;


use function PHPSTORM_META\type;

class HeaderFilter implements Filter
{
    /**
     * @param $var
     * @return mixed
     */
    public static function parse($var)
    {
        var_dump($var);
    }

}