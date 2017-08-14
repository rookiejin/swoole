<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 11:05
 */

namespace Rookiejin\Swoole\Container;


use Psr\Container\ContainerInterface;
use Rookiejin\Swoole\Exception\NotFoundException;
use Rookiejin\Swoole\Exception\RuntimeException;

class Container implements ContainerInterface
{

    protected $instance = [] ;

    protected $alias = [] ;

    public function get($id)
    {
        if($this->has($id)){
            return $this->instance [$id];
        }
        if(class_exists($id)){
            return $this->make($id);
        }
        throw new NotFoundException("the {$id} not found in the container");
    }

    public function has($id)
    {
        return isset($this->instance[$id]) ? true : false ;
    }


    public function make($class)
    {
        if(is_string($class) && $this->has($class)){
            return $this->get($class);
        }
        if($this->alias($class) !== null){
            if(is_object($this->alias[$class])){
                return $this->alias[$class];
            }else{
                $class = $this->alias [$class];
            }
        }
        $reflector = new \ReflectionClass($class) ;
        if(!$reflector->isInstantiable()){
            throw new RuntimeException("can`t instantiate of class::" . $class);
        }

        $construct = $reflector->getConstructor();
        if(is_null($construct)){
            return new $class ;
        }

        $params = $construct->getParameters() ;

        $dependencies = $this->getDependencies($params);

        $newClass = $reflector->newInstanceArgs($dependencies);

        $this->instance[$class] = $newClass ;

        return $newClass ;
    }


    public function getDependencies(array $params)
    {
        $dependencies = [] ;
        foreach ($params as $param){
            /**
             * @var \ReflectionClass $dependy
             */
            $dependy = $param->getClass() ;
            if(is_null($dependy)){
                $dependencies [] = $this->resoveNonClass($param);
            }else{
                $dependencies [] = $this->make($dependy->name);
            }
        }

        return $dependencies ;
    }

    public function resoveNonClass(\ReflectionParameter $parameter)
    {
        if($parameter->isDefaultValueAvailable()){
            return $parameter->getDefaultValue() ;
        }
        throw new RuntimeException($parameter->getName() . "must be not null");
    }

    public function alias($exceped ,$alias = null)
    {
        if(is_null($alias)){
            return isset($this->alias[$exceped]) ? $this->alias [$exceped] : null ;
        }
        $this->alias [$exceped] = $alias ;
        return $this ;
    }
}