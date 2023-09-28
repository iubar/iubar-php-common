<?php

namespace Iubar\Misc;

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class MiscUtils {
	private static $useConEmuOnWin = true;

	/**
	 * Logs to syslog service.
	 *
	 * usage example:
	 *
	 *   $log = new Logger('application');
	 *   $syslog = new SyslogHandler('myfacility', 'local6');
	 *   $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
	 *   $syslog->setFormatter($formatter);
	 *   $log->pushHandler($syslog);
	 *
	 */

	public static function rotatingLoggerFactoryloggerFactory(
	    string $logger_name,
	    string $log_level,
	    string $log_file = 'log.rot',
		bool $log_to_shell = true
	) {
		$error = '';
 		$log_path = '';
		$logger = new Logger($logger_name); // create a log channel
		if ($log_file) {
			$log_path = dirname($log_file);
			$error = self::checkLogPath($log_path);
			if ($error) {
				// Impossibile scrivere nel percorso $log_path
				die('QUIT: ' . $error . PHP_EOL);
			} else {
				$rotating_handler = new \Monolog\Handler\RotatingFileHandler($log_file, 3, $log_level);
				$rotating_handler->setFormatter(new LineFormatter(null, null, true, true)); // LineFormatter::__construct(string $format = null, string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
				$logger->pushHandler($rotating_handler);
			}
		}
		if ($log_to_shell) {
			self::logToShell($logger, $log_level);
		}
		self::checkError($logger, $error, $log_path, $log_to_shell);
		return $logger;
	}

	public static function loggerFactory(
		string $logger_name,
		string $log_level,
		string $log_file = '',
		bool $overwrite_log = true,
		bool $log_to_shell = true
	) {
		$error = '';
		$logger = new Logger($logger_name); // create a log channel
		if ($log_file) {
			$log_path = dirname($log_file);
			$error = self::checkLogPath($log_path);
			// $error = self::checkLogFile($log_file);
			if ($error) {
				// Impossibile scrivere nel percorso $log_path
				die('QUIT: ' . $error . PHP_EOL);
			} else {
				$handler = new StreamHandler($log_file, $log_level);
				$handler->setFormatter(new LineFormatter(null, null, true, true)); // LineFormatter::__construct(string $format = null, string $dateFormat = null, bool $allowInlineLineBreaks = false, bool $ignoreEmptyContextAndExtra = false)
				$logger->pushHandler($handler);
				if ($overwrite_log) {
					file_put_contents($log_file, '');
					$logger->info('Log file cleared');
				}
			}
		} else {
			// log to file is disabled
		}

		if ($log_to_shell) {
			self::logToShell($logger, $log_level);
		}
		self::checkError($logger, $error, $log_file, $log_to_shell);
		return $logger;
	}

	private static function checkLogFile(string $log_file) {
		$error = '';
		// echo "Log file is '" . $log_file . "'" . PHP_EOL;
		if (file_exists($log_file)) {
			if (!is_writable($log_file)) {
				$error = "Permission denied writing to file '" . $log_file . "'";
			}
		} else {
			$error = self::checkLogPath(dirname($log_file));
		}
		return $error;
	}

	private static function checkLogPath(string $log_path) : string {
		$error = '';
		echo "Log path is '" . $log_path . "'" . PHP_EOL;
		if (!is_readable($log_path)) {
			$error = "Log path '" . $log_path . "' not found";
		} elseif (!is_writable($log_path)) {
			$error = "Permission denied can't write to path '" . $log_path . "'";
		}
		return $error;
	}

	private static function checkError(Logger $logger, string $error, string $log_file_or_path, bool $log_to_shell) : void {
		$NO_HANDLER = 'All log handlers are disabled: you should choose between screen or file logging';
		if ($error) {
		    if ($log_to_shell || $log_file_or_path) {
				$logger->error($error);
			} else {
			    die('ERROR 1 : ' . $NO_HANDLER . PHP_EOL);
			}
		} elseif (!$log_file_or_path) {
			if ($log_to_shell) {
				$logger->notice('Log to file is disabled');
				$msg = 'All log messages will be show only on screen';
				$logger->notice($msg);
			} else {
			    die('ERROR 2 : ' . $NO_HANDLER . PHP_EOL);
			}
		}
	}

	/**
	 * 
	 * @param Logger $logger non posso usare LoggerInterface perchè di seguito uso il metodo pushHandler()
	 * @param string $log_level
	 */
	public static function logToShell(Logger $logger, string $log_level) {
		$handler = new StreamHandler('php://stdout', $log_level);
		// const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
		$format = '%channel%.%level_name%: %message% %context% %extra%' . PHP_EOL;
		$colorScheme = null;
		// $dateFormat = 'Y-m-d H:i:s';
		$dateFormat = null;
		$allowInlineLineBreaks = true;
		$ignoreEmptyContextAndExtra = true;
		if (self::$useConEmuOnWin || !self::isWindows()) {
			// $colorScheme = new TrafficLight();
			$handler->setFormatter(
				new ColoredLineFormatter(
					$colorScheme,
					$format,
					$dateFormat,
					$allowInlineLineBreaks,
					$ignoreEmptyContextAndExtra
				)
			);
		} else {
			$formatter = new LineFormatter($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
			$handler->setFormatter($formatter);
		}
		$logger->pushHandler($handler);
	}

	////////////////////////////////

	public static function getWorkspacePath() {
		$workspace = '';
		echo 'I have been run on ' . php_uname('s') . PHP_EOL;
		$user = get_current_user();
		$svr_os = strtolower(explode(' ', php_uname('s'))[0]);
		$isWindows = $svr_os === 'windows';
		$isLinux = $svr_os === 'linux';
		if ($isWindows) {
			$user_home = 'C:/Users' . '/' . $user;
			$workspace = $user_home . '/' . 'workspace_php';
		} elseif ($isLinux) {
			die('Quit: Linux system detected.' . PHP_EOL);
		} else {
			die('Quit: Unknown system detected.' . PHP_EOL);
		}
		return $workspace;
	}

	public static function isWindows() {
		$b = false;
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$b = true;
		}
		return $b;
	}

	// FUNCTIONS
	// (da utilizzare quando non si può usare il trucco "2>&1")

	public static function runCommand($bin, $command = '', $force = true) {
		$stream = null;
		$bin .= $force ? ' 2>&1' : '';

		$descriptorSpec = [
			0 => ['pipe', 'r'],
			1 => ['pipe', 'w']
		];

		$process = proc_open($bin, $descriptorSpec, $pipes);

		if (is_resource($process)) {
			fwrite($pipes[0], $command);
			fclose($pipes[0]);

			$stream = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			proc_close($process);
		}

		return $stream;
	}

	public static function file_get_contents_utf8(string $fn) {
		$content = file_get_contents($fn);
		return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	}
 
} // end class
