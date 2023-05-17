<?php
////////////////////////////////////////////////////////////////////

// REPLACEMENT CHARACTER (U+FFFD)
mb_substitute_character(0xfffd);

function replace_invalid_byte_sequence($str) {
	return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
}

function replace_invalid_byte_sequence2($str) {
	return htmlspecialchars_decode(htmlspecialchars($str, ENT_SUBSTITUTE, 'UTF-8'));
}
function replace_invalid_byte_sequence3($str) {
	return UConverter::transcode($str, 'UTF-8', 'UTF-8');
}

function replace_invalid_byte_sequence4($str) {
	return (new UConverter('UTF-8', 'UTF-8'))->convert($str);
}
function replace_invalid_byte_sequence5($str) {
	// REPLACEMENT CHARACTER (U+FFFD)
	$substitute = "\xEF\xBF\xBD";
	$regex = '/
      ([\x00-\x7F]                       #   U+0000 -   U+007F
      |[\xC2-\xDF][\x80-\xBF]            #   U+0080 -   U+07FF
      | \xE0[\xA0-\xBF][\x80-\xBF]       #   U+0800 -   U+0FFF
      |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} #   U+1000 -   U+CFFF
      | \xED[\x80-\x9F][\x80-\xBF]       #   U+D000 -   U+D7FF
      | \xF0[\x90-\xBF][\x80-\xBF]{2}    #  U+10000 -  U+3FFFF
      |[\xF1-\xF3][\x80-\xBF]{3}         #  U+40000 -  U+FFFFF
      | \xF4[\x80-\x8F][\x80-\xBF]{2})   # U+100000 - U+10FFFF
      |(\xE0[\xA0-\xBF]                  #   U+0800 -   U+0FFF (invalid)
      |[\xE1-\xEC\xEE\xEF][\x80-\xBF]    #   U+1000 -   U+CFFF (invalid)
      | \xED[\x80-\x9F]                  #   U+D000 -   U+D7FF (invalid)
      | \xF0[\x90-\xBF][\x80-\xBF]?      #  U+10000 -  U+3FFFF (invalid)
      |[\xF1-\xF3][\x80-\xBF]{1,2}       #  U+40000 -  U+FFFFF (invalid)
      | \xF4[\x80-\x8F][\x80-\xBF]?)     # U+100000 - U+10FFFF (invalid)
      |(.)                               # invalid 1-byte
    /xs';

	// $matches[1]: valid character
	// $matches[2]: invalid 3-byte or 4-byte character
	// $matches[3]: invalid 1-byte

	$ret = preg_replace_callback(
		$regex,
		function ($matches) use ($substitute) {
			if (isset($matches[2]) || isset($matches[3])) {
				return $substitute;
			}

			return $matches[1];
		},
		$str
	);

	return $ret;
}

function replace_invalid_byte_sequence6($str) {
	$size = strlen($str);
	$substitute = "\xEF\xBF\xBD";
	$ret = '';

	$pos = 0;
	$char = null;
	$char_size = null;
	$valid = null;

	while (utf8_get_next_char($str, $size, $pos, $char, $char_size, $valid)) {
		$ret .= $valid ? $char : $substitute;
	}

	return $ret;
}

function utf8_get_next_char($str, $str_size, &$pos, &$char, &$char_size, &$valid) {
	$valid = false;

	if ($str_size <= $pos) {
		return false;
	}

	if ($str[$pos] < "\x80") {
		$valid = true;
		$char_size = 1;
	} elseif ($str[$pos] < "\xC2") {
		$char_size = 1;
	} elseif ($str[$pos] < "\xE0") {
		if (!isset($str[$pos + 1]) || $str[$pos + 1] < "\x80" || "\xBF" < $str[$pos + 1]) {
			$char_size = 1;
		} else {
			$valid = true;
			$char_size = 2;
		}
	} elseif ($str[$pos] < "\xF0") {
		$left = "\xE0" === $str[$pos] ? "\xA0" : "\x80";
		$right = "\xED" === $str[$pos] ? "\x9F" : "\xBF";

		if (!isset($str[$pos + 1]) || $str[$pos + 1] < $left || $right < $str[$pos + 1]) {
			$char_size = 1;
		} elseif (!isset($str[$pos + 2]) || $str[$pos + 2] < "\x80" || "\xBF" < $str[$pos + 2]) {
			$char_size = 2;
		} else {
			$valid = true;
			$char_size = 3;
		}
	} elseif ($str[$pos] < "\xF5") {
		$left = "\xF0" === $str[$pos] ? "\x90" : "\x80";
		$right = "\xF4" === $str[$pos] ? "\x8F" : "\xBF";

		if (!isset($str[$pos + 1]) || $str[$pos + 1] < $left || $right < $str[$pos + 1]) {
			$char_size = 1;
		} elseif (!isset($str[$pos + 2]) || $str[$pos + 2] < "\x80" || "\xBF" < $str[$pos + 2]) {
			$char_size = 2;
		} elseif (!isset($str[$pos + 3]) || $str[$pos + 3] < "\x80" || "\xBF" < $str[$pos + 3]) {
			$char_size = 3;
		} else {
			$valid = true;
			$char_size = 4;
		}
	} else {
		$char_size = 1;
	}

	$char = substr($str, $pos, $char_size);
	$pos += $char_size;

	return true;
}

