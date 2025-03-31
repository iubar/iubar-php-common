<?php

/*
Note that fgetcsv() uses the system locale setting to make assumptions about character encoding.
So if you are trying to process a UTF-8 CSV file on an EUC-JP server (for example),
you will need to do something like this before you call fgetcsv():

setlocale(LC_ALL, 'it_IT.UTF8');

*/

// setlocale(LC_ALL, 'IT.UTF8');

namespace Iubar\Document;

use Iubar\Common\BaseClass;

class CsvUtil extends BaseClass {
	const BR = "<br/>\r\n";
	public static $MAX_LEN = 50000;

	public function __construct() {
		parent::__construct();
	}

	public static function get_row($filename, $row) {
		// read the whole row as a string
		$line = file($filename)[$row];
		return $line;
	}

	public static function dumpOnConsole($filename, $delim = ',') {
		$row = 1;
		if (($handle = fopen($filename, 'r')) !== false) {
			while (($data = fgetcsv($handle, CsvUtil::$MAX_LEN, $delim)) !== false) {
				$num = count($data);
				echo "$num fields in line " . $row . PHP_EOL;
				$row++;
				for ($c = 0; $c < $num; $c++) {
					echo "\t" . $data[$c] . PHP_EOL;
				}
			}
			fclose($handle);
		}
	}

	public static function get_csv_assoc(string $filename, string $delim = ',', int $start_from_line = 0): array {
		$row = 0;
		$dump = [];
		$headers = CsvUtil::get_csv_header($filename, $delim);
		$handle = fopen($filename, 'r');
		if ($handle !== false) {
			while (($data = fgetcsv($handle, CsvUtil::$MAX_LEN, $delim)) !== false) {
				if ($row >= $start_from_line) {
					$data_ass = [];
					$i = 0;
					foreach ($headers as $key) {
						if (isset($data[$i])) {
							$value = $data[$i];
							$data_ass[$key] = $value;
						} else {
							echo '--> VALUE NOT SET FOR KEY (header = ' .
								$key .
								' row = ' .
								$row .
								' columns = ' .
								$i .
								") (forse c'è un punto e virgola di troppo nell'intestazione del file ? oppure righe vuote in fondo al file ?)" .
								PHP_EOL; // note: columns starts from 0
							print_r($data);
							die('stop');
						}
						$i++;
					}
					$dump[] = $data_ass;
				} else {
					//echo "skipped " . PHP_EOL;
				}
				$row++;
			}
			fclose($handle);
		}
		return $dump;
	}

	public static function get_csv($filename, $delim = ',', $start_from_line = 0) {
		$row = 0;
		$dump = [];

		$handle = fopen($filename, 'r');

		if ($handle !== false) {
			while (($data = fgetcsv($handle, CsvUtil::$MAX_LEN, $delim)) !== false) {
				if ($row >= $start_from_line) {
					$dump[$row] = $data;
					//echo $data[1] . PHP_EOL;
				} else {
					//echo "skipped " . $data[1] . PHP_EOL;
				}
				$row++;
			}
			fclose($handle);
		}
		return $dump;
	}

	public static function get_csv_header($filename, $delim = ',', $enclosure = '"') {
		$handle = fopen($filename, 'r');
		if ($handle !== false) {
			while (($data = fgetcsv($handle, CsvUtil::$MAX_LEN, $delim, $enclosure)) !== false) { 
					return $data;  // restituisco la riga 0
			}
			fclose($handle);
		}
		return null;
	}

	public static function put_csv($filename, $list, $delim = ',') {
		// SEE: http://www.php.net/manual/en/function.fputcsv.php

		// 		$list = array (
		// 			'aaa,bbb,ccc,dddd',
		// 			'123,456,789',
		// 			'"aaa","bbb"'
		// 		);

		$fp = fopen($filename, 'wb+');
		foreach ($list as $line) {
			fputcsv($fp, explode($delim, $line), $delim);
		}
		fclose($fp);
	}

	public static function read_first_line($file) {
		// TODO: spostare in "common" package
		$f = fopen($file, 'r');
		$line = fgets($f);
		fclose($f);
		return $line;
	}

	public static function toUtf8($txt) {
		$txt = mb_convert_encoding($txt, 'UTF-8');
		$txt = trim($txt);
		return $txt;
	}

	public static function writeAssociativeArrayToCsv($out_file, $array) {
		// Presupposto: tutti gli elementi di $array, ovvero i record, devono avere lo stesso insieme di chiavi, anche quando a una chiave non corrisponde alcun valore
		$bytes = 0;
		if (count($array) > 1) {
			$NL = "\r\n";
			$csv_content = '';
			$sep = ', ';
			$first_record = $array[0];
			$keys = array_keys($first_record);
			foreach ($keys as $key) {
				$csv_content .= $key . $sep;
			}
			$csv_content .= $NL;

			foreach ($array as $line) {
				foreach ($line as $key => $value) {
					$csv_content .= $value . $sep;
				}
				$csv_content .= $NL;
			}
			$bytes = file_put_contents($out_file, $csv_content, FILE_APPEND);
		}
		return $bytes;
	}

	public static function arrayToCsvRow($array, $sep = ';') {
		$row = false;
		foreach ($array as $elem) {
			// rimuovi eventuale separatore
			$elem = str_replace($sep, '\\' . $sep, $elem);
			// escape degli apostrofi (?)

			if ($row === false) {
				$row = $elem;
			} else {
				$row = $row . $sep . $elem;
			}
		}
		return $row;
	}

	// if (!function_exists('str_getcsv')) {

	// function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null) {
	//   $temp=fopen("php://memory", "rw");
	//   fwrite($temp, $input);
	//   fseek($temp, 0);
	//   $r = array();
	//   while (($data = fgetcsv($temp, 4096, $delimiter, $enclosure)) !== false) {
	//     $r[] = $data;
	//   }
	//   fclose($temp);
	//   return $r;
	// }
	//}
} // end class

?>
