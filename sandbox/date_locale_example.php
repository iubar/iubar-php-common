<?php

setlocale(LC_TIME, 'IT');
//

$tz = new DateTimeZone('Europe/Rome');
$date = new DateTime();
$date->setTimezone($tz);
echo 'NONE: ' . $date->format("l m-d-Y H:i:s") . PHP_EOL;
echo 'RFC822: ' . $date->format(DateTime::RFC822) . PHP_EOL;
echo 'RFC850: ' . $date->format(DateTime::RFC850) . PHP_EOL;


$format1 = 'l d-m-Y H:i:s O'; 			// see http://php.net/manual/it/function.date.php
$format2 = '%A %d-%m-%Y %H:%M:%S %z'; 	// see http://php.net/manual/en/function.strftime.php
$timestamp = $date->getTimestamp(); 
$d = date($format1, $timestamp);
$str = ucfirst(strftime($format2, $timestamp));
echo "Date: " . $d . PHP_EOL;
echo "Localized date: " . $str . PHP_EOL;