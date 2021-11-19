<?php

namespace Iubar\Common;

use Iubar\Common\BaseClass;
use Doctrine\SqlFormatter\SqlFormatter;
use Doctrine\SqlFormatter\NullHighlighter;

class Formatter extends BaseClass {
	
	public static function formatCf($cf) {
		$cf = trim($cf);
		$cf = strtoupper($cf);
		return $cf;
	}
	public static function formatProvincia($provincia) {
		$provincia = trim($provincia);
		$provincia = str_replace("(", "", $provincia);
		$provincia = str_replace(")", "", $provincia);
		$provincia = strtoupper($provincia);
		return $provincia;
	}	
	public static function formatEmail($email) {
		$email = Formatter::cleanEmail($email);
		$email = strtolower($email);
		return $email;
	}
	public static function formatPhoneNum($tel) {
		$tel = Formatter::cleanPhoneNum($tel);
		return $tel;
	}

	public static function string2Boolean($str){
		
// 		Converting to boolean
		
// 		To explicitly convert a value to boolean, use the (bool) or (boolean) casts. However, in most cases the cast is unncecessary, since a value will be automatically converted if an operator, function or control structure requires a boolean argument.
		
// 		See also Type Juggling.
		
// 		When converting to boolean, the following values are considered FALSE:
		
// 		the boolean FALSE itself
// 		the integer 0 (zero)
// 		the float 0.0 (zero)
// 		the empty string, and the string "0"
// 		an array with zero elements
// 		an object with zero member variables (PHP 4 only)
// 		the special type NULL (including unset variables)
// 		SimpleXML objects created from empty tags
// 		Every other value is considered TRUE (including any resource).
		
		$b = false;
		$str = trim($str);
		if(strtolower($str)==="true"){ 
			$b = true;
		}else if($str==="1"){
			$b = true;
		}else if($str==1){ // TODO: VERIFICARE SE IL CODICE E' OK; Ho scritto questa condizione in caso qualcuno invocasse il metodo passando un intero invece di una stringa. Bisoga però valutare se la condizione è ridondante rispetto alla precedente !!!!
			$b = true;
		}
		return $b;
	}
		
	public static function boolean2String($b){
		$str = "false";
		if($b){
			$str = "true";
		}
		return $str;
	}
	
	public static function boolean2Int($b){
		$n = 0;
		if($b){
			$n = 1;
		}
		return $n;
	}	
	
	public static function boolean2Ok($b){
		$str = "KO";
		if($b){
			$str = "OK";
		}
		return $str;
	}
	
	public static function toTitleCase($str){ // TODO:  
		// TODO !!!!
		// Attenzione a sigle, numeri Romani, predicati con apostrofo
		// http://www.dailywritingtips.com/rules-for-capitalization-in-titles/
		return $str;
	}
	
	public static function toCamelCase($str) { // TODO: scrivere test per parole con apostrofo (es: "D'ARCO")
		
		// TODO: attenzione a parole che includono accenti poichè vengono trattate come unsa singola parola
		// $substrs = array("D'", "ll'");
		// Aggiungere uno spazio dopo ogni apostrofo, trasformare in camel case
		// e infine sostituire "' " con il semplice "'"
		
		// echo "Before toCamelCase(): '$str'" . PHP_EOL; // Debug 
		$enc = mb_detect_encoding($str);
		$str = trim($str);
		if($enc=="UTF-8"){
			// echo "\$enc is: " . $enc . PHP_EOL;			
			
			//$str = mb_strtolower($str, 'UTF-8');
			//$str = ucwords($str);
			// oppure
			$str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
			
		}else{
			$str = strtolower($str); // obbligatorio prima di ucwords()	
			$str = ucwords($str);
		}
		
		// echo "After toCamelCase(): '$str'" . PHP_EOL; // Debug 
		return $str;
	}
	public static function cleanForSql($str) { // TODO: quando usare questo metodo ? Che differenza c'è tra "\'" e "''" ? 
		$str = trim($str);
		$str = addslashes($str);
		return $str;
	}
	public static function cleanString($str) {
		if ($str != "") {
			
			// Prima di eliminare tutti i tag, converto gli "a capo" con uno spazio
			$array_br = array (
					"<br>",
					"<br />",
					"<br/>",
					"<p>",
					"<p />",
					"<p/>" 
			);
			foreach ( $array_br as $bad_str ) {
				$str = str_replace ( strtolower ( $bad_str ), " ", $str );
				$str = str_replace ( strtoupper ( $bad_str ), " ", $str );
			}
			// Quindi elimino tutti i tag html
			$str = strip_tags ( $str );
			$array = array (
					"&nbsp;",
					"\t",
					":" 
			); // in alternativa per eliminare solo i tabs, potrei usare preg_replace('/\t/g', '', $string);
			foreach ( $array as $bad_str ) {
				$str = str_replace ( $bad_str, " ", $str );
			}
			$str = str_replace("  ", " ", $str); // remove double spaces // TODO: errato in caso di 4 spazi contigui...
			$str = trim($str);
			$str = Formatter::mb_trim( $str );
		}
		return $str;
	}

