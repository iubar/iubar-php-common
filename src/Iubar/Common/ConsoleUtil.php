<?php

namespace Iubar\Common;

use Iubar\Common\LangUtil;
use League\CLImate\CLImate;

class ConsoleUtil {
	
public static function getUserFolderPath($win=true){ // Only for WINDOWS
	$out = ".";
	if($win){
		$var = "%USERPROFILE%";
		$cmd = "echo " . $var;
		$out = trim(shell_exec($cmd));
	}
	return $out;
}

public static function pressAKeyToContinue(){
	$f = fopen('php://stdin', 'r');
	echo "Premi [invio] per continuare..." . StringUtil::NL;
	$line = fgets($f, 1024); // read the special file to get the user input from keyboard
	fclose($f);
}

public static function echoContinue($def_boolean=true){
	$f = fopen('php://stdin', 'r');

	if($def_boolean){
		echo "Continuare ? [s]: ";
	}else{
		echo "Continuare ? [n]: ";
	}

	$line = trim(fgets($f));

	if($line=="" && strlen($line)==0){
		if($def_boolean){
			$line = "y";
		}else{
			$line = "n";
		}
	}

	fclose($f);
	return ConsoleUtil::isYes($line);
}

public static function isNo($line){
	return !ConsoleUtil::isYes($line);
}


public static function yesToBoolean($str){
	$b=false;
	if(ConsoleUtil::isYes($str)){
		$b=true;
	}
	return $b;
}

public static function isYes($str){
	$b=false;
	$str = strtolower(trim($str));
	$array = array("sì", "si", "s", "yes", "y", "1");
	if (in_array($str, $array)) {
		$b=true;
	}
	return $b;
}

public static function writeSeparator(){
	echo "--------------------------------------" . StringUtil::NL;
}



public static function beep($int_beeps = 1) {
	for ($i = 0; $i < $int_beeps; $i++): $string_beeps .= "\x07"; endfor;
	print $string_beeps;
}


public static function read($length=255) {
	// FIXME: se digito la seguente string fgets() restituisce una stringa vuota:
	// cifaro61òlibero.it	
	// Ho provato con stream_get_line() ma ho ottenuto stesso risultato
	if (!isset ($GLOBALS['StdinPointer'])) {
		$GLOBALS['StdinPointer'] = fopen ("php://stdin", "r");
	}
	$line = fgets ($GLOBALS['StdinPointer'], $length);
	return trim($line);
}

private static function printMenu($array){
	foreach ($array as $key=>$value){				
		$prefix = "";
		if(LangUtil::is_assoc($array)){
			$index = array_search($key, array_keys($array)) + 1;
			$prefix = $index . ") " . $key . " => ";
		}else{
			$index = $key + 1;
			$prefix = $index . ") ";
		}
		if(!is_array($value)){
			echo $prefix . $value . PHP_EOL;
		}else{
			echo $prefix . json_encode($value) . PHP_EOL;
		}		
	}
}

public static function showMenu($array, $title="", $def=1){
	$result = null;
	if($title){
		self::printTitle($title);
	}
	if($array==null || count($array)==0){
		echo "Errore: impossibile visualizzare il menu, l'elenco è vuoto." . PHP_EOL;
		self::pressAKeyToContinue();
	}else{
		self::printMenu($array);
		echo PHP_EOL;
		$choice = self::askValue("Scegli" , $def) - 1;
		if(LangUtil::is_assoc($array)){
			$keys = array_keys($array);
			$result = $keys[$choice]; // il risultato è la chiave dell'elemento dell'array associativo scelto
		}else{
			$result = $array[$choice]; // il risultato è il valore corrispondente all'indice scelto
		}
	}
	return $result;
}

public static function askValue($txt, $def=""){
	if($def==="" || $def===null){
		echo $txt . ": ";
		$value = ConsoleUtil::read();
	}else{
		echo $txt . " [" . $def . "]: ";
		$value = ConsoleUtil::read();
		if($value=="" && strlen($value)==0){
			$value = $def;
		}
	}	
	return $value;
}
public static function askQuestion($question, $def_boolean=true){
	$b = false;
	echo StringUtil::NL;
	$f = fopen('php://stdin', 'r');

	if($def_boolean){
		echo $question . " [si]: ";
	}else{
		echo $question . " [no]: ";
	}

	$line = trim(fgets($f));

	if($line=="" && strlen($line)==0){
		$b = $def_boolean;
	}else{
		$b = ConsoleUtil::isYes($line);
	}

	fclose($f);
	return $b;
}

