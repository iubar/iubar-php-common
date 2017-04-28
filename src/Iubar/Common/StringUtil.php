<?php

namespace Iubar\Common;

// Per convertire una stringa dalla codifica sconosciuta in utf8:
// iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);

// Verificare anche il comportamento della funzione utf8_encode()

// Verificare anche
// mb_internal_encoding('UTF-8');


class StringUtil {

	const NL = "\r\n";

	public static function getCharFromRight($char_pos, $string){
		return substr($string, -$char_pos, 1);
	}
		
	public static function right($char_num, $string){
		return substr($string, -$char_num, $char_num);
		// penso che sia equivalente scrivere
		// return substr($string, -$char_num);
	}
	
	public static function left($char_num, $string){
		return substr($string, 0, $char_num);
	}

	public static function toCsv($array, $quote = "", $def_for_null = ""){
		// attenzione, a non invocare il metodo con $def_for_null=NULL ma usare $def_for_null = "NULL"
		if($def_for_null===NULL){
			die("StringUtil.toCsv(): errore di invocazione del metodo.\r\n");
		}
		$result = "";
		if(is_array($array)){
			$length = count($array);			
			if($length>0){
			
				for($i = 0; $i < ($length-1); $i++){
					$value = $array[$i];					
					if($value===NULL){
						$value = $def_for_null;
						$result .= $value . ", ";
					} else if($quote===NULL){
						$result .= $value . ", ";
					}else{
						$result .= $quote . $value . $quote . ", ";
					}					
				}	
			
				// ...gestisco l'ultimo elemento dell'array...
				$value = $array[$length-1];			
				if($value===NULL){
					$value = $def_for_null;
					$result .= $value;
				}else if($quote===NULL){
					$result .= $value;
				}else{
					$result .= $quote . $value . $quote;
				}
				
			}
			
		}else{
			// TODO: throw an illegalargument exception
		}
		return $result;
	}
	
	public static function repeatString($str, $n){
		$result = "";
		for($i = 0; $i < $n; $i++){
			$result .= $str;
		}
		return $result;
	}	
	
	public static function demo(){
		$string = "abcdefgh";
		echo "string is " . $string;
		echo StringUtil::NL;
		echo "right 3: " . StringUtil::right(3, $string);
		echo StringUtil::NL;
		echo "left 3: " . StringUtil::left(3, $string);
		echo StringUtil::NL;
	}
	
	public static function removeNl($txt){
		// forse in alternativa potrei usare la funzione trim()
		$txt = str_replace("\r\n", "", $txt);
		$txt = str_replace("\n", "", $txt);
		return $txt;
	}
	
	public static function arrayToText2($array, $sep=StringUtil::NL){
		$content = "";
		if (is_array($array)){
		// $i = 0;
			foreach($array as $elem){
				if (is_array($elem)){
					$content .= '[' . StringUtil::arrayToText2($elem) . ']';
				}else{
				//echo $i . StringUtil::NL;;
				//$content = $content . $elem . $sep; // BUG HERE: VERY SLOWWWWW !!!!!!
				$content .= $elem . $sep;
				//$i++;
				}
			}
		}else{
			$content = "<not an array>" . $sep;
		}
		return $content;
	}
	
	public static function searchAndGetRow($file, $findme, $from_row=0){
		$row_num = -1;
		$array = StringUtil::textToArray($file);
		$n = 0;
		foreach($array as $row){
			if($from_row>=$n){
				$pos = strpos($row, $findme);
				if ($pos !== false) {
				    $row_num = $n;
				    break;
				}
			}
			$n++;
		}
		return $row_num;
	}
	
	public static function textToArray($file, $sep_str=StringUtil::NL){
		$array = array();
		$text = file_get_contents($file);
		$array = explode($sep_str, $text);
		return $array;
	}
	
	public static function arrayToText($array, $sep=StringUtil::NL){
		$content = "";
		if (is_array($array)){
			$content = implode($sep, $array) . $sep;
		}else{
			$content = "<not an array>" . $sep;
		}
		return $content;
	}
	
