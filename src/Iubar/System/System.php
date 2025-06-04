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
 
	 public static function readEnv(string $name): string {
	         $value = '';
	         // $value = getenv($name); // Using getenv() and putenv() is strongly discouraged due to the fact that these functions are not thread safe.
	         if (isset($_ENV[$name])) {
	             $value = self::mixedToString($_ENV[$name]);
	         } else if (isset($_SERVER[$name])) {
	             $value = self::mixedToString($_SERVER[$name]);
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
	     public static function getenvOrEmpty(string $varName): string {
	         // Usa getenv() per ottenere il valore della variabile
	         $value = getenv($varName);
	         // Se la variabile d'ambiente non è settata, restituisci una stringa vuota
	         return $value !== false ? $value : '';
	     }
}
