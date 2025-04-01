<?php

namespace Iubar\Misc;

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
class ClimateConsoleHandler extends AbstractProcessingHandler {
	private CLImate $climate;

	public function __construct(string $level = LogLevel::DEBUG, bool $bubble = true) {
		parent::__construct($level, $bubble);
		$this->climate = new CLImate();
	}

	protected function write(LogRecord $record): void {
		$message = sprintf('[%s] %s: %s', $record['datetime']->format('Y-m-d H:i:s'), $record['level_name'], $record['message']);

		// Applica colori diversi in base al livello di log
		switch ($record['level']) {
			case LogLevel::DEBUG:
				$this->climate->lightGray()->inline($message . PHP_EOL);
				break;
			case LogLevel::INFO:
				$this->climate->lightCyan()->inline($message . PHP_EOL);
				break;
			case LogLevel::NOTICE:
				$this->climate->lightGreen()->inline($message . PHP_EOL);
				break;
			case LogLevel::WARNING:
				$this->climate->lightYellow()->inline($message . PHP_EOL);
				break;
			case LogLevel::ERROR:
				$this->climate->lightRed()->inline($message . PHP_EOL);
				break;
			case LogLevel::CRITICAL:
				$this->climate
					->backgroundRed()
					->white()
					->inline($message . PHP_EOL);
				break;
			case LogLevel::ALERT:
				$this->climate
					->backgroundYellow()
					->black()
					->inline($message . PHP_EOL);
				break;
			case LogLevel::EMERGENCY:
				$this->climate
					->backgroundBlack()
					->lightRed()
					->inline($message . PHP_EOL);
				break;
			default:
				$this->climate->inline($message . PHP_EOL);
		}
	}
}
