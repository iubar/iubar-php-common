<?php

namespace Iubar\Common;

// Per convertire una stringa dalla codifica sconosciuta in utf8:
// iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);

// Verificare anche il comportamento della funzione utf8_encode()

// Verificare anche
// mb_internal_encoding('UTF-8');

class StringUtil {
	public static function startsWith(string $haystack, string $needle): bool {
		$length = strlen($needle);
		return substr($haystack, 0, $length) === $needle;
	}

	public static function endsWith(string $haystack, string $needle): bool {
		$length = strlen($needle);

		return $length === 0 || substr($haystack, -$length) === $needle;
	}

	public static function getCharFromRight($char_pos, string $string): string {
		return substr($string, -$char_pos, 1);
	}

	public static function right(int $char_num, string $string): string {
		return substr($string, -$char_num, $char_num);
		// penso che sia equivalente scrivere
		// return substr($string, -$char_num);
	}

	public static function left(int $char_num, string $string): string {
		return substr($string, 0, $char_num);
	}

	public static function toCsv(array $array, string $quote = '', string $def_for_null = ''): string {
		// attenzione, a non invocare il metodo con $def_for_null=NULL ma usare $def_for_null = "NULL"
		$result = '';

		$length = count($array);
		if ($length > 0) {
			for ($i = 0; $i < $length - 1; $i++) {
				$value = $array[$i];
				if ($value === null) {
					$value = $def_for_null;
					$result .= $value . ', ';
					// 					} elseif ($quote === null) {
					// 						$result .= $value . ', ';
				} else {
					$result .= $quote . $value . $quote . ', ';
				}
			}

			// ...gestisco l'ultimo elemento dell'array...
			$value = $array[$length - 1];
			if ($value === null) {
				$value = $def_for_null;
				$result .= $value;
				// 				} elseif ($quote === null) {
				// 					$result .= $value;
			} else {
				$result .= $quote . $value . $quote;
			}
		}

		return $result;
	}

	public static function repeatString(string $str, int $n): string {
		$result = '';
		for ($i = 0; $i < $n; $i++) {
			$result .= $str;
		}
		return $result;
	}

	public static function isBracketBalanced(string $str, string $open_bracket = '(', string $closed_bracket = ')'): bool {
		$open = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			if ($open < 0) {
				return false;
			}

			$char = substr($str, $i, 1);
			if ($char == $open_bracket) {
				$open++;
			} elseif ($char == $closed_bracket) {
				$open--;
			}
		}

		if ($open == 0) {
			return true;
		}

