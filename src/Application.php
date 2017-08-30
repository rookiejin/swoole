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

    protected $name = "app" ;

    protected $app_path = null ;

    protected $config_path = null ;

    protected $debug = false ;

    protected $count = [] ;

    /**
     * @var bool app 有没有被初始化
     */
    public $hasBootstrap = false;

    /**
     * @var bool 服务器有没有启动
     */
    public $hasStartedServer = false ;

    /**
     * @var HttpServer
     */
    protected $server = null;

    public function __construct( $path )
    {
        $path = rtrim($path , '/\\');
        $this->app_path = $path . DIRECTORY_SEPARATOR;
        self::$self = & $this ;
    }
    
    
    public function bootstrap()
    {
        $this->registerPaths();

        $this->registerObject();

        $this->loadConfig();

        $this->initRouter();

        $this->hasBootstrap = true ;
    }

    protected function registerPaths()
    {
        $config_path = $this->app_path . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ;
        if(! is_dir($config_path)){
            throw new InitException('config dir is not exists') ;
        }
        $this->config_path = realpath( $config_path ) ;
    }


    protected function registerObject()
    {
        $class = [
            'config' => Config::class ,
            'router' => Router::class ,
        ];
        foreach ($class as $key => $val){
            $this->setAlias($key , $val);
        }
    }


    protected function loadConfig()
    {
        Config::load($this->config_path);
        if($this->get('config')->app ['debug'])
        {
            Log::debug("config loaded");
        }
    }

    public function initRouter()
    {
        $router_config = $this->config->router ;
        /**
         * @var Router
         */
        $this->router->init($router_config);
        if($this->get('config')->app ['debug'])
        {
            Log::debug("router loaded");
        }
    }

    /**
     * 测试共享内存
     */
    public function getCount()
    {
        return $this->count ++ ;
    }

    public function run()
    {
        $this->server = $this->make(HttpServer::class, ['config' => $this->config->server]);
        $this->hasStartedServer = true;
        $this->server->run();
    }
}