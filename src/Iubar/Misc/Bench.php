<?php

namespace Iubar\Misc;

class Bench {

private static $array = array();
private static $array2 = array();

public static $debug = false;

public static function startTimer(string $timer_name){
	$starttime = microtime(true);
	self::$array[$timer_name] = $starttime;
	if(self::$debug){
		// http://php.net/manual/en/function.debug-backtrace.php
		$backtrace = debug_backtrace();
		$calling_method = $backtrace[1]["function"];
		echo $calling_method . " : start (unix timestamp): " . $starttime . PHP_EOL;
		self::printUnixTime($starttime);
	}
}

public static function stopTimer(string $timer_name){
	$end = microtime(true);
	self::$array2[$timer_name] = $end;
	return self::getDiff($timer_name, $endtime);
}

/**
 * Returns time elapsed from start to now
 */
public static function getElapsedTimeAsString($timer_name) {
	$now = microtime(true);
	return self::getDiff($timer_name, $now);
}

/**
 * Returns time elapsed from start to stop
 */
public static function getTotalExecutionTimeAsString(string $timer_name) {
	$end = self::$array2[$timer_name];
	return self::getDiff($timer_name, $now);
}

private static function getDiff(string $timer_name, $endtime){
	$str = null;
	$starttime = self::$array[$timer_name];
	if($starttime){
		$diff = $endtime - $starttime;
		if(self::$debug){
			$backtrace= debug_backtrace();
			$calling_method = $backtrace[1]["function"];
			echo "[" . $timer_name . "]" . $calling_method . " diff (unix timestamp): " . $diff . PHP_EOL;
		}
		$str = self::microtimeToString($diff);
		if(self::$debug){
			echo "[" . $timer_name . "]" . " elapsed time: " . $str . PHP_EOL;
		}
	}
	return $str;
}

private static function microtimeToString($mtime){
	list($sec, $usec) = explode('.', $mtime);
	$usec_rounded = round("0." . $usec, 4); // aggiungo lo zero per poter poi arrotondare il numero, altrimenti non potrei arrotondare un numero del tipo "0001"
	$usec_formatted_wo_zero = str_replace("0.", "", $usec_rounded);
	$str = date('H:i:s', $sec) . ',' . $usec_formatted_wo_zero;
	return $str;
}

private static function timeToString($unixtime){
	$date = DateTime::createFromFormat('U.u', $unixtime);
	return $date->format("m-d-Y H:i:s");
}

private static function printUnixTime($unixtime){
	echo "Time: " . self::timeToString($unixtime) . PHP_EOL;
}

public static function getStartTimeAsString(string $timer_name) {
	$unixtime = self::$array[$timer_name];
	return self::timeToString($unixtime);
}

public static function getStopTimeAsString(string $timer_name) {
	$unixtime = self::$array2[$timer_name];
	return self::timeToString($unixtime);
}


}
