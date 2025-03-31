<?php

namespace App\Logger;

use Monolog\Handler\AbstractProcessingHandler;

use League\CLImate\CLImate;
use Monolog\Level;
 

class ClimateConsoleHandler extends AbstractProcessingHandler
{
    private CLImate $climate;

    public function __construct($level = Level::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->climate = new CLImate();
    }

    protected function write(array $record): void
    {
        $message = sprintf("[%s] %s: %s", $record['datetime']->format('Y-m-d H:i:s'), $record['level_name'], $record['message']);

        // Applica colori diversi in base al livello di log
        switch ($record['level']) {
            case Level::DEBUG:
                $this->climate->lightGray()->inline($message . "\n");
                break;
            case Level::INFO:
                $this->climate->cyan()->inline($message . "\n");
                break;
            case Level::NOTICE:
                $this->climate->green()->inline($message . "\n");
                break;
            case Level::WARNING:
                $this->climate->yellow()->inline($message . "\n");
                break;
            case Level::ERROR:
                $this->climate->red()->inline($message . "\n");
                break;
            case Level::CRITICAL:
                $this->climate->backgroundRed()->white()->inline($message . "\n");
                break;
            case Level::ALERT:
                $this->climate->backgroundYellow()->black()->inline($message . "\n");
                break;
            case Level::EMERGENCY:
                $this->climate->backgroundBlack()->lightRed()->inline($message . "\n");
                break;
            default:
                $this->climate->inline($message . "\n");
        }
    }
}