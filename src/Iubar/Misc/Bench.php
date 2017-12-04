<?php
namespace Iubar\Misc;

class Bench {

	private static $array = array(); // start times

	private static $array2 = array(); // stop times

	public static $debug = false;

	public static function startTimer(string $timer_name) {
		$starttime = microtime(true);
		self::$array[$timer_name] = $starttime;
		self::$array2[$timer_name] = 0;
		if (self::$debug) {
			// http://php.net/manual/en/function.debug-backtrace.php
			$backtrace = debug_backtrace();
			$calling_method = $backtrace[1]["function"];
			echo $calling_method . " : has been started at " . $starttime . ' (unix timestamp)' . PHP_EOL;
			echo $calling_method . " : has been started at " . self::timeToString($starttime, new \DateTimeZone('Europe/Rome')) . PHP_EOL;
		}
	}

	public static function stopTimer(string $timer_name, $verbose = false) {
		$endtime = microtime(true);
		self::$array2[$timer_name] = $endtime;
		$str = self::getDiffAsString($timer_name, $endtime);
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
		return self::getDiffAsString($timer_name, $now);
	}
	
	public static function getElapsedTime(string $timer_name) {
		$now = microtime(true);
		return self::getDiff($timer_name, $now);
	}
	

	/**
	 * Returns time elapsed from start to stop
	 */
	public static function getTotalExecutionTimeAsString(string $timer_name) {
		$end = self::$array2[$timer_name];
		return self::getDiffAsString($timer_name, $end);
	}
	
	public static function getTotalExecutionTime(string $timer_name) {
		$end = self::$array2[$timer_name];
		return self::getDiff($timer_name, $end);
	}
	
	private static function getDiffAsString(string $timer_name, $endtime) {
		$str = null;
		$diff = self::getDiff($timer_name, $endtime);
			if (self::$debug) {
				$backtrace = debug_backtrace();
				$calling_method = $backtrace[1]["function"];
				echo "[" . $timer_name . "]" . $calling_method . " elapsed time (unix timestamp): " . $diff . PHP_EOL;
			}
			if($diff!==null){
				$str = self::microtimeToString($diff);
			}
 		return $str;
	}
	
	private static function getDiff(string $timer_name, $endtime) {
		$diff = null;
		$starttime = self::$array[$timer_name];
		if ($starttime) {
			$diff = $endtime - $starttime;
		}
		return $diff;
	}

	/**
	 * Nota che $mtime rappresenta i secondi perchè è valorizzato con la funzione microtime(true)
	 * @see http://php.net/manual/en/function.microtime.php
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 * 
	 * @param unknown $mtime
	 * @param string $format
	 * @return unknown
	 */
	public static function microtimeToString($mtime, $format='Y-m-d H:i:s.u') {
		$str = null;
		//echo "MTIME " . $mtime . PHP_EOL;
// 		if($mtime==0){
// 			$mtime = '0.0';
// 		}
// 		list($sec,$ms) = explode(".", $mtime);
 		$utc = new \DateTimeZone("UTC");
 		
 		// Format explained:
 		// U: Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)	Example: 1292177455
 		// u: Microseconds (up to six digits)	Example: 45, 654321
 		// Note that the U option does not support negative timestamps (before 1970). You have to use date for that.
 		// Note "u" can only parse microseconds up to 6 digits, but some language (like Go) return more than 6 digits for the microseconds, e.g.: "2017-07-25T15:50:42.456430712+02:00" (when turning time.Time to JSON with json.Marshal()). 
 		// Currently there is no other solution than using a separate parsing library to get correct dates. 		 	
		$d = \DateTime::createFromFormat('U.u', number_format($mtime, 6, '.', ''), $utc);
		$d->setTimezone($utc);
		$str = $d->format($format);
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