	public static function cleanPhoneNum($num) {
		if ($num != "") {
			$num = strip_tags ($num);
			$array = array (
					"/",
					"\\",
					".",
					",",
					"-",
					"(",
					")",
					"&nbsp;" 
			);
			foreach ( $array as $bad_str ) {
				$num = str_replace ($bad_str, " ", $num);
			}
			$num = trim($num);
			$num = Formatter::mb_trim($num);
			$num = Formatter::removeSpaces($num);
		}
		return $num;
	}
	
	public static function cleanPath($path) {
		$path = trim($path);
		$path = str_replace("\"", "", $path);
		$path = str_replace("'", "", $path);
		if(is_dir($path)){
			$path = realpath($path);
		}
		return $path;		
	}
	
	public static function cleanEmail($email) {
		if ($email) {
			$email = strip_tags ($email);

			$array = array (
					";",
					"'",
					"'",
					"mailto:"
			);
		
			foreach ( $array as $bad_str ) {
				$email = str_replace ($bad_str, " ", $email);
			}
			
			$email = trim($email);
			$email = Formatter::mb_trim ($email);
		}
		return $email;
	}
	public static function mb_trim($str) { // multibyte-safe trim
		return preg_replace ( "/(^\s+)|(\s+$)/us", "", $str );
	}
	public static function formatSeconds($sec) {
		// number_format($sec, 2, ',', '.') . " s"
		$hours = ( int ) ($sec / 60 / 60);
		$minutes = ( int ) ($sec / 60) - ($hours * 60);
		$seconds = ( int ) $sec - ($hours * 60 * 60) - ($minutes * 60);
		$str = $hours . " ore, " . $minutes . " minuti, " . $seconds . " secondi";
		return $str;
	}
	public static function formatSeconds2($sec, $show_ms=false) {
		$milliseconds = ( int ) ($sec * 1000);
		$seconds = ($milliseconds / 1000);
		$minutes = ( int ) ($sec / 60);
		$hours = ( int ) ($minutes / 60);
		
		$hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
		$minutes = str_pad(($minutes % 60), 2, '0', STR_PAD_LEFT);
		$seconds = str_pad(($seconds % 60), 2, '0', STR_PAD_LEFT);
		
		$str = $hours . ':' . $minutes . ':' . $seconds;
		if($show_ms){
			$str = $str . (($milliseconds === 0) ? '' : '.' . rtrim ( $milliseconds % 1000, '0' ));
		}
		return $str;
	}
	public static function formatMicroTimeAsDate($sec) {
		$str = date ( "l jS F \@ g:i a", $sec );
		return $str;
	}
	
	public static function formatFloatIt($number, $dec=0){
		$number_format_it = number_format($number, $dec, ',', '.');
		return $number_format_it;
	}
	
