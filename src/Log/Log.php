<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 10:44
 */

namespace Rookiejin\Swoole\Log;


use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Rookiejin\Swoole\Application;
use Rookiejin\Swoole\Config\Config;
use Rookiejin\Swoole\Exception\NotFoundException;

class Log implements LoggerAwareInterface
{
    /**
     * 驱动
     *
     * @var string
     */
    public $defaultLogger = "cli";


    private $logger;

    public function __construct(Config $config)
    {
        if (isset($config->app['log']['driver'])) {
            $logger = $config->app['log']['driver'];
        } else {
            $logger = $this->defaultLogger;
        }

        $class = "Rookiejin\\Swoole\\Log\\Driver\\" . ucfirst($logger) . "Loger";
        if (class_exists($class)) {
            $this->setLogger(Application::getInstance()->make($class));
        } else {
            throw new NotFoundException("class {$class} not exists");
        }
    }


    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public static function __callStatic($method, $params)
    {
        /**
         * @var Log
         */
        $log = Application::getInstance()->make(Log::class);
        if (method_exists($log->getLogger(), $method) && count($params) >= 1) {
            $log->getLogger()->{$method}($params[0], isset($params[1]) ?: []);
            return;
        }
        else {
            throw new InvalidArgumentException("Argument wrong or log method not exists::" . $method);
        }
    }
}