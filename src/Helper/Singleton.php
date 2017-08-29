<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 14:55
 */

namespace Rookiejin\Swoole\Helper;

use Rookiejin\Swoole\Exception\RuntimeException;

trait Singleton
{

    public static $instance;

    protected $container = [];

    public static function getInstance()
    {
        return is_null(static::$instance) ? (static::$instance = new static()) : static::$instance;
    }


    public function set($id ,$value)
    {
        $this->container [$id] = $value;
    }

    public function get($id)
    {
        if($this->has($id))
        {
            return $this->container [$id] ;
        }

        return null ;
    }

    public function has($id)
    {
        return isset($this->container [ $id ]) ? true : false;
    }


    public function make($class, $userParams = [])
    {
        if(is_string($class) && ($object = $this->get($class)) !== null)
        {
            return $object ;
        }

        $reflector = new \ReflectionClass($class) ;

        if(!$reflector->isInstantiable())
        {
            throw new RuntimeException("class::{$class} can`t be instanced") ;
        }

        $constructor = $reflector->getConstructor() ;

        if(is_null($constructor))
        {
            $newClass = new $class ;
        }
        else{
            $params = $constructor->getParameters() ;

            $dependencies = $this->getDependencies($params,$userParams);

            $newClass = $reflector->newInstanceArgs($dependencies);
        }

        $this->set($class ,$newClass) ;
        return $newClass ;
    }


    protected function getDependencies($params ,$userParams)
    {
        $dependencies = [];
        foreach ($params as $param) {
            /**
             * @var \ReflectionClass $dependy
             */
            $dependy = $param->getClass();
            $name = $param->getName();
            if(isset($userParams[$name])){
                $dependencies [] = $userParams [$name];
            }else{
                if (is_null($dependy)) {
                    $dependencies [] = $this->resoveNonClass($param);
                } else {
                    $dependencies [] = $this->make($dependy->name);
                }
            }
        }
        return $dependencies;
    }


}