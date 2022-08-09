<?php

namespace Iubar\Net;

/**
 *
 * This script validates an e-mail adress using getmxrr and fsockopen
 * 1. it validates the syntax of the address.
 * 2. get MX records by hostname
 * 3. connect mail server and verify mailbox(using smtp command RCTP TO:<email>)
 * When the function "validate_email([email])" fails connecting the mail server with the highest priority in the MX record it will continue with the second mail server and so on..
 * The function "validate_email([email])" returns 0 when it failes one the 3 steps above, it will return 1 otherwise
 * Casi di test: http://markonphp.com/properly-validate-email-address-php/#sol2
 * Email List Hygiene: https://www.xverify.com
 *
 * @see http://www.samlogic.net/articles/smtp-commands-reference.htm
 * @author Borgo
 *
 */
class EmailValidator {
	public function isEmailValid($email) {
		$mailparts = explode('@', $email);
		if (!isset($mailparts[1])) {
			return false;
		}

		// validate email address syntax
		$exp = "/^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/i";

		$b_valid_syntax = preg_match($exp, $email);
		if (!$b_valid_syntax) {
			return false;
		}
		return true;
	}

	public function validate($email, $check_mx = true, $check_mailbox = false) {
		$is_valid = true;

		if (!$this->isEmailValid($email)) {
			return false;
		}

		$mailparts = explode('@', $email);
		$hostname = $mailparts[1];

		// get mx addresses by getmxrr
		$mx_records = null;
		$mx_weight = null;
		if ($check_mx) {
			$b_mx_avail = getmxrr($hostname, $mx_records, $mx_weight); // Get MX records corresponding to a given Internet host name
			// oppure  return checkdnsrr($hostname, 'MX'); // Check DNS records corresponding to a given Internet host name or IP address
			if (!$b_mx_avail) {
				return false;
			}
		}

		if ($check_mailbox) {
			$is_valid = null; // importante in caso il risulato non determinabile ovvero non sia nè true nè false

			// copy mx records and weight into array $mxs
			$mxs = [];

			for ($i = 0; $i < count($mx_records); $i++) {
				$mxs[$mx_weight[$i]] = $mx_records[$i];
			}

			// sort array mxs to get servers with highest prio
			ksort($mxs, SORT_NUMERIC);
			reset($mxs);
			$b_server_found = 0;
			while ([$mx_weight, $mx_host] = each($mxs)) {
				if ($b_server_found == 0) {
					//try connection on port 25

					$fp = @fsockopen($mx_host, 25, $errno, $errstr, 2);
					if ($fp) {
						$ms_resp = '';
						// say HELO to mailserver (or EHLO)

						// @see https://www.abuseat.org/lookup.cgi?ip=217.133.38.27
						// @see https://www.abuseat.org/advanced.html
						$ms_resp .= $this->send_command($fp, 'HELO iubar.it');
						//

						//     							Something similar to the following should be returned:
						//     							250-mail.port25.com says hello
						//     							250-STARTTLS
						//     							250-ENHANCEDSTATUSCODES
						//     							250-PIPELINING
						//     							250-CHUNKING
						//     							250-8BITMIME
						//     							250-XACK
						//     							250-XMRG
						//     							250-SIZE 54525952
						//     							250-VERP
						//     							250 DSN

						// initialize sending mail
						$ms_resp .= $this->send_command($fp, 'MAIL FROM:<support@iubar.it>');

						// Example
						// MAIL FROM: <support@microsoft.com>
						// 250 2.1.0 MAIL ok

						// try receipent address, will return 250 when ok..
						// Now that the MAIL FROM command has been sent we can send the RCPT TO command.  This command tells the SMTP mail server to who the message should be sent.  This can be the same or different than the to header, which is the email address shown in the email client.
						$rcpt_text = $this->send_command($fp, 'RCPT TO:<' . $email . '>');
						$ms_resp .= $rcpt_text;

						// ...the "RCPT TO" command returns the message
						// 250 2.1.5 <support@microsoft.com> ok

						if (substr($rcpt_text, 0, 3) == '250') {
							$b_server_found = 1;
							$is_valid = true;
						} else {
							$regex1 = '/503(.*)(outside of|must precede)/i';
							$regex2 = '/(.*)(blocked|banned|host rejected)(.*)/i';
							$regex3 = '/(.*)(not exist|unknown|not found|(550(.*)5\.1\.1))(.*)/i';
							$regex4 = '/(.*)(greylisted)(.*)/i';
							if (preg_match($regex1, $rcpt_text, $matches)) {
								// $error = "SMTP authentication is enabled";
								// return $error;
								return $rcpt_text;
							} elseif (preg_match($regex2, $rcpt_text, $matches)) {
								return $rcpt_text;
							} elseif (preg_match($regex3, $rcpt_text, $matches)) {
								$is_valid = false;
							} elseif (preg_match($regex4, $rcpt_text, $matches)) {
								return $rcpt_text;
							} else {
								return $rcpt_text;
							}
						}

						// quit mail server connection
						$ms_resp .= $this->send_command($fp, 'QUIT');

						fclose($fp);
					}
				}
			}
		}

		return $is_valid;
	}

	private function send_command($fp, $out) {
		@fwrite($fp, $out . "\r\n");
		return $this->get_data($fp);
	}

	private function get_data($fp) {
		$s = '';
		stream_set_timeout($fp, 2);

		for ($i = 0; $i < 2; $i++) {
			$s .= fgets($fp, 1024);
		}

		return $s;
	}

	// http://php.net/manual/en/function.levenshtein.php
	// http://php.net/manual/en/function.similar-text.php
}

// Support windows platforms. Only for PHP < 5.3
// if (!function_exists ('getmxrr') ) {
//     function getmxrr($hostname, &$mxhosts, &$mxweight) {
//         if (!is_array ($mxhosts) ) {
//             $mxhosts = array ();
//         }

//         if (!empty ($hostname) ) {
//             $output = "";
//             @exec ("nslookup.exe -type=MX $hostname.", $output);
//             $imx=-1;

//             foreach ($output as $line) {
//                 $imx++;
//                 $parts = "";
//                 if (preg_match ("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts) ) {
//                     $mxweight[$imx] = $parts[1];
//                     $mxhosts[$imx] = $parts[2];
//                 }
//             }
//             return ($imx!=-1);
//         }
//         return false;
//     }
// }

// if (realpath(__FILE__) == realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])){
// 	$is_valid = validate_email("info@iubar.it", true);
// 	echo $is_valid;
// }
