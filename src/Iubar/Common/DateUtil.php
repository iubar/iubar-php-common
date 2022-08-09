<?php

namespace Iubar\Common;

class DateUtil {
	// --------------------------------------------------------
	// VERSIONE 0.2 (25/12/2010)
	// --------------------------------------------------------

	// REF: http://www.php.net/manual/en/public static function.date.php

	//setlocale(LC_ALL,"ita"); // Ok on Windows
	//setlocale(LC_ALL,"it_IT", "it", "ita"); // For every platfor
	//echo (strftime("It is %a on %b %d, %Y, %X time zone: %Z",time()));
	//echo (strftime("Oggi è %A %d %B, %Y, %X tz: %z",time()));

	public static $def_date_format = 'd/m/Y';
	public static $mysql_date_format = 'Y-m-d H:i:s'; // eg: $dateTime->format(DateUtil::$mysql_date_format);

	/*
%a - abbreviated weekday name according to the current locale
%A - full weekday name according to the current locale
%b - abbreviated month name according to the current locale
%B - full month name according to the current locale
%c - preferred date and time representation for the current locale
%C - century number (the year divided by 100 and truncated to an integer, range 00 to 99)
%d - day of the month as a decimal number (range 01 to 31)
%D - same as %m/%d/%y
%e - day of the month as a decimal number, a single digit is preceded by a space (range ' 1' to '31')
%g - like %G, but without the century.
%G - The 4-digit year corresponding to the ISO week number (see %V). This has the same format and value as %Y, except that if the ISO week number belongs to the previous or next year, that year is used instead.
%h - same as %b
%H - hour as a decimal number using a 24-hour clock (range 00 to 23)
%I - hour as a decimal number using a 12-hour clock (range 01 to 12)
%j - day of the year as a decimal number (range 001 to 366)
%m - month as a decimal number (range 01 to 12)
%M - minute as a decimal number
%n - newline character
%p - either `am' or `pm' according to the given time value, or the corresponding strings for the current locale
%r - time in a.m. and p.m. notation
%R - time in 24 hour notation
%S - second as a decimal number
%t - tab character
%T - current time, equal to %H:%M:%S
%u - weekday as a decimal number [1,7], with 1 representing Monday
%U - week number of the current year as a decimal number, starting with the first Sunday as the first day of the first week
%V - The ISO 8601:1988 week number of the current year as a decimal number, range 01 to 53, where week 1 is the first week that has at least 4 days in the current year, and with Monday as the first day of the week. (Use %G or %g for the year component that corresponds to the week number for the specified timestamp.)
%W - week number of the current year as a decimal number, starting with the first Monday as the first day of the first week
%w - day of the week as a decimal, Sunday being 0
%x - preferred date representation for the current locale without the time
%X - preferred time representation for the current locale without the date
%y - year as a decimal number without a century (range 00 to 99)
%Y - year as a decimal number including the century
%Z or %z - time zone offset or name or abbreviation (Operating System dependent)
%% - a literal `%' character
*/

	// $dateTime = new DateTime("now", new DateTimeZone('Europe/Rome'));
	// $mysqldate = $dateTime->format("Y-m-d H:i:s");
	// echo $mysqldate;

	public static function getMysqlTodayString($type = 'DATE') {
		$date = null;
		$time = time();
		if ($type == 'DATE') {
			$date = date('Y-m-d', $time);
		} elseif ($type == 'DATETIME') {
			$date = date('Y-m-d H:i:s', $time);
		} else {
			throw new \InvalidArgumentException('getMysqlTodayString(), parametro errato: ' . $type);
		}
		return $date;
	}

	public static function mysql_date_to_string($date_field) {
		$str = '';
		if ($date_field != '') {
			$str = date('d-m-Y H:i:s', strtotime($date_field));
		}
		return $str;
	}

	public static function amazingStringFromTime($str, $nTimestamp = null) {
		// This public static function reads a human readable string representation of dates. e.g.
		// DD MM YYYY => 01 07 1978
		// DDD D MMM YY => Mon 1 Jul 78

		$arrPairs = [
			'DDDD' => '%A',
			'DDD' => '%a',
			'DD' => '%d',
			'D' => '%e', // has leading space: ' 1', ' 2', etc for single digit days
			'MMMM' => '%B',
			'MMM' => '%b',
			'MM' => '%m',
			'YYYY' => '%Y',
			'YY' => '%y',
			'HH' => '%H',
			'hh' => '%I',
			'mm' => '%M',
			'ss' => '%S'
		];

		$str = str_replace(array_keys($arrPairs), array_values($arrPairs), $str);
		return strftime($str, $nTimestamp);
	}

