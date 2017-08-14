<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/12
 * Time: 13:48
 */

namespace Rookiejin\Swoole\Server;


class Server
{

    private $server ;


    public function __construct()
    {
        echo "server constructed";
        $this->init() ;
    }

    private function init(){
        $this->server = new \Swoole\Server("0.0.0.0",8888);
    }

    public function run()
    {
        $this->server->on('connect',[$this,'connect']);
        $this->server->on('receive',[$this,'receive']);
        $this->server->on('close',[$this,'close']);
        $this->server->start();
    }

    public function connect($server , $fd)
    {
        
    }

    public function receive($server, $fd, $from_id ,$data)
    {
        
    }

    public function close($server , $fd)
    {
        
    }

}