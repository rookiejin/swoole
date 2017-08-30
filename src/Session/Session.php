<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 17:54
 */

namespace Rookiejin\Swoole\Session;


class Session
{
    private $driver ;


    protected $session_id;


    public function __construct(SessionInterface $driver)
    {
        $this->driver = $driver ;
    }


    public function sessionId()
    {

        if(is_null($this->session_id))
        {
            $this->session_id = $this->driver->create_sid() ;
        }

    }

}