//////////////////////// TEST

$data = [
	// Table 3-8. Use of U+FFFD in UTF-8 Conversion
	// http://www.unicode.org/versions/Unicode6.1.0/ch03.pdf)
	"\x61" . "\xF1\x80\x80" . "\xE1\x80" . "\xC2" . "\x62" . "\x80" . "\x63" . "\x80" . "\xBF" . "\x64",

	// 'FULL MOON SYMBOL' (U+1F315) and invalid byte sequence
	"\xF0\x9F\x8C\x95" . "\xF0\x9F\x8C" . "\xF0\x9F\x8C"
];

$data2 = [
	// U+20AC
	"\xE2\x82\xAC" . "\xE2\x82\xAC" . "\xE2\x82\xAC",
	"\xE2\x82" . "\xE2\x82\xAC" . "\xE2\x82\xAC",

	// U+24B62
	"\xF0\xA4\xAD\xA2" . "\xF0\xA4\xAD\xA2" . "\xF0\xA4\xAD\xA2",
	"\xF0\xA4\xAD" . "\xF0\xA4\xAD\xA2" . "\xF0\xA4\xAD\xA2",
	"\xA4\xAD\xA2" . "\xF0\xA4\xAD\xA2" . "\xF0\xA4\xAD\xA2",

	// 'FULL MOON SYMBOL' (U+1F315)
	"\xF0\x9F\x8C\x95" . "\xF0\x9F\x8C",
	"\xF0\x9F\x8C\x95" . "\xF0\x9F\x8C" . "\xF0\x9F\x8C"
];

function run(array $callables, array $arguments) {
	return array_map(function ($callable) use ($arguments) {
		return array_map($callable, $arguments);
	}, $callables);
}

// RUN THE TEST

var_dump(
	run(
		[
			'replace_invalid_byte_sequence',
			'replace_invalid_byte_sequence2',
			'replace_invalid_byte_sequence3',
			'replace_invalid_byte_sequence4',
			'replace_invalid_byte_sequence5',
			'replace_invalid_byte_sequence6'
		],
		$data
	)
);

/////////////////////// BENCHMARK THE CODE

function timer(array $callables, array $arguments, $repeat = 10000) {
	$ret = [];
	$save = $repeat;

	foreach ($callables as $key => $callable) {
		$start = microtime(true);

		do {
			array_map($callable, $arguments);
		} while ($repeat -= 1);

		$stop = microtime(true);
		$ret[$key] = $stop - $start;
		$repeat = $save;
	}

	return $ret;
}

$functions = [
	'mb_convert_encoding()' => 'replace_invalid_byte_sequence',
	'htmlspecialchars()' => 'replace_invalid_byte_sequence2',
	'UConverter::transcode()' => 'replace_invalid_byte_sequence3',
	'UConverter::convert()' => 'replace_invalid_byte_sequence4',
	'preg_replace_callback()' => 'replace_invalid_byte_sequence5',
	'direct comparision' => 'replace_invalid_byte_sequence6'
];

foreach (timer($functions, $data) as $description => $time) {
	echo $description, PHP_EOL, $time, PHP_EOL;
}

$LINK_ICHARS_DOMAIN = (string) html_entity_decode(
	implode('', [
		// @TODO completing letters ...
		'&#x00E6;', // æ
		'&#x00C6;', // Æ
		'&#x00C0;', // À
		'&#x00E0;', // à
		'&#x00C1;', // Á
		'&#x00E1;', // á
		'&#x00C2;', // Â
		'&#x00E2;', // â
		'&#x00E5;', // å
		'&#x00C5;', // Å
		'&#x00E4;', // ä
		'&#x00C4;', // Ä
		'&#x00C7;', // Ç
		'&#x00E7;', // ç
		'&#x00D0;', // Ð
		'&#x00F0;', // ð
		'&#x00C8;', // È
		'&#x00E8;', // è
		'&#x00C9;', // É
		'&#x00E9;', // é
		'&#x00CA;', // Ê
		'&#x00EA;', // ê
		'&#x00CB;', // Ë
		'&#x00EB;', // ë
		'&#x00CE;', // Î
		'&#x00EE;', // î
		'&#x00CF;', // Ï
		'&#x00EF;', // ï
		'&#x00F8;', // ø
		'&#x00D8;', // Ø
		'&#x00F6;', // ö
		'&#x00D6;', // Ö
		'&#x00D4;', // Ô
		'&#x00F4;', // ô
		'&#x00D5;', // Õ
		'&#x00F5;', // õ
		'&#x0152;', // Œ
		'&#x0153;', // œ
		'&#x00FC;', // ü
		'&#x00DC;', // Ü
		'&#x00D9;', // Ù
		'&#x00F9;', // ù
		'&#x00DB;', // Û
		'&#x00FB;', // û
		'&#x0178;', // Ÿ
		'&#x00FF;', // ÿ
		'&#x00D1;', // Ñ
		'&#x00F1;', // ñ
		'&#x00FE;', // þ
		'&#x00DE;', // Þ
		'&#x00FD;', // ý
		'&#x00DD;', // Ý
		'&#x00BF;' // ¿
	]),
	ENT_QUOTES,
	'UTF-8'
);

