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

    public $i ;

    public static function getInstance()
    {
        return is_null(self::$instance) ? new self() : self::$instance;
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

    /**
     * @param $params
     * @param $userParams
     * @return array
     */
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

    /**
     * @param       $func
     * @param array $params
     * @return mixed
     * @throws RuntimeException
     */
    public static function invokeFunction($func,$params=[])
    {

        $ref = new \ReflectionFunction($func);
        $args = [];
        if($ref->getNumberOfParameters() > 0)
        {
            $paramters = $ref->getParameters();
            foreach ($paramters as $parameter)
            {
                if(isset($params[$parameter->getName()]))
                {
                    $args [$parameter->getName()] = $params [$parameter->getName()];
                }
                else{
                    if($parameter->isDefaultValueAvailable())
                    {
                        $args [$parameter->getName()] = $parameter->getDefaultValue();
                    }
                    else{
                        $class = $parameter->getClass();
                        if($class != null)
                        {
                            $class = static::getInstance()->make($class);
                            $args[$parameter->getName()] = $class ;
                        }
                        else{
                            throw new RuntimeException("invoke closure function arguments error:" . __CLASS__ . "::" . __METHOD__);
                        }
                    }
                }
            }
        }
        return $ref->invoke($args);
    }

    /**
     * @param       $method
     * @param array $params
     * @return mixed
     * @throws RuntimeException
     */
    public static function invokeMethod($method, $params = [])
    {
        $controller = static::getInstance()->make($method[0], $params) ;
        if(!method_exists($controller,$method [1]))
        {
            throw new RuntimeException('invoke method failed:' . $method[0] . "::" . $method[1] . ",method not exist");
        }
        $ref = new \ReflectionMethod($controller ,$method[1]);
        $args = [];
        if($ref->getNumberOfParameters() > 0)
        {
            $paramters = $ref->getParameters() ;
            foreach ($paramters as $parameter)
            {
                if(isset($params[$parameter->getName()]))
                {
                    $args [$parameter->getName()] = $params [$parameter->getName()];
                }
                else{
                    if($parameter->isDefaultValueAvailable())
                    {
                        $args [$parameter->getName()] = $parameter->getDefaultValue();
                    }
                    else{
                        $class = $parameter->getClass();
                        if($class != null)
                        {
                            $class = static::getInstance()->make($class);
                            $args[$parameter->getName()] = $class ;
                        }
                        else{
                            throw new RuntimeException("invoke method arguments error:" . $method[0] . "::" . $method[1]);
                        }
                    }
                }
            }
        }
        if(count($args) > 0)
        {
            return $ref->invokeArgs($controller ,$args);
        }
        else{
            return $ref->invoke($controller);
        }
    }

    public function clearInstance()
    {
        static::$instance = null ;
        return null ;
    }
}
