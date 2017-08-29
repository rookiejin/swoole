<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 17:55
 */

namespace Rookiejin\Swoole\Http;


class Uri
{

    public static $captor = '([\w\.\-\ ]+)';

    /**
     * @param string $uri
     * @return array
     */
    public static function parser( string $uri )
    {

        $labels = [] ;

        $uri = (substr($uri , 0 ,1) === '/') ? $uri : '/' . $uri ;

        $peaces = explode('/' ,$uri) ;

        foreach ($peaces as $key => $value)
        {
            $peaces[$key] = str_replace('*','(.*)',$value);

            if(strpos($value ,':') === 0)
            {
                $peaces [$key] = self::$captor ;
                $labels [] = substr($value,1) ;
                continue ;
            }
            if(strpos($value,'{') === 0)
            {
                $peaces [$key] = self::$captor ;
                $labels [] = substr($value ,1 ,-1);
            }
        }

        if($peaces [count($peaces) -1])
        {
            $peaces [] = '';
        }

        $pattern = str_replace('/' ,'\/' ,implode('/' ,$peaces)) ;

        return [
            'pattern' => $pattern ,
            'labels' => $labels
        ];
    }
    protected $method = null;


    public function setMethod($method)
    {
        $this->method = strtoupper( $method );
    }

    public function getMethod()
    {
        return $this->method ;
    }


    protected $uri = null;

    public function getUri()
    {
        return $this->uri ;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;
    }

    protected $labels = null;

    public function getLabels()
    {
        return $this->labels ;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels ;
    }


    protected $callable = null ;


    public function setCallAble($callable)
    {
        $this->callable = $callable ;
    }

    public function getCallable()
    {
        return $this->callable ;
    }

}