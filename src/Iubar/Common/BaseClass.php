<?php
namespace Iubar\Common;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class BaseClass {
	// 	const EMERGENCY = 'emergency';
	// 	const ALERT     = 'alert';
	// 	const CRITICAL  = 'critical';
	// 	const ERROR     = 'error';
	// 	const WARNING   = 'warning';
	// 	const NOTICE    = 'notice';
	// 	const INFO      = 'info';
	// 	const DEBUG     = 'debug';

    protected ?LoggerInterface $logger = null;

	public function __construct() {
	}

	public function setLogger(LoggerInterface $logger): void {
		$this->logger = $logger;
		$this->log(LogLevel::DEBUG, 'BaseClass: logger inizialized');
	}

	public function getLogger() {
		return $this->logger;
	}

	public function logDebug(string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->log(LogLevel::DEBUG, $message, $context);
		}
	}
	public function logError(string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->log(LogLevel::ERROR, $message, $context);
		}
	}
	public function logCritical(string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->log(LogLevel::CRITICAL, $message, $context);
		}
	}
	public function logInfo(string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->log(LogLevel::INFO, $message, $context);
		}
	}
	public function logWarning(string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->log(LogLevel::WARNING, $message, $context);
		}
	}

	public function log(string $level, string $message, array $context = []): void {
		if ($this->logger != null) {
			$this->logger->log($level, $message, $context);
		}
	}
}
