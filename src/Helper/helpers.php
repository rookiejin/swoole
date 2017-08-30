<?php

/**
 * 提供系统快捷函数
 */

function app($id = null)
{
    return \Rookiejin\Swoole\Application::getInstance($id);
}

function config($module, $item = null)
{
    $config = app('config') ;

    if(!is_null($item))
    {
        return isset($config->{$module}[$item]) ?: null ;
    }

    return $config->{$module} ;
}

/**
 * @return \Rookiejin\Swoole\Container\Context
 */
function context()
{
    if(app()->hasStartedServer)
    {
        return \Rookiejin\Swoole\Container\Context::getInstance() ;
    }

    throw new RuntimeException('服务器还没有启动不能调用上下文!') ;
}


/**
 * 返回app的目录
 */
function app_path()
{
    return app()->getPath();
}

/**
 * storage 的 path
 */
function storage_path( $path = null )
{
    if(is_null($path))
    {
        return app_path() . 'storage';
    }
    else{
        $path = ltrim($path , '/\\');
        return storage_path() . $path ;
    }
}


