<?php

namespace Iubar\Common;

class NetUtil {
	
	public static function getNetmaskFromIp($ip) { // TODO: metodo non corretto valido solo per indirizzi di tipo "C"
		$arrStr = explode(".", $ip);
		$net_addr = $arrStr[0] . "." . $arrStr[1] . "." . $arrStr[2] . "." . "0";
		return $net_addr;
	}
	
	public static function isIpValid($host){
		$b = false;
		$ip = gethostbyname($host);
		if(ip2long($ip) == -1 || ($ip == gethostbyaddr($ip) && preg_match("/.*\.[a-zA-Z]{2,3}$/",$host) == 0) ) {
			//echo 'Error, incorrect host or ip';
			$b = false;
		} else {
			//echo 'Ok';
			$b = true;
		}
		return $b;
	}
	
	public static function isInternetAvailable(){
		$connected = @fsockopen("www.iubar.it", 80);
		//website, port  (try 80 or 443)
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}else{
			$is_conn = false; //action in connection failure
		}
		return $is_conn;
	
	}
	
	public static function getIp() {
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} elseif (isset($_SERVER['REMOTE_HOST'])) {
			$ip = $_SERVER['REMOTE_HOST'];
		} elseif (isset($_SERVER['CLIENTNAME'])) {
			$clientname = $_SERVER['CLIENTNAME'];
			$ip = gethostbyname($clientname);
		} else {
			$ip = "Sconosciuto";
		}
	
		//$ip = gethostbyname('www.example.com');
		//$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
		return $ip;
	}
	
	/**
	 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
	 * array containing the HTTP server response header fields and content.
	 */
	public static function get_web_page( $url ) {
	
	    /*
	     USAGE:
	
	     $result = get_web_page( $url );
	
	     if ( $result['errno'] != 0 )
	         ... error: bad url, timeout, redirect loop ...
	
	         if ( $result['http_code'] != 200 )
	             ... error: no page, no permissions, no service ...
	
	             $page = $result['content'];
	             */
	
	         $options = array(
	             CURLOPT_RETURNTRANSFER => true,     // return web page
	             CURLOPT_HEADER         => false,    // don't return headers
	             CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	             CURLOPT_ENCODING       => "",       // handle all encodings
	             CURLOPT_USERAGENT      => "spider", // who am i
	             CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	             CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
	             CURLOPT_TIMEOUT        => 120,      // timeout on response
	             CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	         );
	
	         $ch      = curl_init( $url );
	         curl_setopt_array( $ch, $options );
	         $content = curl_exec( $ch );
	         $err     = curl_errno( $ch );
	         $errmsg  = curl_error( $ch );
	         $header  = curl_getinfo( $ch );
	         curl_close( $ch );
	
	         $header['errno']   = $err;
	         $header['errmsg']  = $errmsg;
	         $header['content'] = $content;
	         return $header;
	}	
	
	
		
} // end class
