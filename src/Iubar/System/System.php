<?php

namespace Iubar\System;

class System {
	public static function isWindows() {
		$svr_os = strtolower(explode(' ', php_uname('s'))[0]);
		return $svr_os === 'windows';
	}
	public static function isMac() {
		$svr_os = strtolower(explode(' ', php_uname('s'))[0]);
		return $svr_os === 'mac';
	}
	public static function isLinux() {
		$svr_os = strtolower(explode(' ', php_uname('s'))[0]);
		return $svr_os === 'linux';
	}

	/**
	 * Using getenv() and putenv() is strongly discouraged due to the fact that these functions are not thread safe.
	 * 
	 * @param string $name
	 * @return string
	 */
	public static function readEnv(string $varName): string {
		$value = '';
		if (isset($_ENV[$varName])) {
		    $value = self::mixedToString($_ENV[$varName]);
		} elseif (isset($_SERVER[$varName])) {
		    $value = self::mixedToString($_SERVER[$varName]);
		}else{
		    $value = self::getEnvOrEmpty($varName);
		}
		return $value;
	}

	public static function mixedToString(mixed $value): string {
		if (is_string($value)) {
			return $value;
		}
		if (is_null($value)) {
			return '';
		}
		if (is_bool($value)) {
			return $value ? '1' : '';
		}
		if (is_int($value) || is_float($value)) {
			return (string) $value;
		}
		if (is_object($value) && method_exists($value, '__toString')) {
			return (string) $value;
		}
		if (is_array($value)) {
			// Se vuoi puoi serializzare o json_encode
			$encoded = json_encode($value);
			return $encoded !== false ? $encoded : '';
		}

		// Per sicurezza, fallback
		return '';
	}

	/**
	 * Il metodo permette di superare il type check di phpstan
	 */
	public static function getEnvOrEmpty(string $varName): string {
		// Usa getenv() per ottenere il valore della variabile
		$value = getenv($varName);
		// Se la variabile d'ambiente non è settata, restituisci una stringa vuota
		return $value !== false ? $value : '';
	}
}
