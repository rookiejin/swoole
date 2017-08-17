<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15
 * Time: 9:56
 */

namespace Rookiejin\Swoole\Config;


use Rookiejin\Swoole\Application;

class Config implements \ArrayAccess
{

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            $this->config [ $offset ] = null;
            unset($this->config[ $offset ]);
        }
    }

    public $config = [];

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->config[ $key ];
        }

        return null;
    }

    public function set($key, $value)
    {
        $this->config [ $key ] = $value;
    }

    public function has($key)
    {
        return isset($this->config[ $key ]) ? true : false;
    }

    public static function load($path)
    {
        $dirs   = scandir($path);
        foreach ($dirs as $key => $val) {
            if (!in_array($val, ['.', '..'])) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $val)) {
                    Config::load($path . DIRECTORY_SEPARATOR . $val);
                } else {
                    $tmp = require_once $path . DIRECTORY_SEPARATOR . $val;
                    if (is_array($tmp)) {
                         $val = str_replace('.php','',$val);
                        Application::getInstance('config')->set($val, $tmp);
                    }
                    unset($tmp);
                }
            }
        }
    }

    /**
     * getSession => $config ['session'] [$key] ;
     * @param $key
     */
    public function __get($key)
    {
        if($this->has($key)){
            return $this->get($key);
        }
        return null ;
    }
}