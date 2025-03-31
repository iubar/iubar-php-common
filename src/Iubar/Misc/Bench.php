<?php
namespace Iubar\Misc;

class Bench {
    /**
     * start times
     * @var array<string,float>
     */
    private static $array = []; 
 
	/**
	 * stop times
	 * @var array<string,float>
	 */
	private static $array2 = []; // float 

	public static bool $debug = false;

	public static string $def_format = 'H:i:s.u'; // oppure 'Y-m-d H:i:s.u'

	public static function startTimer(string $timer_name) : void{
		$starttime = microtime(true);
		self::$array[$timer_name] = $starttime;
		self::$array2[$timer_name] = 0;
		if (self::$debug) {
			// http://php.net/manual/en/function.debug-backtrace.php
			$backtrace = debug_backtrace();
			$calling_method = $backtrace[1]['function'];
			echo $calling_method . ' : has been started at ' . $starttime . ' (unix timestamp)' . PHP_EOL;
			echo $calling_method .
				' : has been started at ' .
				self::timeToString($starttime, new \DateTimeZone('Europe/Rome')) .
				PHP_EOL;
		}
	}

	public static function stopTimer(string $timer_name, bool $verbose = false) : string {
		$endtime = microtime(true);
		self::$array2[$timer_name] = $endtime;
		$str = self::getDiffAsString($timer_name, $endtime);
		if ($verbose) {
			$str = '[' . $timer_name . ']' . ' elapsed time: ' . $str;
		}
		if (self::$debug) {
			echo $str . PHP_EOL;
		}
		return $str;
	}

	/**
	 * Returns time elapsed from start to now
	 */
	public static function getElapsedTimeAsString(string $timer_name) : ?string {
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
	public static function getTotalExecutionTimeAsString(string $timer_name) : string {
		$end = self::$array2[$timer_name];
		return self::getDiffAsString($timer_name, $end);
	}

	public static function getTotalExecutionTime(string $timer_name) : ?float  {
		$end = self::$array2[$timer_name];
		return self::getDiff($timer_name, $end);
	}

	private static function getDiffAsString(string $timer_name, float $endtime, string $format = '') : ?string {
		$str = null;
		if (!$format) {
			$format = self::$def_format;
		}
		$diff = self::getDiff($timer_name, $endtime);
		if (self::$debug) {
			$backtrace = debug_backtrace();
			$calling_method = $backtrace[1]['function'];
			echo '[' . $timer_name . ']' . $calling_method . ' elapsed time (unix timestamp): ' . $diff . PHP_EOL;
		}
		if ($diff !== null) {
			$str = self::microtimeToString($diff, $format);
		}
		return $str;
	}

	private static function getDiff(string $timer_name, float $endtime) : ?float  {
		$diff = null;
		$starttime = self::$array[$timer_name];
		if ($starttime) {
			$diff = $endtime - $starttime;
		}
		return $diff;
	}

	/**
	 * Nota che $mtime rappresenta i secondi perchè è valorizzato con la funzione microtime(true)
	 *
	 * @see http://php.net/manual/en/function.microtime.php
	 * @see http://php.net/manual/en/datetime.createfromformat.php
	 */
	public static function microtimeToString(float $mtime, string $format) : string {
		if (!$mtime) {
			return '<undefined micro time>';
		}
		$str = null;
		// echo "MTIME " . $mtime . PHP_EOL;
		// if($mtime==0){
		// $mtime = '0.0';
		// }
		// list($sec,$ms) = explode(".", $mtime);
		$utc = new \DateTimeZone('UTC');

		// Format explained:
		// U: Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT) Example: 1292177455
		// u: Microseconds (up to six digits) Example: 45, 654321
		// Note that the U option does not support negative timestamps (before 1970). You have to use date for that.
		// Note "u" can only parse microseconds up to 6 digits, but some language (like Go) return more than 6 digits for the microseconds, e.g.: "2017-07-25T15:50:42.456430712+02:00" (when turning time.Time to JSON with json.Marshal()).
		// Currently there is no other solution than using a separate parsing library to get correct dates.
		$dt = \DateTime::createFromFormat('U.u', number_format($mtime, 6, '.', ''), $utc);
		$dt->setTimezone($utc);
		$str = $dt->format($format);
		return $str;
	}

	/**
	 *
	 *
	 * @see http://php.net/manual/it/function.date.php
	 * @see http://php.net/manual/en/class.datetime.php RFC822 = "D, d M y H:i:s O"
	 *      RFC850 = "l, d-M-y H:i:s T"
	 */
	private static function timeToString(float $unixtime, \DateTimeZone $tz): string {
		if (!$unixtime) {
			return '<undefined time>';
		}
		$dt = \DateTime::createFromFormat('U.u', strval($unixtime));
		$dt->setTimezone($tz);
		return $dt->format(\DateTime::RFC850); // This method does not use locales. All output is in English.
	}

	public static function getStartTimeAsString(string $timer_name, \DateTimeZone $tz = null): string {
		if (!$tz) {
			$tz = new \DateTimeZone('Europe/Rome');
		}
		$unixtime = self::$array[$timer_name];
		return self::timeToString($unixtime, $tz);
	}

	public static function getStopTimeAsString(string $timer_name, \DateTimeZone $tz = null) : string {
	    if (!$tz) {
			$tz = new \DateTimeZone('Europe/Rome');
		}
		$unixtime = self::$array2[$timer_name];
		return self::timeToString($unixtime, $tz);
	}

	public static function getNowAsString(\DateTimeZone $tz = null) : string {
	    if (!$tz) {
			$tz = new \DateTimeZone('Europe/Rome');
		}
		$dt = new \DateTime();
		$dt->setTimezone($tz); // TODO: valutare differenza tra l'invocare $dt->setTimezone(null) e il non invocarlo affatto.
		return $dt->format(\DateTime::RFC850);
	}
}
