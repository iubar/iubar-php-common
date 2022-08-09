<?php

namespace Iubar\Common;

use Psr\Log\LogLevel;

class LoggingUtil {
	public static function syslogLeveltoPsr($priority) {
		/**
		 LOG_EMERG		system is unusable
		 LOG_ALERT		action must be taken immediately
		 LOG_CRIT		critical conditions
		 LOG_ERR		error conditions
		 LOG_WARNING	warning conditions
		 LOG_NOTICE		normal, but significant, condition
		 LOG_INFO		informational message
		 LOG_DEBUG		debug-level message
		 **/

		$level = null;
		switch ($priority) {
			case LOG_EMERG:
				$level = LogLevel::EMERGENCY;
				break;
			case LOG_ALERT:
				$level = LogLevel::ALERT;
				break;
			case LOG_CRIT:
				$level = LogLevel::CRITICAL;
				break;
			case LOG_ERR:
				$level = LogLevel::ERROR;
				break;
			case LOG_WARNING:
				$level = LogLevel::WARNING;
				break;
			case LOG_NOTICE:
				$level = LogLevel::NOTICE;
				break;
			case LOG_INFO:
				$level = LogLevel::INFO;
				break;
			case LOG_DEBUG:
				$level = LogLevel::DEBUG;
				break;
		}
		return $level;
	}

	public static function psrLeveltoSyslog($level) {
		$priority = null;
		switch ($level) {
			case LogLevel::EMERGENCY:
				$priority = LOG_EMERG;
				break;
			case LogLevel::ALERT:
				$priority = LOG_ALERT;
				break;
			case LogLevel::CRITICAL:
				$priority = LOG_CRIT;
				break;
			case LogLevel::ERROR:
				$priority = LOG_ERR;
				break;
			case LogLevel::WARNING:
				$priority = LOG_WARNING;
				break;
			case LogLevel::NOTICE:
				$priority = LOG_NOTICE;
				break;
			case LogLevel::INFO:
				$priority = LOG_INFO;
				break;
			case LogLevel::DEBUG:
				$priority = LOG_DEBUG;
				break;
		}
		return $priority;
	}

	public static function psrLeveltoString($level) {
		$txt = null;
		switch ($level) {
			case LogLevel::EMERGENCY:
				$txt = 'EMERGENCY';
				break;
			case LogLevel::ALERT:
				$txt = 'ALERT';
				break;
			case LogLevel::CRITICAL:
				$txt = 'CRITICAL';
				break;
			case LogLevel::ERROR:
				$txt = 'ERROR';
				break;
			case LogLevel::WARNING:
				$txt = 'WARNING';
				break;
			case LogLevel::NOTICE:
				$txt = 'NOTICE';
				break;
			case LogLevel::INFO:
				$txt = 'INFO';
				break;
			case LogLevel::DEBUG:
				$txt = 'DEBUG';
				break;
		}
		return $txt;
	}
}
