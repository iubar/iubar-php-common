<?php


require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel ;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

class OoDemo {
    
    private LoggerInterface $logger;
    
    function __construct() {
            $this->logger = new Logger('application');        
        echo 'class is  : ' . get_class($this->logger) . PHP_EOL;                
        if($this->logger instanceof Monolog\Logger){                          
                $level = LogLevel::WARNING;
                $handler = new StreamHandler('php://stdout', $level);
                //$handler = new ConsoleHandler();                
                //$formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
                $formatter = new ColoredLineFormatter();
                $handler->setFormatter($formatter);
                $this->logger->pushHandler($handler);
                $this->logger->log(LogLevel::WARNING, 'Hello');
                $this->logger->log(LogLevel::INFO, 'World!');
                $this->logger->log(LogLevel::CRITICAL, '!');
        }else{    
            echo 'Error ' . PHP_EOL;
           exit(1);        
        }
 
    }  

}




$test = new OoDemo();
        
    
 