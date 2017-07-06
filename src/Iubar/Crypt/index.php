<?php
use Iubar\Crypt\AesEcbPkcs5Padding;
use Iubar\Crypt\AesCbcNoPadding;

error_reporting(E_ERROR | E_PARSE);

// https://stackoverflow.com/questions/1220751/how-to-choose-an-aes-encryption-mode-cbc-ecb-ctr-ocb-cfb
// https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation
// https://it.wikipedia.org/wiki/Modalit%C3%A0_di_funzionamento_dei_cifrari_a_blocchi
// https://github.com/keel/aes-cross

require_once __DIR__ . '/../../../vendor/autoload.php';


// include_once("AesEcbPkcs5Padding.php");
// include_once("AesCbcNoPadding.php");
// include_once("AesCbcPkcs5Padding.php");


$key = "iubar67890123456";
$iv = "1010101010101010";
$plaintext = "prova";



// AES+ECB: risultato atteso "l8qTM6fR0twU17lGEvroDw=="

// $out = AES::encrypt($plaintext, $key);

echo PHP_EOL . '-----------' .  PHP_EOL;

if(true){ // OK
	echo "class AesEcbPkcs5Padding" . PHP_EOL;
	$out = AesEcbPkcs5Padding::encrypt($plaintext, $key);
	echo $out . PHP_EOL;
	$out = AesEcbPkcs5Padding::decrypt($out, $key);
	echo $out . PHP_EOL;
	echo '-----------' .  PHP_EOL;
}

if(true){
	echo "class AesCbcPkcs5Padding ... it uses openssl()" . PHP_EOL;
	$cbc = new AesCbcPkcs5Padding();
	$out = $cbc->encrypt($plaintext);
	echo $out . PHP_EOL;
	$out = $cbc->decrypt($out);
	echo $out . PHP_EOL;
	echo '-----------' .  PHP_EOL;
}

if(true){
	echo "class AesCbcNoPadding" . PHP_EOL;
	$aes = new AesCbcNoPadding($key);
	$out = $aes->encrypt($plaintext, $iv);
	echo $out . PHP_EOL;
	$out = $aes->decrypt($out, $key, $iv);
	echo $out . PHP_EOL;
	echo '-----------' .  PHP_EOL;
}


// IV
// https://en.wikipedia.org/wiki/Initialization_vector
// https://stackoverflow.com/questions/8804574/aes-encryption-how-to-transport-iv
// https://security.stackexchange.com/questions/17044/when-using-aes-and-cbc-is-it-necessary-to-keep-the-iv-secret


