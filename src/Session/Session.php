<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 17:54
 */

namespace Rookiejin\Swoole\Session;


use Rookiejin\Swoole\Http\Request;

class Session
{
    private $driver = null;

    protected $session_id;

    private $session_name = 'swmvc_session';

    private $enable = true;

    private $expire = 7200 ;

    private $path = '/' ;

    private $secure = false ;

    private $domain = null ;

    private $httponly = true;

    public function __construct(SessionInterface $driver)
    {
        $this->driver = $driver;
    }


    public function init(Request $request, array $config)
    {
        $this->enable = isset($config ['enable']) ?: true;

        if (!$this->enable) {
            return false;
        }

        if (isset($config ['name'])) {
            $this->session_name = $config ['name'];
        }

        if(isset($config['secure']))
        {
            $this->secure = (bool) $config ['secure'];
        }

        if(isset($config['domain']))
        {
            $this->domain = $config ['domain'] ;
        }

        if(isset($config['path']))
        {
            $this->path = $config ['path'] ;
        }

        $sid = $request->cookie($this->session_name, null)  ;

        if(is_null($sid) || !$this->check($sid))
        {
            $sid = $this->generateSessionId();
        }

        $this->session_id = $sid ;

        response()->addCookie($this->session_name, $this->session_id , time() + $this->expire , '/',strval($this->domain),$this->secure,$this->httponly);
    }


    public function getSessionId()
    {
        return $this->session_id;
    }

    public function generateSessionId()
    {
        return $this->driver->create_sid( $this->expire );
    }

    /**
     * 验证session 合法性
     */
    public function check( $sid )
    {
        return $this->driver->check($sid) ;
    }

}