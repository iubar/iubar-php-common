<?php

namespace Iubar\Common;

use Iubar\Common\BaseClass;
use Iubar\Common\Formatter;

class Validator extends BaseClass {
	// RIFERIMENTI http://www.phpro.org/tutorials/Introduction-to-PHP-Regex.html
	// VEDI ANCHE: http://php.net/manual/it/function.utf8-decode.php

	// http://www.i18nqa.com/debug/utf8-debug.html
	// http://www.utf8-chartable.de/unicode-utf8-table.pl?unicodeinhtml=dec&htmlent=1
	// http://www.utf8-chartable.de/

	public static string $regex_only_words = "/^[a-zA-Z]*$/";
	public static string $regex_only_numbers = "/^[0-9]*$/";

	public static string $regex_phrase_of_words = "/^[A-Za-z]+(\\s[A-Za-zàèìòù`´'@\.]+)*$/";
	// La stringa precedente non è valida per UTF8

	public static string $regex_phrase_of_number = "/^[0-9]+(\\s[0-9]+)*$/";
	public static string $regex_phone_number = "/^[\+0-9]+(\\s[0-9]+)*$/"; // a differenza di $regex_phrase_of_number, prevede che un numero telefonico possa iniziare con il carattere '+'

	// OLD : public static string $regex_email = "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,10})$/i";
	public static string $regex_email = '/^[a-z0-9_\.-]+@[\da-z\.-]+\.[a-z\.]{2,10}$/i';

	// Attenzione: La provincia di Roma potrebbe essere indicata con la vecchia dicitura (ROMA)
	public static string $regex_provincia1 = '/[\\(]([A-Z]{2})[\\)]/';
	public static string $regex_provincia2 = "/[\\s]([A-Z]{2})$/";
	public static string $regex_provincia3 = '/[\\s]([A-Z]{2})[\\s]/';
	public static string $regex_provincia4 = '/[\\s\\S]([A-Z]{2})[\\s\\S]/';

	public static array $toponimi = [
		'via',
		'v.',
		'viale',
		'v.le',
		'piazza',
		'piazzale',
		'piazzetta',
		'p.zza',
		'p.za',
		'p.le',
		'p.tta',
		'banchina',
		'strada',
		'str.',
		'st.',
		'vicolo',
		'arco',
		'largo',
		'l.go',
		'corso',
		'cso',
		'c.so',
		'galleria',
		'borgo',
		'passo',
		'località'
	];

	public function __construct() {
		parent::__construct();
	}

	public function setLocaleIt() : void {
		$locale_array = ['it_IT.UTF-8', 'it_IT@euro', 'it_IT', 'italian'];
		setlocale(LC_ALL, $locale_array);
	}

	public static function getUtf8RegExPhraseOfWords() {
		// USAGE: non è obbligatorio ma sarebbe buona regola invocare
		// Valdiator::setLocaleIt() prima di usare qualunque metodo di questa classe

		// TEORIA:
		//
		// Le due seguenti soluzioni sono alternative:
		//
		// $regex_phrase_of_words1 = "/^[A-Za-z]+([\s\x{00E0}A-Za-z]+)*$/u"; 	// "à" in Unicode vale U+00E0)
		//
		// Nota che il flag 'u' al termine della precedente espressione è obbligatorio poichè uso \x
		// Inoltre è opportuno verificare il comportamento di qualsiasi espressione quando utilizzo il flag 'u'.
		// Infatti quando l'espressione con il flag 'u' è impiegata per eseguire il parse di una stringa ASCII ha causato dei problemi durante i test da me condotti.
		//
		// $regex_phrase_of_words2 = "/^[A-Za-z]+([\s" . chr(0xc3) . chr(0xa0) . "A-Za-z]+)*$/"; 	// Qui utilizzo la notazione esadecimale del caratter "à", ovvero "c3 a0"
		// Nota che in quest'ultima regex non è necessario usare il flag finale "u"

		$a = chr(0xc3) . chr(0xa0); //  'à' c3 a0
		$e1 = chr(0xc3) . chr(0xa8); //  'è' c3 a8
		$e2 = chr(0xc3) . chr(0xa9); //  'é' c3 a9
		$i = chr(0xc3) . chr(0xac); //  'ì' c3 ac
		$o = chr(0xc3) . chr(0xb2); //  'ò' c3 b2
		$u = chr(0xc3) . chr(0xb9); //  'ù' c3 b9
		$eur = chr(0xe2) . chr(0x82) . chr(0xac); // '€' e2 82 ac
		$array = [$a, $e1, $e2, $i, $o, $u, $eur];
		$regex_phrase_of_words_utf8 = '/^[A-Za-z]+([\s';
		foreach ($array as $c) {
			$regex_phrase_of_words_utf8 .= $c;
		}
		$regex_phrase_of_words_utf8 .= '@';
		$regex_phrase_of_words_utf8 .= '\.';
		$regex_phrase_of_words_utf8 .= '`';
		$regex_phrase_of_words_utf8 .= '´';
		$regex_phrase_of_words_utf8 .= "'";
		$regex_phrase_of_words_utf8 .= "'A-Za-z]+)*$/";
		//echo "getUtf8RegExPhraseOfWords() returns " . $regex_phrase_of_words_utf8 . PHP_EOL;
		return $regex_phrase_of_words_utf8;
	}

