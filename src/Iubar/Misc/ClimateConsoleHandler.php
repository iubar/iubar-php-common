<?php

namespace App\Logger;

use Monolog\Handler\AbstractProcessingHandler;

use League\CLImate\CLImate;
use Psr\Log\LogLevel;
use Monolog\LogRecord;
 
/**
 * 
 * USAGE:
 * $logger = new Logger('console');
 * $logger->pushHandler(new ClimateConsoleHandler(Logger::DEBUG));
 * 
 * // Test vari livelli di log
 * $logger->debug('Questo è un messaggio di debug');
 * $logger->info('Questo è un messaggio informativo');
 * $logger->notice('Questo è un avviso');
 * $logger->warning('Attenzione! Potrebbe esserci un problema');
 * $logger->error('Errore riscontrato nel sistema');
 * $logger->critical('Errore critico! Serve intervento immediato');
 * $logger->alert('Allarme! Situazione di emergenza');
 * $logger->emergency('Sistema in pericolo! Azione richiesta');
 *
 */
class ClimateConsoleHandler extends AbstractProcessingHandler
{
    private CLImate $climate;

    public function __construct(string $level = LogLevel::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->climate = new CLImate();
    }
    
    protected function write(LogRecord $record): void
    {
        $message = sprintf("[%s] %s: %s", $record['datetime']->format('Y-m-d H:i:s'), $record['level_name'], $record['message']);

        // Applica colori diversi in base al livello di log
        switch ($record['level']) {
            case LogLevel::DEBUG:
                $this->climate->lightGray()->inline($message . "\n");
                break;
            case LogLevel::INFO:
                $this->climate->cyan()->inline($message . "\n");
                break;
            case LogLevel::NOTICE:
                $this->climate->green()->inline($message . "\n");
                break;
            case LogLevel::WARNING:
                $this->climate->yellow()->inline($message . "\n");
                break;
            case LogLevel::ERROR:
                $this->climate->red()->inline($message . "\n");
                break;
            case LogLevel::CRITICAL:
                $this->climate->backgroundRed()->white()->inline($message . "\n");
                break;
            case LogLevel::ALERT:
                $this->climate->backgroundYellow()->black()->inline($message . "\n");
                break;
            case LogLevel::EMERGENCY:
                $this->climate->backgroundBlack()->lightRed()->inline($message . "\n");
                break;
            default:
                $this->climate->inline($message . "\n");
        }
    }
}