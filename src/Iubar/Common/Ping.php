<?php
namespace Iubar\Common;

class Ping {
	public function __construct() {
	}

	public function ping($hostname) {
		// Making the package
		$type = "\x08";
		$code = "\x00";
		$checksum = "\x00\x00";
		$identifier = "\x00\x00";
		$seqNumber = "\x00\x00";
		$data = 'Scarface';
		$package = $type . $code . $checksum . $identifier . $seqNumber . $data;

		$checksum = $this->icmpChecksum($package); // Calculate the checksum
		// echo decbin(ord($checksum[0])) ." ". decbin(ord($checksum[1]));

		$package = $type . $code . $checksum . $identifier . $seqNumber . $data;

		// And off to the sockets
		$socket = socket_create(AF_INET, SOCK_RAW, 1);
		$b = @socket_connect($socket, $hostname, null);
		if ($b) {
			// If you're using below PHP 5, see the manual for the microtime_float
			// function. Instead of just using the microtime() function.
			$startTime = microtime(true);
			socket_send($socket, $package, strLen($package), 0);
			if (socket_read($socket, 255)) {
				echo round(microtime(true) - $startTime, 4) . ' seconds';
			}
			socket_close($socket);
		}
	}

	// Checksum calculation function
	private function icmpChecksum($data) {
		if (strlen($data) % 2) {
			$data .= "\x00";
		}

		$bit = unpack('n*', $data);
		$sum = array_sum($bit);

		while ($sum >> 16) {
			$sum = ($sum >> 16) + ($sum & 0xffff);
		}

		return pack('n*', ~$sum);
	}
} // end class
