<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 11:49
 */

namespace Rookiejin\Swoole\Server;


interface ServerEvent
{
    public function onStart($server ,$worker_id);

    public function onConnect($server , $client_id, $from_id);

    public function onReceive($server ,$client_id,$from_id,$data);

    public function onClose($server ,$client_id,$from_id);

    public function onShutdown($server ,$worker_id);

    public function onTask($server ,$task_id,$from_id,$data);

    public function onFinish($server,$task_id,$data);

    public function onTimer($server,$interval);
}