	public static function strange2min($strange) {
		$h24h = substr($strange, -2);
		[$hour, $minute] = preg_split('/(?i:am|pm|:)/', $strange);
		if ($h24h == 'am' && $hour == 12) {
			$hour = 0;
		} elseif ($h24h == 'pm') {
			$hour = $hour + 12;
		}
		return $hour * 60 + $minute;
	}

	public static function checkinzone($start, $end) {
		// USAGE
		// $start = "1:30am";
		// $end = "9:39am";
		// echo '<strong>', var_dump(checkinzone($start,$end)),'</strong>';

		$now = date('G') * 60 + date('i');
		$start = self::strange2min($start);
		$end = self::strange2min($end);
		return $start <= $now && $now <= $end;
	}

	/**
	 * Finds the difference in days between two calendar dates.
	 */
	public static function dateDiff($startDate, $endDate) {
		// Parse dates for conversion
		$startArry = date_parse($startDate);
		$endArry = date_parse($endDate);

		// Convert dates to Julian Days
		$start_date = gregoriantojd($startArry['month'], $startArry['day'], $startArry['year']);
		$end_date = gregoriantojd($endArry['month'], $endArry['day'], $endArry['year']);

		// Return difference
		return round($end_date - $start_date, 0);
	}

	public static function getTime() {
		return date('h:i:s A');
	}

	public static function getFullTime() {
		$str = date('h:i:s A') . ' of ' . date('d-m-Y');
		return $str;
	}

	/**
	 * check a date in the Italian format
	 */
	public static function checkData($date) {
		if (!isset($date) || $date == '') {
			return false;
		}

		[$dd, $mm, $yy] = explode('/', $date);
		if ($dd != '' && $mm != '' && $yy != '') {
			return checkdate($mm, $dd, $yy);
		}

		return false;
	}

	public static function isValidDateTime($dateTime) {
		if (
			preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)
		) {
			if (checkdate($matches[2], $matches[3], $matches[1])) {
				return true;
			}
		}

