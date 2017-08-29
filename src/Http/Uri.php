<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 17:55
 */

namespace Rookiejin\Swoole\Http;


class Uri
{

    public static $captor = '([\w\.\-\ ]+)';

    /**
     * @param string $uri
     * @param bool $usingRegex
     * @return array
     */
    public static function parser( string $uri, bool $usingRegex )
    {

        $labels = [] ;
        //   解析成 /xxx/xxx/xxx [/] 这种格式
        $uri = (substr($uri , 0 ,1) === '/') ? $uri : '/' . $uri ;

        if(!$usingRegex)
        {
            $uri = (substr($uri ,0,-1) === '/') ? substr($uri,0,strlen($uri) -1) : $uri ;
            return [
              'pattern' => $uri ,
              'labels' => [],
            ];
        }
        $peaces = explode('/' ,$uri) ;

        foreach ($peaces as $key => $value)
        {
            $peaces[$key] = str_replace('*','(.*)',$value);

            if(strpos($value ,':') === 0)
            {
                $peaces [$key] = self::$captor ;
                $labels [] = substr($value,1) ;
                continue ;
            }
            if(strpos($value,'{') === 0)
            {
                $peaces [$key] = self::$captor ;
                $labels [] = substr($value ,1 ,-1);
            }
        }

        if($peaces [count($peaces) -1])
        {
            $peaces [] = '';
        }

        $pattern = str_replace('/' ,'\/' ,implode('/' ,$peaces)) ;

        return [
            'pattern' => $pattern ,
            'labels' => $labels
        ];
    }
}
