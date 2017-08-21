<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/14
 * Time: 11:05
 */

namespace Rookiejin\Swoole\Container;


use Psr\Container\ContainerInterface;
use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Exception\InitException;
use Rookiejin\Swoole\Exception\NotFoundException;

class Container implements ContainerInterface, \ArrayAccess
{

    /**
     * @var array
     */
    protected $instance = [];

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var Application
     */
    public static $self = null;

    /**
     * @return Application
     * @throws InitException
     */
    public static function getInstance($id = null)
    {
        if (is_null(static::$self)) {
            throw new InitException('application has not been bootstraped');
        }

        if (is_string($id)) {
            return static::$self->get($id);
        }

        return static::$self;
    }

    /**
     * @param string $id
     * @return mixed|object
     * @throws NotFoundException
     */
    public function get($id)
    {
        if ($this->hasAlias($id)) {
            $id = $this->getAlias($id);
        }
        if ($this->has($id)) {
            return $this->instance [ $id ];
        }
        if (class_exists($id)) {
            return $this->make($id);
        }
        throw new NotFoundException("the {$id} not found in the container");
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return isset($this->instance[ $id ]) ? true : false;
    }


    /**
     * 别名中只保存对象的名字，不保存对象的实例，
     * 如果第二个参数是实例，则绑定到instance上面
     *
     * @param $alias
     * @param $needle mix|object|string
     * @return $this
     */
    public function setAlias($alias, $needle)
    {
        if (is_object($needle)) {
            $needle = get_class($needle);
        }
        if (is_string($needle) && !$this->has($needle)) {
            $this->make($needle);
        }
        $this->alias [ $alias ] = $needle;
    }


    /**
     * @param $alias
     * @return mixed
     */
    public function getAlias($alias)
    {
        if ($this->hasAlias($alias)) {
            return $this->alias [ $alias ];
        }
    }

    /**
     * @param $alias
     * @return bool
     */
    public function hasAlias($alias)
    {
        return isset($this->alias[ $alias ]) ? true : false;
    }


    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|object
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        if (is_string($value) && class_exists($value)) {
            $this->instance [ $offset ] = $this->make($value);
        } elseif (is_object($value)) {
            $this->instance [ $offset ] = $value;
        } elseif ($value instanceof \Closure) {
            $this->instance [ $offset ] = $value();
        }

        return $this;
    }

    public function offsetUnset($offset)
    {
        if ($this->has($offset)) {
            $this->instance [ $offset ] = null;
            unset($this->instance [ $offset ]);
        }

        return $this;
    }

    /**
     * @param $class
     * @return mixed|object $class
     * @throws InitException
     */
    public function make($class, $userParams = [])
    {
        if (is_string($class) && $this->has($class)) {
            return $this->get($class);
        }
        $reflector = new \ReflectionClass($class);
        if (!$reflector->isInstantiable()) {
            throw new InitException("can`t instantiate of class::" . $class);
        }

        $construct = $reflector->getConstructor();

        if (is_null($construct)) {
            $newClass = new $class;
        } else {
            $params = $construct->getParameters();

            $dependencies = $this->getDependencies($params,$userParams);

            $newClass = $reflector->newInstanceArgs($dependencies);
        }
        $this->offsetSet($class, $newClass);

        return $newClass;
    }


    public function getDependencies(array $params,$userParams = [])
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

    public function resoveNonClass(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
    }

    public function __get($key)
    {
        return $this->get($key);
    }
}