	public static function posForward($str, $pos, $find){
	
		// USAGE: 	$pos_end = str_pos_forward($str, $pos, " ");
		//			if($pos_end == -1){$pos_end = strlen($str);}
	
		$j = strlen($str);
		for ($k = $pos; $k < $j; $k++) {
			$char = substr($str, $k, 1); // non posso usare $char = $str[$k] ???? bisognerebbe provare
			if($char==$find){
				return $k;
			}
		}
		return -1;
	}
	
	public static function posBackward($str, $pos, $find){
	
		// USAGE: 	$pos_start = str_pos_backward($str, $pos, " ") + 1;
	
		$j = strlen($str);
		for ($k = $pos; $k > -1; $k--) {
			$char = substr($str, $k, 1); // non posso usare $char = $str[$k] ???? bisognerebbe provare
			if($char==$find){
				return $k;
			}
		}
		return -1;
	}
	
	// DEPRECATO: metodo trasferito nella classe Formatter
	public static function boolean2String($b){
		$str = "false";
		if($b){
			$str = "true";
		}
		return $str;
	}
	
	public static function strToHex($string){
		//return bin2hex($string);
		$hex="";
		for ($i=0; $i < strlen($string); $i++){
		if (ord($string[$i])<16)
			$hex .= "0";
			$hex .= dechex(ord($string[$i]));
		}
		return strtoupper($hex);
	}
	
	public static function hexToStr($hex){
	    $string='';
	    for ($i=0; $i < strlen($hex)-1; $i+=2){
	        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
	    }
	    return $string;
	}
	
	public static function removeBadChars($str, $bad_array){
		if($str!=""){
			foreach($bad_array as $search_char){
				$str = str_replace($search_char, "", $str);
			}
		}
		return $str;
	}

	public static function replaceOnce($search, $replace, $string){	
		if (strpos($string, $search) !== false){
			// $occurrence = strpos($string, $search);
			return substr_replace($string, $replace, strpos($string, $search), strlen($search));
		}	
		return $string;
	}
	
	public static function replaceOnce2($search, $replace, $string){
		return preg_replace('/$search/', $replace, $string, 1);
	}
	
	public static function getLastWord($text){
		$tokens = explode(' ', trim($text));
		return $tokens[(sizeof($array)-1)];
	}
	
	public static function getWordAfter($text, $str){
		$word = null;
		$result = preg_split('/' . $str . '/', $text);
		if(count($result) > 1){
			$result_split = explode(' ', $result[1]);
			$word = $result_split[1];
		}
		return $word;
	}
	
	public static function getSize($text){
		// If you need length of string in bytes (strlen cannot be trusted anymore because of mbstring.func_overload)		
		return mb_strlen($text, '8bit');		
	}
	
	public static function getFirstWord($text){
		$tokens = explode(' ', trim($text));
		return $tokens[0];
	}

	public static function remove_utf8_bom($text) {
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);
		return $text;
	}
	public static function remove_utf8_bom_2($text) {
		$text = str_replace("\xEF\xBB\xBF", '', $text);
		return $text;
	}


	/**
	 * Convert a string from one encoding to another encoding
	 * and remove invalid bytes sequences.
	 *
	 * @param string $string to convert
	 * @param string $to encoding you want the string in
	 * @param string $from encoding that string is in
	 * @return string
	 */
	public static function encode($string, $to = 'UTF-8', $from = 'UTF-8'){
		// ASCII is already valid UTF-8
		if($to == 'UTF-8' AND is_ascii($string)){
			return $string;
		}
	
		// Convert the string
		return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
	}
	
	
	/**
	 * Tests whether a string contains only 7bit ASCII characters.
	 *
	 * @param string $string to check
	 * @return bool
	 */
	public static function is_ascii($string){
		return ! preg_match('/[^\x00-\x7F]/S', $string);
	}
	
	public static function toUtf8($str){
		$utf8 = iconv(mb_detect_encoding($str, mb_detect_order(), true), "UTF-8", $str);
		return $utf8;
	}
	
} // end class

?>