	public static function formatBytes($yoursize) {
		// $r = number_format($yoursize / 1024, 2, ',', '.')
		$str = NULL;
		if ($yoursize < 1024) {
			$str = "{$yoursize} bytes";
		} elseif ($yoursize < 1048576) {
			$size_kb = round ( $yoursize / 1024 );
			$str = "{$size_kb} KB";
		} else {
			$size_mb = round ( $yoursize / 1048576, 1 );
			$str = "{$size_mb} MB";
		}
		return $str;
	}
	public static function formatBytesPerSec($yoursize) {
		$str = Formatter::formatBytes($yoursize) . "/s";
		return $str;
	}
	public static function formatTimestamp($time) {
		$str = date("F d Y H:i:s.", $time); // TODO: controllare se è formato italiano
		return $str;
	}
	public static function replaceAccents($str) {
		$search = explode(",",
				"ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
		$replace = explode(",",
				"c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
		$str = str_replace($search, $replace, $str);
		$str = $str . "'";
		return $str;
	}
	public static function removeMultipleSpaces($string){
		//echo "Before removeMultipleSpaces() : '$string'" . PHP_EOL; // Debug 
		$enc = mb_detect_encoding($string);
		if($enc=="UTF-8"){
			//echo "\$enc is: " . $enc . PHP_EOL;
			$string = preg_replace('/\s+/u', ' ', $string);
		}else{
			$string = preg_replace('/\s+/', ' ', $string);
		}
		//echo "After removeMultipleSpaces(): '$string'" . PHP_EOL; // Debug 
		return $string;
	}
	public static function removeSpaces($string){
		$string = preg_replace('/\s/', ' ', $string);
		return $string;
	}
	
	public static function testEncoding(){
		// echo "mb_detect_encoding(à): " . mb_detect_encoding("à") . PHP_EOL; // VALE SEMPRE 'UTF-8' se il file è utf-8
		//echo "current mb_regex_encoding: " . mb_regex_encoding() . PHP_EOL;	 	// VALE SEMPRE 'EUC-JP'		
		echo "current mb_internal_encoding: " . mb_internal_encoding() . PHP_EOL;		
		echo "changing mb_internal_encoding to UTF-8" . PHP_EOL;
		mb_internal_encoding("UTF-8");
		echo "new mb_internal_encoding: " . mb_internal_encoding(). PHP_EOL;	
		//echo "mb_regex_encoding: " . mb_regex_encoding() . PHP_EOL;	// VALE SEMPRE 'EUC-JP'	
		// echo "mb_detect_encoding(à): " . mb_detect_encoding("à") . PHP_EOL; // VALE SEMPRE 'UTF-8' se il file è utf-8
	}
	
	
	

	public static function formatBytes2($file, $type) {
	    switch ($type) {
	        case "KB":
	            $filesize = filesize($file) * .0009765625; // bytes to KB
	            break;
	        case "MB":
	            $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
	            break;
	        case "GB":
	            $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
	            break;
	    }
	
	    if ($filesize <= 0) {
	        return $filesize = 'unknown file size';
	    } else {
	        return round($filesize, 2) . ' ' . $type;
	    }
	}
	
	public static function secondsToWords($seconds) {
	    $ret = "";
	    $hours = intval(intval($seconds) / 3600);
	
	    if ($hours > 0) {
	
	        $ret .= "$hours hours ";
	    }
	    $minutes = bcmod((intval($seconds) / 60), 60);
	
	    if ($hours > 0 || $minutes > 0) {
	
	        $ret .= "$minutes minutes ";
	    }
	    $seconds = bcmod(intval($seconds), 60);
	    $ret .= "$seconds seconds";
	    return $ret;
	}
	
	public static function secondsToWords2($seconds) {
	
	    /**
	     * * number of days **
	     */
	    $days = (int) ($seconds / 86400);
	
	    /**
	     * * if more than one day **
	     */
	
	    $plural = $days > 1 ? 'days' : 'day';
	
	    /**
	     * * number of hours **
	     */
	
	    $hours = (int) (($seconds - ($days * 86400)) / 3600);
	
	    /**
	     * * number of mins **
	     */
	
	    $mins = (int) (($seconds - $days * 86400 - $hours * 3600) / 60);
	
	    /**
	     * * number of seconds **
	     */
	
	    $secs = (int) ($seconds - ($days * 86400) - ($hours * 3600) - ($mins * 60));
	
	    /**
	     * * return the string **
	     */
	
	    return sprintf("%d $plural, %d hours, %d min, %d sec", $days, $hours, $mins, $secs);
	}
	
	public static function secondsToWords3($secs) {
	    $vals = array(
	        'w' => (int) ($secs / 86400 / 7),
	
	        'd' => $secs / 86400 % 7,
	
	        'h' => $secs / 3600 % 24,
	
	        'm' => $secs / 60 % 60,
	
	        's' => $secs % 60
	    );
	
	    $ret = array();
	
	    $added = false;
	
	    foreach ($vals as $k => $v) {
	
	        if ($v > 0 || $added) {
	
	            $added = true;
	
	            $ret[] = $v . $k;
	        }
	    }
	
	    return join(' ', $ret);
	}
	
	public static function formatSql(string $query, bool $highlight) : string 
	{
	    $formatter = null;
	    if($highlight){
	        $formatter = new SqlFormatter();
	    }else{
	        $formatter = new SqlFormatter(new NullHighlighter());
	    }
	    return $formatter->format($query);
	}
	
} // end class