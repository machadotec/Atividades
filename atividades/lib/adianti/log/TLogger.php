<?php
namespace Adianti\Log;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

/**
 * Provides an abstract interface to register LOG files
 *
 * @version    2.0
 * @package    log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TLogger implements LoggerInterface
{
    protected $filename; // path for LOG file
    
    /**
     * Class Constructor
     * @param  $filename path for LOG file
     */
    public function __construct($filename = NULL)
    {
        if ($filename)
        {
            $this->filename = $filename;
            // clear the file contents
            file_put_contents($filename, '');
        }
    }
    
    /**
     * Write abstract method
     * Must be declared in child classes
     */
    abstract function write($message);
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->write("Emergency: $message");
    }
    
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->write("Alert: $message");
    }
    
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->write("Critical: $message");
    }
    
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->write("Error: $message");
    }
    
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->write("Warning: $message");
    }
    
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->write("Notice: $message");
    }
    
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->write("Info: $message");
    }
    
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->write("Debug: $message");
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        switch ($level)
        {
            case LogLevel::EMERGENCY:
                $this->emergency( $message, $context );
                break;
            case LogLevel::ALERT:
                $this->alert( $message, $context );
                break;
            case LogLevel::CRITICAL:
                $this->critical( $message, $context );
                break;
            case LogLevel::ERROR:
                $this->error( $message, $context );
                break;
            case LogLevel::WARNING:
                $this->warning( $message, $context );
                break;
            case LogLevel::NOTICE:
                $this->notice( $message, $context );
                break;
            case LogLevel::INFO:
                $this->info( $message, $context );
                break;
            case LogLevel::DEBUG:
                $this->debug( $message, $context );
                break;
        }
    }
}
