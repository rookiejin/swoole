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
use Rookiejin\Swoole\Exception\RuntimeException;
use Rookiejin\Swoole\Http\Router;
use Rookiejin\Swoole\Server\HttpServer;
use Rookiejin\Swoole\Server\Server;

class Application extends Container
{
    protected $version = "0.1";

    protected $name = "app" ;

    protected $app_path = null ;

    protected $config_path = null ;

    protected $debug = false ;

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
    }

    protected function registerPaths()
    {
        $config_path = $this->app_path . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ;
        if(! is_dir($config_path)){
            throw new RuntimeException('config dir is not exists') ;
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
    }

    public function initRouter()
    {
        $router_config = $this->config->get('router');
        /**
         * @var Router
         */
        $this->router->init($router_config);
    }
    

    public function run()
    {
        $this->server = $this->make(HttpServer::class,['config'=>['setting' => ['work_number' => '20']]]);
        $this->server->run();
    }
}