	public static function isAValidSelectQuery(string $query) {
		$commands = ['SELECT '];
		return self::isAValidQuery($query, $commands);
	}
	public static function isAValidDmlQuery(string $query) {
		$commands = ['INSERT ', 'UPDATE ', 'SELECT ', 'DELETE '];
		return self::isAValidQuery($query, $commands);
	}
	private static function isAValidQuery(string $query, array $commands) {
		$b = false;

		$len = strlen($query);
		if ($len > 0) {
			$subqueries = explode(';', $query);
			foreach ($subqueries as $subquery) {
				$subquery = trim($subquery);
				if ($subquery != '') {
					// Prima condizione: il comando SQL deve essere tra quelli previsti
					foreach ($commands as $command) {
						$pos = strpos(strtolower($subquery), strtolower($command));
						if ($pos !== false) {
							$b = true;
							break;
						}
					}

					// Seconda condizione: le parentesi devo essere bilanciate
					if ($b) {
						$b = Validator::isBalanced($subquery);
						if (!$b) {
							die("Non bilanciate: $subquery" . "\r\n");
						}
					}
				}
			}
		}
		return $b;
	}

	public static function isAValidCf(string $cf) : bool {
		$b = false;
		$cf = trim($cf);
		$len = strlen($cf);
		if ($len == 16) {
			$b = true;
		}
		return $b;
	}

	public static function isAValidPiva(string $piva) : bool {
		$b = false;
		$piva = trim($piva);
		$len = strlen($piva);
		if ($len == 11) {
			$b = Validator::isNumeric($piva);
		}
		return $b;
	}

	public static function isAValidEmail(string $email) : bool {
		$b = false;
		$email = Formatter::cleanEmail($email);
		$b = preg_match(Validator::$regex_email, $email, $matches);
		// 			if(isset($matches[0][0])){			// TODO: verificare se corretto
		// 				$email2 = $matches[0][0];
		// 				if($email2 !="" ){
		// 					$b = true;
		// 				}
		// 			}
		return $b;
	}

