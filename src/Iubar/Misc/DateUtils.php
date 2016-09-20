<?php

namespace Iubar\Misc;

class DateUtils {
	
	const DEFAULT_DATE_SEPARATOR = '/';
	const DEFAULT_DATE_FORMAT = 'Y-m-d';
	const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';
	
	public static function getTodayDate($separator = null){
		if ($separator === null){
			$separator = self::DEFAULT_DATE_SEPARATOR;
		}
		return date('m'. $separator . 'd'. $separator .'Y');
	}
	
	public static function dateTimeToString(\DateTime $date = null){
		$str = null;
		if ($date !== null){
			$date->setTimezone(new \DateTimeZone('Europe/Rome'));
			$str = $date->format(self::DEFAULT_DATE_FORMAT);
		}
		
		return $str;
	}
	
	public static function stringToDate($str_date){
		$str_date = str_replace('/', '-', $str_date);
		$date = new \DateTime($str_date);
		$date->setTimezone(new \DateTimeZone('Europe/Rome'));
		return $date->format(self::DEFAULT_DATE_FORMAT);
	}
	
	public static function stringToDateTimeStr($str_date){
		$str_date = str_replace('/', '-', $str_date);
		$date = new \DateTime($str_date);
		$date->setTimezone(new \DateTimeZone('Europe/Rome'));
		return $date->format(self::DEFAULT_DATE_TIME_FORMAT);
	}
	
	public static function stringToDateTime($str_date){
	    $str_date = str_replace('/', '-', $str_date);
	    $date = new \DateTime($str_date);
	    $date->setTimezone(new \DateTimeZone('Europe/Rome'));
	    return $date;
	}
	
	public static function getCurrentYear(){
		$now = new \DateTime();
		return $now->format("Y");
	}
}