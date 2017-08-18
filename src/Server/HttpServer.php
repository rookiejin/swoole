<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 11:53
 */

namespace Rookiejin\Swoole\Server;


use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Helper\Dispatcher;
use Rookiejin\Swoole\Helper\Str;
use Swoole\Http\Request;
use Swoole\Http\Response;

class HttpServer implements Server, ServerEvent
{

    protected $process_name = "swooleHttpServer";

    protected $listen = [];

    protected $setting
        = [
            "worker_num" => 4,
            "log_file"   => "storage/log/swoole.log",
            "daemonize"  => true,
        ];

    protected $master_pid_file = null;

    protected $server = null;

    protected $host = "0.0.0.0";

    protected $port = "8888";

    private $socket_type = SWOOLE_SOCK_TCP;

    private $mode = SWOOLE_PROCESS;

    /**
     * HttpServer constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->configure($config);
        $this->initServer();
    }

    protected function configure(array $config)
    {
        if (isset($config['process_name']) && !empty($config ['process_name'])) {
            $this->process_name = $config ['process_name'];
        }

        if (isset($config['listen']) && is_array($config ['listen'])) {
            $this->listen = array_merge($this->listen, $config['listen']);
        }

        if (isset($config['setting'])) {
            $this->setting = array_merge($config['setting']);
        }
        if (isset($config['host']) && !empty($config['host'])) {
            $this->host = $config ['host'];
        }
        if (isset($config['port']) && !empty($config['port'])) {
            $this->port = $config ['port'];
        }
    }

    protected function initServer()
    {
        $this->server = new \Swoole\Http\Server($this->host, $this->port, $this->mode, $this->socket_type);
        $this->server->set($this->setting);
        $this->server->on('Start', [$this, 'onMasterStart']);
        $this->server->on('ManagerStart', [$this, 'onManagerStart']);
        $this->server->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->on('WorkerStop', [$this, 'onWorkerStop']);
        $this->server->on('Request', [$this, 'onRequest']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);
        if (is_array($this->listen)) {
            foreach ($this->listen as $val) {
                if (!$val['host'] || !$val['port']) {
                    continue;
                }
                $this->server->addlistener($val['host'], $val['port'], $this->socket_type);
            }
        }
    }

    public function onMasterStart(\Swoole\Http\Server $server)
    {
        if(is_null($this->master_pid_file)){
            $this->master_pid_file = "/tmp/swoole.{$this->port}.pid";
        }
        try{
            swoole_async_writefile($this->master_pid_file ,$server->master_pid );
        }catch (\Exception $e){
            throw new RuntimeException("the master pid file :: {$this->master_pid_file} is not writeable");
        }
        return true;
    }

    public function onManagerStart()
    {
        return true;
    }

    public function onWorkerStart()
    {
        return true;
    }

    public function onWorkerStop()
    {
        return true;
    }

    public function onRequest(Request $request,Response $response)
    {
        /**
         * @var Response
         */
        $res = Dispatcher::dispatch(\Rookiejin\Swoole\Http\Request::class,'request',$request , $response);
    }
    
    /**
     * @param $setting
     * @return mixed
     */
    public function run()
    {
        $this->server->start();
    }

    /**
     * @param $client_id
     * @param $data
     * @return mixed
     */
    public function send($client_id, $data)
    {
        // TODO: Implement send() method.
    }

    /**
     * @param $client_id
     * @return mixed
     */
    public function close($client_id)
    {
        // TODO: Implement close() method.
    }

    /**
     * @param $protocol
     * @return mixed
     */
    public function setProtocol($protocol)
    {
        // TODO: Implement setProtocol() method.
    }

    /**
     * @param $server
     * @param $worker_id
     * @return mixed
     */
    public function onStart($server, $worker_id)
    {
        // TODO: Implement onStart() method.
    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     * @return mixed
     */
    public function onConnect($server, $client_id, $from_id)
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onReceive($server, $client_id, $from_id, $data)
    {
        // TODO: Implement onReceive() method.
    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     * @return mixed
     */
    public function onClose($server, $client_id, $from_id)
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @param $server
     * @param $worker_id
     * @return mixed
     */
    public function onShutdown($server, $worker_id)
    {
        // TODO: Implement onShutdown() method.
    }

    /**
     * @param $server
     * @param $task_id
     * @param $from_id
     * @param $data
     * @return mixed
     */
    public function onTask($server, $task_id, $from_id, $data)
    {
        // TODO: Implement onTask() method.
    }

    /**
     * @param $server
     * @param $task_id
     * @param $data
     * @return mixed
     */
    public function onFinish($server, $task_id, $data)
    {
        // TODO: Implement onFinish() method.
    }

    /**
     * @param $server
     * @param $interval
     * @return mixed
     */
    public function onTimer($server, $interval)
    {
        // TODO: Implement onTimer() method.
    }


}