						public static function progressBarForLinuxTerminal($current=0, $total=100, $label="", $size=50) {
						
							//Don't have to call $current=0
							//Bar status is stored between calls
							static $bars;
							if(!isset($bars[$label])) {
								$new_bar = TRUE;
								fputs(STDOUT,"$label Progress:" . StringUtil::NL);
							}
							if($current == $bars[$label]) return 0;
						
							$perc = round(($current/$total)*100,2);        //Percentage round off for a more clean, consistent look
							for($i=strlen($perc); $i<=4; $i++) $perc = ' '.$perc;    // percent indicator must be four characters, if shorter, add some spaces
						
							$total_size = $size + $i + 3;
							// if it's not first go, remove the previous bar
							if(!$new_bar) {
								for($place = $total_size; $place > 0; $place--) echo "\x08";    // echo a backspace (hex:08) to remove the previous character
							}
							 
							$bars[$label]=$current; //saves bar status for next call
							// output the progess bar as it should be
							for($place = 0; $place <= $size; $place++) {
								if($place <= ($current / $total * $size)) echo '[42m [0m';    // output green spaces if we're finished through this point
								else echo '[47m [0m';                    // or grey spaces if not
							}
						
							// end a bar with a percent indicator
							echo " $perc%";
						
							if($current == $total) {
							echo StringUtil::NL;        // if it's the end, add a new line
							unset($bars[$label]);
							}
							}
													
										
/*

Copyright (c) 2010, dealnews.com, Inc.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice,
this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
* Neither the name of dealnews.com, Inc. nor the names of its contributors
may be used to endorse or promote products derived from this software
without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
		SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
		INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

*/

/**
 * show a status bar in the console
*
* <code>
* for($x=1;$x<=100;$x++){
*
*     show_status($x, 100);
*
*     usleep(100000);
*
* }
* </code>
*
* @param   int     $done   how many items are completed
* @param   int     $total  how many items are to be done total
* @param   int     $size   optional size of the status bar
* @return  void
*
*/

public static function showStatus($done, $total, $size=30) {

	static $start_time;

	// if we go over our bound, just ignore it
	if($done > $total) return;

	if(empty($start_time)) $start_time=time();
	$now = time();

	$perc=(double)($done/$total);

	$bar=floor($perc*$size);

	$status_bar="\r[";
	$status_bar.=str_repeat("=", $bar);
	if($bar<$size){
		$status_bar.=">";
		$status_bar.=str_repeat(" ", $size-$bar);
	} else {
		$status_bar.="=";
	}

	$disp=number_format($perc*100, 0);

	$status_bar.="] $disp%  $done/$total";

	$rate = ($now-$start_time)/$done;
	$left = $total - $done;
	$eta = round($rate * $left, 2);

	$elapsed = $now - $start_time;

	$status_bar.= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

	echo "$status_bar  ";

	flush();

	// when done, send a newline
	if($done == $total) {
		echo StringUtil::NL;
	}

}
	
public static function printTitle($title){
	echo StringUtil::NL;
	echo "---------------" . StringUtil::NL;
	echo $title . StringUtil::NL;
	echo "---------------" . StringUtil::NL;
	echo StringUtil::NL;
}		

public static function stackTrace() {
    $stack = debug_backtrace();
    $output = '';

    $stackLen = count($stack);
    for ($i = 1; $i < $stackLen; $i++) {
        $entry = $stack[$i];

        $func = $entry['function'] . '(';
        $argsLen = count($entry['args']);
        for ($j = 0; $j < $argsLen; $j++) {
            $my_entry = $entry['args'][$j];
            if (is_string($my_entry)) {
                $func .= $my_entry;
            }
            if ($j < $argsLen - 1) $func .= ', ';
        }
        $func .= ')';

        $entry_file = 'NO_FILE';
        if (array_key_exists('file', $entry)) {
            $entry_file = $entry['file'];               
        }
        $entry_line = 'NO_LINE';
        if (array_key_exists('line', $entry)) {
            $entry_line = $entry['line'];
        }           
        $output .= $entry_file . ':' . $entry_line . ' - ' . $func . PHP_EOL;
    }
    return $output;
}

public static function stackTrace2() {
    $stack = debug_backtrace();
    $output = 'Stack trace:' . PHP_EOL;

    $stackLen = count($stack);
    for ($i = 1; $i < $stackLen; $i++) {
        $entry = $stack[$i];

        $func = $entry['function'] . '(';
        $argsLen = count($entry['args']);
        for ($j = 0; $j < $argsLen; $j++) {
            $func .= $entry['args'][$j];
            if ($j < $argsLen - 1) $func .= ', ';
        }
        $func .= ')';

        $output .= '#' . ($i - 1) . ' ' . $entry['file'] . ':' . $entry['line'] . ' - ' . $func . PHP_EOL;
    }

    return $output;
}
	
} // end class	
	
?>