<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15
 * Time: 9:54
 */

namespace Rookiejin\Swoole;


use Rookiejin\Swoole\Config\Config;
use Rookiejin\Swoole\Container\Container;
use Rookiejin\Swoole\Exception\InitException;
use Rookiejin\Swoole\Helper\Memory;
use Rookiejin\Swoole\Http\Router;
use Rookiejin\Swoole\Log\Log;
use Rookiejin\Swoole\Server\HttpServer;

class Application extends Container
{
    protected $version = "0.1";

    protected $name = "app";

    /**
     * 程序根目录 .
     *
     * @var null|string
     */
    protected $app_path = null;


    /**
     * @var null app_path / config
     */
    protected $config_path = null;


    /**
     * @var null 存储目录 .
     */
    protected $storage_path = null;


    /**
     * @var null 静态资源目录
     */
    protected $public_path = null;

    /**
     * @var bool is in debug
     */
    public $debug = false;

    /**
     * @var bool app 有没有被初始化
     */
    public $hasBootstrap = false;

    /**
     * @var bool 服务器有没有启动
     */
    public $hasStartedServer = false;

    /**
     * @var HttpServer
     */
    protected $server = null;


    protected $initObject
        = [
            'config' => Config::class,
            'router' => Router::class,
        ];


    const DS = DIRECTORY_SEPARATOR;

    public function __construct($path)
    {
        $path = realpath(rtrim($path, '/\\'));

        if (!is_dir($path)) {
            throw new InitException("{$path} 不是一个有效的目录");
        }
        $this->app_path = $path . DIRECTORY_SEPARATOR;
        self::$self     = &$this;
    }

    public function getPath()
    {
        return $this->app_path;
    }

    public function bootstrap()
    {
        $this->registerPaths();

        $this->registerObject();

        $this->loadConfig();

        $this->initRouter();

        $this->hasBootstrap = true;
    }

    protected function registerPaths()
    {
        if (!is_dir($config_path = $this->app_path . self::DS . 'config' . self::DS)) {
            @mkdir($config_path, 0755, true);
        }

        $this->config_path = $config_path;

        if (!is_dir($public_path = $this->app_path . self::DS . 'public' . self::DS)) {
            @mkdir($public_path, 0755, true);
        }

        $this->public_path = $public_path;

        if (!is_dir($storage_path = $this->app_path . self::DS . 'storage' . self::DS)) {
            @mkdir($storage_path, 0755, true);
        }

        $this->storage_path = $storage_path;
    }


    protected function registerObject()
    {
        foreach ($this->initObject as $key => $val) {
            $this->setAlias($key, $val);
        }
    }

    protected function loadConfig()
    {

        Config::load($this->config_path);

        $this->debug = !is_null(config('app', 'true')) ?: false;

        if ($this->debug) {
            Log::debug("config loaded");
        }
    }

    public function initRouter()
    {
        $router_config = $this->config->router;
        /**
         * @var Router
         */
        $this->router->init($router_config);
        if ($this->debug) {
            Log::debug("router loaded");
        }
    }

    public function run()
    {
        $this->server           = $this->make(HttpServer::class, ['config' => config('server')]);
        $this->hasStartedServer = true;
        $this->server->run();
    }
}
