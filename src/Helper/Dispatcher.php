<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 16:42
 */

namespace Rookiejin\Swoole\Helper;


use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Exception\NotFoundException;

class Dispatcher
{
    public static function dispatch($className,$method,...$params)
    {
        if(class_exists($className)){
            return self::invokeMethod([$className,$method],$params);
        }
        throw new NotFoundException("class or method not found {$class}::{$method}");
    }

    /**
     * 创建对象方法的反射
     */
    public static function invokeMethod($method,$vars)
    {
        if(is_array($method)){
            $class = is_object($method [0]) ? $method [0] : Application::getInstance()->make($method[0]);
            $reflect = new \ReflectionMethod($class,$method[1]);
        }else{
            $reflect = new \ReflectionMethod($method);
        }
        // bugs exists
        if($reflect->getNumberOfParameters() > 0){
            return $reflect->invokeArgs(isset($class)?$class:null,$vars);
        }
        throw new NotFoundException("method or class not found::{$method}");
    }
}