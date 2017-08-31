<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 18:24
 */


namespace Rookiejin\Swoole\Session\Driver;

use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Helper\Str;
use Rookiejin\Swoole\Session\SessionInterface;
use Swoole\Serialize;

class FileDriver implements SessionInterface
{

    private $session_name ;

    private $path = 'storage/framework/session';

    public function __construct($path = null)
    {
        if(is_null($path))
        {
            $path = $this->path ;
        }

        $path = app_path($path) ;

        if(!is_dir($path))
        {
            if(!mkdir($path ,0755,true))
            {
                throw new RuntimeException("session path::{$path} is not writeable");
            }
        }

        $this->path = realpath($path) . DIRECTORY_SEPARATOR ;
    }
    
    
    /**
     * @return mixed
     */
    public function open()
    {
        // TODO: Implement open() method.
    }

    /**
     * @return mixed
     */
    public function create_sid( $expire )
    {
        while (true)
        {
            $sid =  Str::random(40) ;
            if(!file_exists($this->path . $sid))
            {
                break ;
            }
        }
        swoole_async_writefile($this->path . $sid , Serialize::pack(['expire' => $expire]));
        return $sid ;
    }

    /**
     * @return mixed
     */
    public function destory()
    {
        // TODO: Implement destory() method.
    }

    /**
     * @return mixed
     */
    public function gc()
    {
        // TODO: Implement gc() method.
    }

    /**
     * @return mixed
     */
    public function read()
    {
        // TODO: Implement read() method.
    }

    /**
     * @return mixed
     */
    public function write()
    {
        // TODO: Implement write() method.
    }

    /**
     * @param $sid
     */
    public function check($sid)
    {
        if(file_exists($this->path . $sid))
        {
            return true ;
        }
        return false ;
    }
}