		return false;
	}

	/**
	 * @deprecated trasformare in un test case
	 */
	public static function demo(): void {
		$string = 'abcdefgh';
		echo 'string is ' . $string;
		echo PHP_EOL;
		echo 'right 3: ' . StringUtil::right(3, $string);
		echo PHP_EOL;
		echo 'left 3: ' . StringUtil::left(3, $string);
		echo PHP_EOL;
	}

	public static function removeNl(string $txt): string {
		// forse in alternativa potrei usare la funzione trim()
		$txt = str_replace("\r\n", '', $txt);
		$txt = str_replace("\n", '', $txt);
		return $txt;
	}

	public static function arrayToText2(array $array, string $sep = PHP_EOL): string {
		$content = '';

		// $i = 0;
		foreach ($array as $elem) {
			if (is_array($elem)) {
				$content .= '[' . StringUtil::arrayToText2($elem) . ']';
			} else {
				//echo $i . PHP_EOL;;
				//$content = $content . $elem . $sep; // BUG HERE: VERY SLOWWWWW !!!!!!
				$content .= $elem . $sep;
				//$i++;
			}
		}

		return $content;
	}

	public static function searchAndGetRow(string $file, string $findme, int $from_row = 0): int {
		$row_num = -1;
		$array = StringUtil::textToArray($file);
		$n = 0;
		foreach ($array as $row) {
			if ($from_row >= $n) {
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

	public static function textToArray(string $file, string $sep_str = PHP_EOL): array {
		$array = [];
		$text = file_get_contents($file);
		$array = explode($sep_str, $text);
		return $array;
	}

	public static function arrayToText(array $array, string $sep = PHP_EOL): string {
		$content = implode($sep, $array) . $sep;
		return $content;
	}

	public static function posForward(string $str, int $pos, string $find): int {
		// USAGE: 	$pos_end = str_pos_forward($str, $pos, " ");
		//			if($pos_end == -1){$pos_end = strlen($str);}

		$j = strlen($str);
		for ($k = $pos; $k < $j; $k++) {
			$char = substr($str, $k, 1); // non posso usare $char = $str[$k] ???? bisognerebbe provare
			if ($char == $find) {
				return $k;
			}
		}
		return -1;
	}

	public static function posBackward(string $str, int $pos, string $find): int {
		// USAGE: 	$pos_start = str_pos_backward($str, $pos, " ") + 1;

		$j = strlen($str);
		for ($k = $pos; $k > -1; $k--) {
			$char = substr($str, $k, 1); // non posso usare $char = $str[$k] ???? bisognerebbe provare
			if ($char == $find) {
				return $k;
			}
		}
		return -1;
	}

	// DEPRECATO: metodo trasferito nella classe Formatter
	public static function boolean2String(bool $b): string {
		$str = 'false';
		if ($b) {
			$str = 'true';
		}
		return $str;
	}

	public static function strToHex(string $string): string {
		//return bin2hex($string);
		$hex = '';
		for ($i = 0; $i < strlen($string); $i++) {
			if (ord($string[$i]) < 16) {
				$hex .= '0';
			}
			$hex .= dechex(ord($string[$i]));
		}
		return strtoupper($hex);
	}

	public static function hexToStr(string $hex): string {
		$string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
	}

	public static function removeBadChars(string $str, array $bad_array): string {
		if ($str != '') {
			foreach ($bad_array as $search_char) {
				$str = str_replace($search_char, '', $str);
			}
		}
		return $str;
	}

	public static function replaceOnce(string $search, string $replace, string $string): string {
		if (strpos($string, $search) !== false) {
			// $occurrence = strpos($string, $search);
			return substr_replace($string, $replace, strpos($string, $search), strlen($search));
		}
		return $string;
	}

	public static function replaceOnce2(string $search, string $replace, string $string): string|array|null {
		return preg_replace('/$search/', $replace, $string, 1);
	}

	public static function getLastWord(string $text): string|null {
		$tokens = explode(' ', trim($text));
		$size = sizeof($tokens);
		return $tokens[$size - 1];
	}

	public static function getWordAfter(string $text, string $str): string|null {
		$word = null;
		$result = preg_split('/' . $str . '/', $text);
		if (count($result) > 1) {
			$result_split = explode(' ', $result[1]);
			$word = $result_split[1];
		}
		return $word;
	}

	public static function getSize(string $text): int {
		// If you need length of string in bytes (strlen cannot be trusted anymore because of mbstring.func_overload)
		return mb_strlen($text, '8bit');
	}

	public static function getFirstWord(string $text): string {
		$tokens = explode(' ', trim($text));
		return $tokens[0];
	}

	public static function remove_utf8_bom(string $text): string {
		$bom = pack('H*', 'EFBBBF');
		$text = preg_replace("/^$bom/", '', $text);
		return $text;
	}
	public static function remove_utf8_bom_2(string $text) {
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
	public static function encode(string $string, string $to = 'UTF-8', string $from = 'UTF-8'): string|false {
		// ASCII is already valid UTF-8
		if ($to == 'UTF-8' and self::is_ascii($string)) {
			return $string;
		}

		// Convert the string
		return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
	}

	/**
	 * Tests whether a string contains only 7bit ASCII characters.
	 *
	 */
	public static function is_ascii(string $string): int|false {
		// Usa una regex per verificare che tutti i caratteri siano nell'intervallo ASCII
		return preg_match('/^[\x00-\x7F]*$/', $string);
	}

	public static function toUtf8(string $str) {
		$utf8 = iconv(mb_detect_encoding($str, mb_detect_order(), true), 'UTF-8', $str);
		return $utf8;
	}

	function is_utf8_1(string $str): bool {
		return mb_check_encoding($str, 'UTF-8');
	}

	function is_utf8_2(string $str): bool {
		return (bool) preg_match('//u', $str);
	}

	function is_utf8_3(string $str): bool {
		return iconv('UTF-8', 'UTF-8//IGNORE', $str) === $str;
	}

	public static function isNotEmpty(string $str): bool {
		return $str !== '';
	}

	public static function isEmpty(string $str): bool {
		return !self::isNotEmpty($str);
	}

	public static function isTrue(string $str): bool {
		$b = false;
		if ($str == 'true' || intval($str) == 1) {
			$b = true;
		}

		return $b;
	}

	public static function trimString(?string $str): string|null {
		if ($str !== null) {
			return trim($str);
		} else {
			return null;
		}
	}
} // end class