	public static function isAValidMySqlDate(string $txt): bool  {
		$b = false;
		// $now = date("Y-m-d H:i:s")
		// $mysqltime = date("Y-m-d H:i:s", $phptime); // http://php.net/manual/en/function.time.php
		$b = preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $txt);
		return $b;
	}

	public static function isPec($email) {
		$b = false;
		$email = Formatter::cleanEmail($email);
		$patterns = ['postecert.it', 'cert.cna.it', 'pecaruba.it', 'legalmail.it', 'pec.it', 'pec.com', 'consulentidellavoropec.it', 'actaliscertymail.it', 'pec.poste.it', '@pec.'];
		foreach ($patterns as $pattern) {
			$pos = strpos($email, $pattern);
			if ($pos !== false) {
				$b = true;
				break;
			}
		}
		return $b;
	}

	public static function isAlphabetical(string $str): bool  {
		// TODO: testare il metodo con la stringa "Cantù" quando questa proviene da file in formato ANSI o da file in formato UTF-8. Sembrano esserci differenze

		$b = false;
		$enc = mb_detect_encoding($str);
		$str = trim($str);
		$regex = '';
		if (true) {
			// if($enc=="UTF-8"){
			$regex = Validator::getUtf8RegExPhraseOfWords(); // sembra funzionare indipendentemente dall'encoding
		} else {
			$regex = Validator::$regex_phrase_of_words;
		}

		$b = preg_match($regex, $str, $matches);

		// 			if(isset($matches[0][0])){						// TODO: verificare se corretto
		// 				$str2 = $matches[0][0];
		// 				if($str2 != ""){
		// 					$b = true;
		// 				}
		// 			}

		return $b;
	}

	public static function isNumeric(string $str) : bool {
		$b = false;
		$str = trim($str);
		$b = preg_match(Validator::$regex_phrase_of_number, $str, $matches);
		// 			if(isset($matches[0][0])){						// TODO: verificare se corretto
		// 				$str2 = $matches[0][0];
		// 				if($str2 != ""){
		// 					$b = true;
		// 				}
		// 			}

		return $b;
	}

	public static function isAValidCap(string $cap): bool  {
		$b = false;
		$cap = trim($cap);
		$len = strlen($cap);
		if ($len == 5) {
			$b = Validator::isNumeric($cap);
		}
		return $b;
	}

	public static function isAValidPhoneNum(string $tel) : bool {
		// TODO: prevedere una lunghezza minima per la stringa, almeno 3
		$b = false;
		$tel = Formatter::cleanPhoneNum($tel);
		$b = preg_match(Validator::$regex_phone_number, $tel, $matches);
		// 			if(isset($matches[0][0])){					// TODO: verificare se corretto
		// 				$tel2 = $matches[0][0];
		// 				if($tel2 != ""){
		// 					$b = true;
		// 				}
		// 			}

		if ($b) {
			$tel2 = str_replace(' ', '', $tel); // rimuovo gli ulteriori spazi singoli lasciati inalterati da cleanPhoneNum()
			if (strlen($tel2) > 15) {
				$b = false; // $b è false se la lunghezza del numero è maggiore di 15 caratteri
			}
		}
		return $b;
	}

	public static function demoRegEx() :void {
		// preg_match("/^[A-Za-z]+(\s[A-Za-z]+)*$/", $nome_cognome, $matches2);		// match any words with a space between
		// preg_match("/^[A-Z][a-z]+(\s[A-Z]a-z]+)*$/", $nome_cognome, $matches2);	// match any camel-case words with a space between
		// preg_match("/^[A-Z]+(\s[A-Z]+)*$/", $nome_cognome, $matches3);			// match any uppercase words with a space between

		$text1 = 'pippo pluto e paperino';
		$text2 = '0721 800000';
		$text3 = 'borgogelli@tim.it';
		$text4 = ' borgogelli@tim.it ';

		$regex_dummy = '/<h2>(.*?)<\\/h2>/';

		$text100 = '<h2>Pippo Calogero</h2>';
		$text100 = ' ((VE) d';
		$text101 = 'VE';
		$text102 = ' VE';
		$text103 = ' VE ';
		$text120 = '&nbsp;035731512';

		// $matches = $this->test(Validator::$regex_phrase_of_words, $text1);
		// $matches = $this->test(Validator::$regex_phrase_of_number, $text2);
		// $matches = $this->test(Validator::$regex_email, $text3);

		$matches = self::test(Validator::$regex_provincia1, $text100);
		$matches = self::test(Validator::$regex_provincia2, $text101);
		$matches = self::test(Validator::$regex_provincia2, $text102);
		$matches = self::test(Validator::$regex_provincia2, $text103);
	}

	private static function test(string $regex, string $text) : array {
		$NL = "\r\n";
		echo 'Text is ' . $text . ' and regex is ' . $regex . $NL;
		preg_match_all($regex, $text, $matches);
		print_r($matches);
		echo $NL . $NL . $NL;
		return $matches;
	}

	public static function isBalanced(string $s) : bool {
		// Determine if there is an equal number of parentheses
		// and if they balance logically, i.e.
		// ()()) = Bad (trailing ")")
		// (())()() = GOOD
		// )()()(()) = BAD (leading ")")

		// Keep track of number of open parens
		static $open = 0;
		// Make sure start & end chars are not incorrect
		if (substr($s, 0, 1) == ')' || substr($s, -1, 1) == '(') {
			return false;
		}
		// Loop through each char
		if (is_array($s)) {
			for ($i = 0; $i < count($s); $i++) {
				if (substr($s, $i, 1) == '(') {
					// Increase the open count
					$open++;
					echo "Open $open \n";
				} elseif (substr($s, $i, 1) == ')') {
					// If open goes below zero, there's an invalid closing paren
					if ($open < 0) {
						return false;
					}
					// Decrease the open count
					$open--;
					echo "Closed $open \n";
				}
			}
		}

		if ($open != 0) {
			return false;
		}

		return true;
	}

	public static function testIsBalanced() : void {
		$tests = [
			'(())' => '' /* should pass */,
			')()()' => '' /* should fail - leading close */,
			'()()(' => '' /* should fail - trailing open */,
			'()()())()()' => '' /* should fail - errant ")" in middle */
		];
		foreach ($tests as $k => $v) {
			$tests[$k] = Validator::isBalanced($k) ? 'PASS' : 'FAIL';
		}
		var_dump($tests);
	}
} // end class
