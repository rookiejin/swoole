<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 11:56
 */

namespace Rookiejin\Swoole\Helper;


use Traversable;

class Collection implements \ArrayAccess,ArrayAble,\IteratorAggregate
{

    protected $item ;

    public function __construct( array $item = [])
    {
        $this->item = $item ;
    }

    public function make(array $item = [])
    {
        return new static($item);
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return isset($this->item[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if($this->offsetExists($offset)){
            return $this->item [$offset];
        }
        return null ;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        $this->item [$offset] = $value ;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        if($this->offsetExists($offset)){
            unset($this->item[$offset]);
        }
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->item);
    }

    public function __toArray()
    {
        return $this->item ;
    }

}