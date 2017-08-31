<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 11:53
 */

namespace Rookiejin\Swoole\Server;


use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Config\Config;
use Rookiejin\Swoole\Container\Context;
use Rookiejin\Swoole\Exception\HttpException;
use Rookiejin\Swoole\Exception\InitException;
use Rookiejin\Swoole\Exception\MethodNotAllowedException;
use Rookiejin\Swoole\Exception\NotFoundException;
use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Helper\Collection;
use Rookiejin\Swoole\Helper\Dispatcher;
use Rookiejin\Swoole\Helper\Memory;
use Rookiejin\Swoole\Http\HttpNotFoundException;
use Rookiejin\Swoole\Log\Log;
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

    protected function configure(Collection $config)
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
            echo 1;
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

        $this->server->on(SwEvent::START, [$this, 'onMasterStart']);

        $this->server->on(SwEvent::MANAGER_START, [$this, 'onManagerStart']);

        $this->server->on(SwEvent::WORKER_START, [$this, 'onWorkerStart']);

        $this->server->on(SwEvent::CONNECT, [$this, 'onConnect']);

        $this->server->on(SwEvent::RECEIVE, [$this, 'onReceive']);

        $this->server->on(SwEvent::CLOSE, [$this, 'onClose']);

        $this->server->on(SwEvent::WORKER_STOP, [$this, 'onWorkerStop']);

        $this->server->on(SwEvent::REQUEST, [$this, 'onRequest']);

        $this->server->on(SwEvent::TASK, [$this, 'onTask']);

        $this->server->on(SwEvent::FINISH, [$this, 'onFinish']);

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
        if (is_null($this->master_pid_file)) {
            $this->master_pid_file = "/tmp/swoole.{$this->port}.pid";
        }
        if (app()->debug) {
            Log::debug("server master started::pid->\t" . $server->master_pid);
        }
        try {
            swoole_async_writefile($this->master_pid_file, $server->master_pid);
        }
        catch (\Exception $e) {
            throw new InitException("the master pid file :: {$this->master_pid_file} is not writeable");
        }

        return true;
    }

    public function onManagerStart(\Swoole\Server $server)
    {

        if (app()->debug) {
            Log::debug("manager started::pid -> \t" . $server->manager_pid);
        }

        return true;
    }

    public function onWorkerStart(\Swoole\Http\Server $server)
    {
        if (app()->debug) {
            Log::debug("worker started::worker_id ->\t" . $server->worker_id);
        }

        return true;
    }

    public function onWorkerStop(\Swoole\Http\Server $server, $worker_id)
    {
        if (app()->debug) {
            Log::debug("worker stoped::worker_id->\t" . $worker_id);
        }

        return true;
    }

    public function onRequest(Request $request, Response $response)
    {
        if (app()->debug) {
            Log::debug([
                'uri'       => $request->server ['request_uri'],
                'method'    => $request->server ['request_method'],
                'worker_id' => $this->server->worker_id,
            ]);
        }
        try {
            $ctx = Context::getInstance();
            $res = $ctx->request($request, $response);
        }
        catch (RuntimeException $e) {
            $response->status(500);
            $res = $e->getMessage();
        }
        catch (\Exception $e) {
            list($status, $res) = $this->handleException($e);
            $response->status($status);
        } finally {
            if (isset($ctx)) {
                $ctx->clearInstance();
            }
            if (isset($res)) {
                $response->end($res);
            } else {
                $response->end('');
            }
        }

        return;
    }


    public function handleException(\Exception $exception): array
    {
        if ($exception instanceof HttpException) {
            if ($exception instanceof MethodNotAllowedException) {
                return [405, $exception->getMessage()];
            }
            if ($exception instanceof NotFoundException || $exception instanceof HttpNotFoundException) {
                return [404, $exception->getMessage()];
            }
        }
        return [500, $exception->getMessage()];
    }


    /**
     * @param $setting
     * @return mixed
     */
    public function run()
    {
        Log::info("server is running @ {$this->host}::{$this->port}");
        $this->server->start();

    }

    /**
     * @param $client_id
     * @param $data
     * @return mixed
     */
    public function send($client_id, $data)
    {
        if(app()->debug)
        {
            Log::debug("server send data to client::->\t" . $client_id );
        }
    }

    /**
     * @param $client_id
     * @return mixed
     */
    public function close($client_id)
    {
        if(app()->debug)
        {
            Log::debug("server close client::->\t" . $client_id );
        }
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
     */
    public function onStart($server,$worker_id)
    {
        if (app()->debug) {
            Log::debug("server run success");
        }
    }

    /**
     * @param $server
     * @param $client_id
     * @param $from_id
     * @return mixed
     */
    public function onConnect($server, $client_id, $from_id)
    {
        if (app()->debug) {
            Log::debug("client ::@{$client_id} connected");
        }
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
