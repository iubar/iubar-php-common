<?php

use Iubar\Common\Validator;

// TODO: settare prima il LOCALE

charsetTest();
test2();
// die();

// test("null", null);
// test("empty string", "");
// test("0", 0);
// test("false", false);
// Tutti e quattro i valori sopra, resttuiscono true se confrontati con zero, null e stringa vuota
// quando si utilizza l'operatore semplice ==
// if(null===NULL){
// 	echo "null===NULL" . PHP_EOL; // SI
// 	echo PHP_EOL;
// }

// Scrivere test per
// mb_detect_encoding($to_name, 'ASCII', true)
// utf8_encode
// utf8_decode($data [$c]);
// iconv("UTF-8", "ISO-8859-1", $data [$c]);
// iconv("UTF-8", "ASCII//TRANSLIT", $data [$c]);
// iconv("UTF-8", "CP1256", $data [$c]);
// iconv("UTF-8", "Windows-1252", $data [$c]);

function charsetTest() {
	$data = '';
	$data .= "'à': " . 'à' . PHP_EOL;
	$data .= "utf8_encode('à'): " . utf8_encode('à') . PHP_EOL;
	$data .= "utf8_decode('à'): " . utf8_decode('à') . PHP_EOL;

	// $hex_string = bin2hex (utf8_encode('à'));
	// $data .= "bin2hex(utf8_encode('à')): " . $hex_string  . PHP_EOL;
	// bin2hex(utf8_encode('à')) restituisce c383c2a0 (c383 == "Ã" ) (c2a0 == "&nbsp;")

	$array = ['à', 'è', 'é', 'ì', 'ò', 'ù', '€'];
	foreach ($array as $c) {
		$hex_string = bin2hex($c);
		$data .= "bin2hex('" . $c . "'): " . $hex_string . PHP_EOL;
	}

	$s1 = chr(0xc3) . chr(0x83) . chr(0xc2) . chr(0xa0);
	$data .= "chr(0xc3) . chr(0x83) . chr(0xc2) . chr(0xa0) = '$s1'" . PHP_EOL;
	$data .= 'utf8_decode(chr(0xc3) . chr(0x83) . chr(0xc2) . chr(0xa0)): ' . utf8_decode($s1) . PHP_EOL;

	$data .= 'utf8_decode(): ' . utf8_decode($s1) . PHP_EOL;

	$data .= 'Magia: à == ' . chr(0xc3) . chr(0xa0) . PHP_EOL;

	$filename = "C:\\Users\\Borgo\\workspace_php\\php\\php_iubar_anag\\php\\temp\\utf_test.txt";
	file_put_contents($filename, $data);
	die('QUIT' . PHP_EOL);
}

function test2() {
	$filename = 'C:\\Users\\Borgo\\Desktop\\dati.csv.txt';
	$str = file_get_contents($filename);

	$regex_phrase_of_words1 = Validator::getUtf8RegExPhraseOfWords();

	$a = chr(0xc3) . chr(0xa0); //  'à' c3 a0
	$e1 = chr(0xc3) . chr(0xa8); //  'è' c3 a8
	$e2 = chr(0xc3) . chr(0xa9); //  'é' c3 a9
	$i = chr(0xc3) . chr(0xac); //  'ì' c3 ac
	$o = chr(0xc3) . chr(0xb2); //  'ò' c3 b2
	$u = chr(0xc3) . chr(0xb9); //  'ù' c3 b9
	$eur = chr(0xe2) . chr(0x82) . chr(0xac); // '€' e2 82 ac

	$row = 0;
	$handle = fopen($filename, 'ru');
	while (($data = fgetcsv($handle, 1000, ',')) !== false) {
		$num = count($data);
		echo "$num campi sulla linea $row" . PHP_EOL;
		$row++;
		for ($c = 0; $c < $num; $c++) {
			$str = $data[$c];
			$str = trim($str);
			$str = str_replace("\xEF\xBB\xBF", '', $str); // REMOVE BOM !!!
			if ($c == 4) {
				$b = preg_match($regex_phrase_of_words1, $str, $matches) || $str == '';
				if (!$b) {
					die("Non alfabetico on row $row column $c: '$str'" . PHP_EOL);
				} else {
					echo "row $row column $c OK: '$str'" . PHP_EOL;
				}
			}
		}
	}
	fclose($handle);
}

//////////////////////////////////////////////////////////////////////

function test($desc, $value) {
	echo 'Input: ' . $value . ' (' . $desc . ') (' . strlen($value) . ' chars)' . PHP_EOL;
	askValue($value);
	echo PHP_EOL;
}

function askValue($value) {
	$found = false;
	if ($value === '') {
		echo 'Empty string (same class)' . PHP_EOL;
		$found = true;
	}
	if ($value == '') {
		echo 'Empty String (after casting)' . PHP_EOL;
		$found = true;
	}
	if ($value === null) {
		echo 'Null (same class)' . PHP_EOL;
		$found = true;
	}
	if ($value == null) {
		echo 'Null (after casting)' . PHP_EOL;
		$found = true;
	}
	if ($value === 0) {
		echo 'Zero (same class)' . PHP_EOL;
		$found = true;
	}
	if ($value == 0) {
		echo 'Zero (after casting)' . PHP_EOL;
		$found = true;
	}
	if ($value) {
		echo 'True (after casting)' . PHP_EOL;
		$found = true;
	}
	if ($value === true) {
		echo 'True (same class)' . PHP_EOL;
		$found = true;
	}
	if (!$found) {
		echo 'Not found' . PHP_EOL;
		$found = true;
	}
}

////////////////////////////////////////////////////////

// TODO: SEGUE ALTRO CORDICE PER CUI EFFETTUARE UN UN TEST SU FILE

////////////////////////////////////////

// USAGE: string mb_convert_encoding ( string $str , string $to_encoding [, mixed $from_encoding = mb_internal_encoding() ] )
// $str = mb_convert_encoding ($str, 'UTF-8', 'auto');
// ... o in alternativa ...
// $str = utf8_encode($str);
//////////////////////////////////////

function isUTF8($str) {
	if ($str === mb_convert_encoding(mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')) {
		return true;
	} else {
		return false;
	}
}

function detectUTF8($string) {
	// 	USAGE:
	// 	if(detectUTF8($note)){
	// 		$str=str_replace("\xE2\x82\xAC","&euro;",$str);
	// 		$str=iconv("UTF-8","ISO-8859-1//TRANSLIT",$str);
	// 		$str=str_replace("&euro;","\x80",$str);
	// 	}

	return preg_match(
		'%(?:
        [\xC2-\xDF][\x80-\xBF]       		 # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]          # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}   # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]          # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}   	 # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}           # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    	 # plane 16
        )+%xs',
		$string
	);
}
