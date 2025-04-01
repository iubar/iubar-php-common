<?php

namespace Iubar\Misc;

/**
 * Encryption and Decryption Class
 *
 */
class Encryption {
	/**
	 * Cipher algorithm
	 *
	 * @var string
	 */
	const CIPHER = 'aes-256-cbc';

	/**
	 * Hash function
	 *
	 * @var string
	 */
	const HASH_FUNCTION = 'sha256';

	/**
	 * constructor for Encryption object.
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * A timing attack resistant comparison.
	 *
	 * @access private
	 * @static static method
	 * @param string $hmac The hmac from the ciphertext being decrypted.
	 * @param string $compare The comparison hmac.
	 * @return bool
	 * @see https://github.com/sarciszewski/php-future/blob/bd6c91fb924b2b35a3e4f4074a642868bd051baf/src/Security.php#L36
	 */
	public static function hashEquals(string $hmac, string $compare) {
		if (function_exists('hash_equals')) {
			return hash_equals($hmac, $compare);
		}

		// if hash_equals() is not available,
		// then use the following snippet.
		// It's equivalent to hash_equals() in PHP 5.6.
		$hashLength = mb_strlen($hmac, '8bit');
		$compareLength = mb_strlen($compare, '8bit');

		if ($hashLength !== $compareLength) {
			return false;
		}

		$result = 0;
		for ($i = 0; $i < $hashLength; $i++) {
			$result |= ord($hmac[$i]) ^ ord($compare[$i]);
		}

		return $result === 0;
	}
}
