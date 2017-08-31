<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15
 * Time: 9:56
 */

namespace Rookiejin\Swoole\Config;


use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Helper\Collection;

class Config
{
    public $config = [];

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->config[ $key ];
        }

        if( strstr($key ,'.') !== false)
        {
            $keys = explode('.',$key);
            if( ($collect = $this->get($keys [0])) === null)
            {
                return null ;
            }
            else{
                unset($keys[0]) ;
                return $collect->get(implode('.',$keys));
            }
        }
        return null;
    }

    public function set($key, $value)
    {
        if(is_array($value))
        {
            $this->config [$key] = new Collection($value);
        }
        else{
            $this->config [$key] = [$value];
        }
    }

    public function has($key)
    {
        return isset($this->config[ $key ]) ? true : false;
    }

    public static function load($path)
    {
        $dirs   = scandir($path);
        $total = count($dirs);
        for($i = 2; $i < $total; $i ++)
        {
            if(is_dir( $path . DIRECTORY_SEPARATOR . $dirs [$i] ))
            {
                Config::load($path . DIRECTORY_SEPARATOR . $dirs [$i]) ;
            }
            else{
                $tmp = require_once $path . DIRECTORY_SEPARATOR . $dirs [$i] ;
                if(is_array($tmp))
                {
                    app('config')->set(str_replace('.php' ,'',$dirs [$i]), $tmp);
                }
                unset($tmp);
            }
        }
    }

    /**
     * getSession => $config ['session'] [$key] ;
     * @param $key
     * @return null
     */
    public function __get($key)
    {
        if($this->has($key)){
            return $this->get($key);
        }
        return null ;
    }
}