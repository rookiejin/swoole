<?php

/**
 * 提供系统快捷函数
 */

function app($id = null)
{
    return \Rookiejin\Swoole\Application::getInstance($id);
}

function config( $id )
{
    $config = app('config');

    return $config->get($id);
}

/**
 * @return \Rookiejin\Swoole\Container\Context
 */
function context()
{
    if (app()->hasStartedServer) {
        return \Rookiejin\Swoole\Container\Context::getInstance();
    }

    throw new RuntimeException('服务器还没有启动不能调用上下文!');
}


/**
 * 返回app的目录
 */
function app_path( $id )
{
    if(is_null($id))
    {
        return app()->getPath();
    }
    return app()->getPath() . $id ;
}

/**
 * storage 的 path
 */
function storage_path($path = null)
{
    if (is_null($path)) {
        return app_path() . 'storage';
    } else {
        $path = ltrim($path, '/\\');

        return storage_path() . $path;
    }
}


//-- 网络请求相关的助手函数 -- //

if (!function_exists('request')) {

    /**
     * 获取请求数据
     *
     * @return \Rookiejin\Swoole\Http\Request
     */
    function request()
    {
        return context()->getRequest();
    }
}

if (!function_exists('response')) {
    /**
     * 获取响应数据
     *
     * @return \Rookiejin\Swoole\Http\Response
     */
    function response()
    {
        return context()->getResponse();
    }
}

if (!function_exists('cookie')) {
    function cookie()
    {
        return context()->getRequest()->getCookies();
    }
}


if (!function_exists('request_get')) {
    /**
     * @param      $key
     * @param null $default 默认值
     * @param bool $filter  是否过滤
     * @return mixed
     */
    function request_get($key, $default = null, $filter = true)
    {
        return context()->getRequest()->get($key, $default, $filter);
    }
}


if (!function_exists('request_post')) {
    /**
     * @param      $key
     * @param null $default
     * @param bool $filter
     * @return mixed
     */
    function request_post($key, $default = null, $filter = true)
    {
        return context()->getRequest()->post($key ,$default ,$filter);
    }
}


if(! function_exists('request_all'))
{
    function request_all()
    {
        return context()->getRequest()->all();
    }
}


if(! function_exists('request_file'))
{
    /**
     * 获取文件上传
     * @param null $key
     * @return mixed|null
     */
    function request_file($key = null)
    {
        return context()->getRequest()->file($key);
    }
}


























