<?php

namespace Iubar\Net;

// This script validates an e-mail adress using getmxrr and fsockopen

// 1. it validates the syntax of the address.
// 2. get MX records by hostname
// 3. connect mail server and verify mailbox(using smtp command RCTP TO:<email>)

// When the function "validate_email([email])" fails connecting the mail server with the highest priority in the MX record it will continue with the second mail server and so on..

// The function "validate_email([email])" returns 0 when it failes one the 3 steps above, it will return 1 otherwise

// Grtz Lennart Poot

// 2015-06-23 Borgo: modificato il codice originale

// Casi di test: http://markonphp.com/properly-validate-email-address-php/#sol2

class EmailValidator {

	public function validate($email, $check_mx=true, $check_mailbox=false){
    	$is_valid = false;

    	$mailparts=explode("@",$email);
    	$hostname = $mailparts[1];

    	// validate email address syntax
    	$exp = "/^[a-z\'0-9]+([._-][a-z\'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/i";

    	$b_valid_syntax=preg_match($exp, $email);

    	// get mx addresses by getmxrr
    	$b_mx_avail = true;
    	if($check_mx){
    		$b_mx_avail = getmxrr( $hostname, $mx_records, $mx_weight );
    	}
    	$b_server_found=0;

    	if($b_valid_syntax && $b_mx_avail){

    		if(!$check_mailbox){
    			$is_valid = true;
    		}else{

    		// copy mx records and weight into array $mxs
    		$mxs=array();

    		for($i=0;$i<count($mx_records);$i++){
    			$mxs[$mx_weight[$i]]=$mx_records[$i];
    		}

    		// sort array mxs to get servers with highest prio
    		ksort ($mxs, SORT_NUMERIC );
    		reset ($mxs);

    		while (list ($mx_weight, $mx_host) = each ($mxs) ) {
    			if($b_server_found == 0){

    				//try connection on port 25
    				$fp = @fsockopen($mx_host,25, $errno, $errstr, 2);
    				if($fp){
    					$ms_resp="";
    					// say HELO to mailserver
    					$ms_resp.= $this->send_command($fp, "HELO microsoft.com");

    					// initialize sending mail
    					$ms_resp.= $this->send_command($fp, "MAIL FROM:<support@microsoft.com>");

    					// try receipent address, will return 250 when ok..
    					$rcpt_text= $this->send_command($fp, "RCPT TO:<".$email.">");
    					$ms_resp.=$rcpt_text;

    					if(substr( $rcpt_text, 0, 3) == "250"){
    						$b_server_found=1;
    						$is_valid = true;
    					}

    					// quit mail server connection
    					$ms_resp.= $this->send_command($fp, "QUIT");

    					fclose($fp);

    				}

    			}
    		}
    	}
    	}
    	return $is_valid;
    }

    private function send_command($fp, $out){
    	fwrite($fp, $out . "\r\n");
    	return $this->get_data($fp);
    }

    private function get_data($fp){
    	$s="";
    	stream_set_timeout($fp, 2);

    	for($i=0;$i<2;$i++)
    		$s.=fgets($fp, 1024);

    		return $s;
    }

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