		return false;
	}

	public static function isMoorning() {
		$h = date('H');
		$b = false;
		if ($h < 13) {
			$b = true;
		}
		return $b;
	}

	public static function getElapsedDays($from, $to) {
		// Attenzione: non tiene conto del passaggio ora legale/solare
		$diff = $to - $from;
		return round($diff / 86400);
	}

	public static function getElapsedDays2($from, $to) {
		// Attenzione: non tiene conto del passaggio ora legale/solare
		// The public static function expects to be given a string containing an English date format
		$start_ts = strtotime($from);
		$end_ts = strtotime($to);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
	}

	public static function getYesterday() {
		return mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
	}

	public static function getToday() {
		// USAGE: date ("d-M-Y", DateUtil::getToday());
		return mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	}

	public static function dateToString($date) {
		$str = strftime('%d/%m/%Y', $date);
		return $str;
	}

	public static function dateToString2($date) {
		return strftime('%d %B %Y', $date);
	}

	// Get the last day of the month
	public static function lastDayOfMonth($month = '', $year = '') {
		if (empty($month)) {
			$month = date('m');
		}
		if (empty($year)) {
			$year = date('Y');
		}
		$result = strtotime("{$year}-{$month}-01");
		$result = strtotime('-1 second', strtotime('+1 month', $result));
		return date('d/m/Y', $result);
	}

	public static function stringToTime($str) {
		// 	// Nota che la funzione strtotime presenta delle ambiguità che possono essere
		// 	// superate solo utilizzando un particolare separatore
		// 	echo date("jS F, Y", strtotime("11.12.10"));
		// 	// outputs 10th December, 2011
		// 	echo date("jS F, Y", strtotime("11/12/10"));
		// 	// outputs 12th November, 2010
		// 	echo date("jS F, Y", strtotime("11-12-10"));
		// 	// outputs 11th December, 2010
		return strtotime($str);
	}

	public static function stringToTime2($str) {
		// il metodo funziona solo per date espresse nel formato gg/mm/aaaa o gg/mm/aa
		$d = null;

		if ($str != '') {
			$seps = ['/', '-', '.'];
			$separator = $seps[0];
			$found = false;
			foreach ($seps as $sep) {
				$pos = strpos($str, $sep);
				if ($pos !== false) {
					$separator = $sep;
					$found = true;
					break;
				}
			}

			if ($found) {
				$array = explode($separator, $str);

				if (sizeof($array) == 3) {
					$day = $array[0];
					$month = $array[1];
					$year = $array[2];

					//echo "day " . $day . "\r\n";
					//echo "month " . $month . "\r\n";
					//echo "year " . $year . "\r\n";

					$d = mktime(0, 0, 0, $month, $day, $year);
				}
			}
		}
		return $d;
	}

	public static function secondsToHours($s) {
		$h = $s / 3600;
		return $h;
	}

	public static function isDayInTheFuture($date) {
		$b = false;

		$day = strftime('%d', $date);
		$month = strftime('%m', $date);
		$year = strftime('%Y', $date);

		$today = self::getToday();

		$day2 = strftime('%d', $today);
		$month2 = strftime('%m', $today);
		$year2 = strftime('%Y', $today);

		if ($day > $day2 || $month > $month2 || $year > $year2) {
			$b = true;
		}

		return $b;
	}

	public static function isToday($date) {
		$b = false;

		$day = strftime('%d', $date);
		$month = strftime('%m', $date);
		$year = strftime('%Y', $date);

		$today = self::getToday();

		$day2 = strftime('%d', $today);
		$month2 = strftime('%m', $today);
		$year2 = strftime('%Y', $today);

		if ($day == $day2 && $month == $month2 && $year == $year2) {
			$b = true;
		}

		return $b;
	}

	public static function isYesterday($date) {
		$b = false;

		$day = strftime('%d', $date);
		$month = strftime('%m', $date);
		$year = strftime('%Y', $date);

		$yesterday = self::getYesterday();

		$day2 = strftime('%d', $yesterday);
		$month2 = strftime('%m', $yesterday);
		$year2 = strftime('%Y', $yesterday);

		if ($day == $day2 && $month == $month2 && $year == $year2) {
			$b = true;
		}

		return $b;
	}

	//////////////////////////////////////////////////// TEST

	public static function test_diff_2() {
		// Il risultato esatto è 2 non 3 perchè vi è passaggio a ora legale
		$s = 0;
		$m = 0;
		$h1 = 4;
		$h2 = 1;
		$day = '28';
		$month = '3';
		$year = '2010';
		$d1 = mktime($h1, $m, $s, $month, $day, $year);
		$d2 = mktime($h2, $m, $s, $month, $day, $year);
		$diff1 = $d1 - $d2;

		date_default_timezone_set('Brazil/Acre');
		$d1 = mktime($h1, $m, $s, $month, $day, $year);
		$d2 = mktime($h2, $m, $s, $month, $day, $year);
		$diff2 = $d1 - $d2;

		date_default_timezone_set('America/New_York');
		$d1 = mktime($h1, $m, $s, $month, $day, $year);
		$d2 = mktime($h2, $m, $s, $month, $day, $year);
		$diff3 = $d1 - $d2;

		echo 'diff1: ' . self::secondsToHours($diff1) . ' h.' . PHP_EOL;
		echo 'diff2: ' . self::secondsToHours($diff2) . ' h.' . PHP_EOL;
		echo 'diff3: ' . self::secondsToHours($diff3) . ' h.' . PHP_EOL;
	}

	public static function test_diff() {
		// ATTENZIONE: Qui la timezone è probabilmente (!) uguale a GMT+1
		$hours_diff = strtotime('20:00:00') - strtotime('19:00:00');
		echo date('h:i', $hours_diff) . ' Hours' . PHP_EOL;
		// it shows: 02:00 Hours
		// but if you use a default UTC time:
		date_default_timezone_set('UTC');
		$hours_diff = strtotime('20:00:00') - strtotime('19:00:00');
		echo date('h:i', $hours_diff) . ' Hours' . PHP_EOL;
	}

	public static function test_tz() {
		$tz = new \DateTimeZone('Europe/Rome');
		print_r($tz->getLocation());
	}

	public static function test_tz_2() {
		date_default_timezone_set('Europe/Rome');

		$datetime = new \DateTime('2008-08-03 12:35:23');
		echo $datetime->getTimezone()->getName() . "\n";

		$datetime = new \DateTime('2008-08-03 12:35:23');
		$la_time = new \DateTimeZone('America/Los_Angeles');
		$datetime->setTimezone($la_time);
		echo $datetime->getTimezone()->getName();
	}

	public static function getElapsedTime($pastTimestamp) {
		$currentTimestamp = time();
		$timePassed = $currentTimestamp - $pastTimestamp; //time passed in seconds
		// Minute == 60 seconds
		// Hour == 3600 seconds
		// Day == 86400
		// Week == 604800
		$elapsedString = '';
		if ($timePassed > 604800) {
			$weeks = floor($timePassed / 604800);
			$timePassed -= $weeks * 604800;
			$elapsedString = $weeks . ' weeks, ';
		}
		if ($timePassed > 86400) {
			$days = floor($timePassed / 86400);
			$timePassed -= $days * 86400;
			$elapsedString .= $days . ' days, ';
		}
		if ($timePassed > 3600) {
			$hours = floor($timePassed / 3600);
			$timePassed -= $hours * 3600;
			$elapsedString .= $hours . ' hours, ';
		}
		if ($timePassed > 60) {
			$minutes = floor($timePassed / 60);
			$timePassed -= $minutes * 60;
			$elapsedString .= $minutes . ' minutes, ';
		}
		$elapsedString .= $timePassed . ' seconds';

		return $elapsedString;
	}

	public static function getLastMonths($n) {
		$array = [];
		$today_time = time();
		$month_current = DateUtil::getMonthNum($today_time);
		$year_current = DateUtil::getYear($today_time);
		$bDone = false;
		$year = $year_current;

		$d2 = $month_current;
		$d3 = $n;

		while (!$bDone) {
			$m_start = 0;
			$m_end = 0;
			$diff = $d2 - $d3;
			if ($diff <= 0) {
				$m_start = 1;
				$m_end = $d2;
				$d2 = 12;
				$d3 = $diff * -1; // cambio il segno
			} else {
				$m_start = $diff;
				if ($year == $year_current) {
					$m_end = $month_current;
				} else {
					$m_end = 12;
				}
				$bDone = true;
			}
			$array["$year"] = [$m_start, $m_end];
			if ($diff <= 0) {
				$year = $year - 1;
			}
		} // end while

		// Il passo successivo....

		$months = [];

		foreach ($array as $year => $r) {
			$array2 = [];
			$start = $r[0];
			$end = $r[1];
			for ($i = $start; $i <= $end; $i++) {
				$array2[] = $i;
			}
			$months["$year"] = $array2;
		}
		ksort($months);

		return $months;
	}

	// DATE MANIPULATION

	public static function countDaysInMonth($month, $year) {
		// $last_day = date('d', mktime(0, 0, 0, $this->month + 1, 0, $this->year));
		// in alternativa posso usare ...
		return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}

	public static function getTodayNum() {
		return date('j', time()); // 1-31
	}

	public static function getMonthNum($date) {
		return date('n', $date);
	}

	public static function getYear($date) {
		return date('Y', $date);
	}

	public static function getDayOfMonth($date) {
		return date('j', $date);
	}

	public static function getMonthName($month) {
		$timestamp = mktime(0, 0, 0, $month, 1); // anno corrente, occorre specificare il giorno 1 onde evitare side-effects in date come 31/12
		return date('M', $timestamp);
	}

	public static function getPreviousMonthNum($month) {
		$prev = 0;
		if ($month == 1) {
			$prev = 12;
		} else {
			$prev = $month - 1;
		}
		return $prev;
	}

	public static function getPreviousYearNum($month, $year) {
		$prev = 0;
		if ($month == 1) {
			$prev = $year - 1;
		} else {
			$prev = $year;
		}
		return $prev;
	}

	public static function getDateFirstDayOfMonth($month, $year) {
		return self::getDateFirstDayOfMonth2($month, $year, DateUtil::$def_date_format);
	}

	public static function getDateLastDayOfMonth($month, $year) {
		return self::getDateLastDayOfMonth2($month, $year, DateUtil::$def_date_format);
	}

	public static function getDateFirstDayOfMonth2($month, $year, $date_format) {
		$timestamp = mktime(0, 0, 0, $month, 1, $year);
		return date($date_format, $timestamp);
	}

	public static function getDateLastDayOfMonth2($month, $year, $date_format) {
		$timestamp = mktime(0, 0, -1, $month + 1, 1, $year);
		//printDebug("test: " . date($date_format, $timestamp));
		return date($date_format, $timestamp);
	}

	public static function getDateAsEndOfDay($day_of_month, $month, $year) {
		return self::getDateAsEndOfDay2($day_of_month, $month, $year, DateUtil::$def_date_format);
	}

	public static function getDateAsEndOfDay2($day_of_month, $month, $year, $date_format) {
		$timestamp = mktime(0, 0, -1, $month, $day_of_month + 1, $year);
		return date($date_format, $timestamp);
	}
} // end class
