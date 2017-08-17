<?php

namespace Rookiejin\Swoole\Server ;

interface Server{
    public function run() ;

    public function send($client_id,$data) ;

    public function close($client_id) ;

    public function setProtocol($protocol) ;
}