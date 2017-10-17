<?php
namespace Iubar\Misc;

class Bench {

	private static $array = array();

	private static $array2 = array();

	public static $debug = false;

	public static function startTimer(string $timer_name) {
		$starttime = microtime(true);
		self::$array[$timer_name] = $starttime;
		if (self::$debug) {
			// http://php.net/manual/en/function.debug-backtrace.php
			$backtrace = debug_backtrace();
			$calling_method = $backtrace[1]["function"];
			echo $calling_method . " : has been started at " . $starttime . ' (unix timestamp)' . PHP_EOL;
			echo $calling_method . " : has been started at " . self::timeToString($starttime) . PHP_EOL;
		}
	}

	public static function stopTimer(string $timer_name, $verbose = false) {
		$endtime = microtime(true);
		self::$array2[$timer_name] = $endtime;
		$str = self::getDiff($timer_name, $endtime);
		if($verbose){
			$str = "[" . $timer_name . "]" . " elapsed time: " . $str;
		}
		if (self::$debug) {
			echo $str . PHP_EOL;
		}
		return $str;
	}

	/**
	 * Returns time elapsed from start to now
	 */
	public static function getElapsedTimeAsString(string $timer_name) {
		$now = microtime(true);
		return self::getDiff($timer_name, $now);
	}

	/**
	 * Returns time elapsed from start to stop
	 */
	public static function getTotalExecutionTimeAsString(string $timer_name) {
		$end = self::$array2[$timer_name];
		return self::getDiff($timer_name, $end);
	}

	private static function getDiff(string $timer_name, $endtime) {
		$str = null;
		$starttime = self::$array[$timer_name];
		if ($starttime) {
			$diff = $endtime - $starttime;
			if (self::$debug) {
				$backtrace = debug_backtrace();
				$calling_method = $backtrace[1]["function"];
				echo "[" . $timer_name . "]" . $calling_method . " elapsed time (unix timestamp): " . $diff . PHP_EOL;
			}
			$str = self::microtimeToString($diff);
		}
		return $str;
	}

	private static function microtimeToString($mtime) {
		list ($sec, $usec) = explode('.', $mtime);
		$usec_rounded = round("0." . $usec, 4); // aggiungo lo zero per poter poi arrotondare il numero, altrimenti non potrei arrotondare un numero del tipo "0001"
		$usec_formatted_wo_zero = str_replace("0.", "", $usec_rounded);
		$str = date('H:i:s', $sec) . ',' . $usec_formatted_wo_zero;
		return $str;
	}

/**
 * 
 * @param unknown $unixtime
 * @return unknown
 * 
 * @see http://php.net/manual/it/function.date.php
 * @see http://php.net/manual/en/class.datetime.php  
 * RFC822 = "D, d M y H:i:s O"
 * RFC850 = "l, d-M-y H:i:s T"
 * 
 */
	private static function timeToString($unixtime, \DateTimeZone $tz) {
		$date = \DateTime::createFromFormat('U.u', $unixtime);
		$date->setTimezone($tz);
		return $date->format(\DateTime::RFC850); // This method does not use locales. All output is in English.
	}

	public static function getStartTimeAsString(string $timer_name, \DateTimeZone $tz=null) {
		if($tz==null){
			$tz= new \DateTimeZone('Europe/Rome');
		}
		$unixtime = self::$array[$timer_name];
		return self::timeToString($unixtime, $tz);
	}

	public static function getStopTimeAsString(string $timer_name, \DateTimeZone $tz=null) {
		if($tz==null){
			$tz= new \DateTimeZone('Europe/Rome');
		}
		$unixtime = self::$array2[$timer_name];
		return self::timeToString($unixtime, $tz);
	}
	
}


