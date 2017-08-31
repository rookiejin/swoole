<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21
 * Time: 10:47
 */

namespace Rookiejin\Swoole\Log\Driver;


use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

class CliLoger extends AbstractLogger
{
    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return mixed
     */
    public function log($level, $message, array $context = [])
    {
        switch ($level)
        {
            case LogLevel::INFO :
            {
                $color = "\033[1;34m [ INFO  ] " . $this->getDate() ;
                break ;
            }
            case LogLevel::ERROR :
            {
                $color = "\033[0;31m [ ERROR ] " . $this->getDate() ;
                break ;
            }
            case LogLevel::DEBUG :
            {
                $color = "\033[1;36m [ DEBUG ] " . $this->getDate() ;
                break ;
            }
            case LogLevel::ALERT :
            {
                $color = "\033[0;34m [ ALERT ] " . $this->getDate() ;
                break;
            }
            case LogLevel::CRITICAL:
            case LogLevel::EMERGENCY:
            case LogLevel::NOTICE:
            {
                $color = "\033[1;33m [ NOTICE ] " . $this->getDate() ;
                break ;
            }
            case LogLevel::WARNING:
            {
                $color = "\033[0;31m [ WARNING ] " . $this->getDate() ;
                break;
            }
            default:
                throw new InvalidArgumentException("log level is not invalid :: {$level}") ;
        }

        $this->output($color ,$message);
    }

    private function getDate()
    {
        return "[" . date("Y-m-d H:i:s" ,time() ) . "]\t";
    }

    private function output( $color , $message )
    {
        if(is_array($message)){
            $string = var_export($message,true);
            echo $color . $string . PHP_EOL ;
        }elseif(is_object($message)){
            if(method_exists($message ,'__toString'))
            {
                $string = $message->__toString();
                echo $color . $string . PHP_EOL ;
            }
            else{
                echo $color . PHP_EOL ;
                var_export($message,true);
            }
        }
        else
        {
            echo $color . $message . PHP_EOL ;
        }

    }
}