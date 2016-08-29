<?php

namespace Iubar\Common;

use Iubar\Common\BaseClass;

require 'BaseClass.php';

class HostingUtil extends BaseClass {

	public $iubar_lan = "192.168.0.";
	public $iubar_wan = "82.91.10.178";
	public $iubar_it = "62.149.128.166";
	
	public $www_iubar_it = "62.149.140.104";
	public $pagheopen_it = "62.149.128.163";
	
	public $www_pagheopen_it = "62.149.140.206";
	public $www_pagheopen_it_out =  "62.149.141.";
	
	public $www_fiscoopen_it;
	public $www_fiscoopen_it2;
	
	public $friend_ips = NULL;

	public function __construct(){
		parent::__construct();
		$this->friend_ips = array($iubar_lan, $iubar_wan, $iubar_it, $www_iubar_it, $pagheopen_it, $www_pagheopen_it, $www_pagheopen_it_out);	
	}

	public function isIpAuthorized(){
		$b = false;
		$client_ip = $this->getClientIp();
		// echo $ip;
		if(NetUtil::isIpValid($client_ip)){
			foreach($this->friend_ips as $valid_ip){
				$pos = strpos($client_ip, $valid_ip);
				if ($pos !== false) {
					$b=true;
					break;
				}
			}
		}else{
			echo "ERROR: " . $client_ip . " it's not a valid ip." . "\n";
		}
		return $b;
	}

	public function getClientIp() {
		$ip = NULL;
	    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
	        if (array_key_exists($key, $_SERVER) === true) {
	            foreach (explode(',', $_SERVER[$key]) as $address) {
	                if (filter_var($address, FILTER_VALIDATE_IP) !== false) {
	                    $ip = $address;
	                }
	            }
	        }
	    }
	    return $ip;
	}


	public function getServerName(){
		return $_SERVER["SERVER_NAME"];
	}
	
	public function getServerIpAddr(){
		$ip = NULL;
		if (isset($_SERVER["SERVER_ADDR"])) {
			$ip = $_SERVER["SERVER_ADDR"];
		}
		if($ip==""){
			$ip = gethostbyname($_SERVER['HTTP_HOST']);
		}
		return $ip;
	}
	
	public function getServerSoftware(){
		return $_SERVER["SERVER_SOFTWARE"];
	}
	
	public function isThisScriptOnAWebServer(){
		$from_web = true;
		$root = $_SERVER['DOCUMENT_ROOT']; // /web/htdocs/www.iubar.it/home/
		if($root==""){
			$from_web = false;
		}
		return $from_web;
	}

	 public function isTheServerReachable($hostname){
		$socket = socket_create(AF_INET, SOCK_RAW, 1);
		$b = @socket_connect($socket, $hostname, null);
		if(!$b){
			$text="Unable to connect<pre>".socket_strerror(socket_last_error())."</pre>";
			echo $text;			
		}else{
			// $text="Connection successful on IP $address, port $port";
			// echo $text;
			socket_close($socket);
		}
		return $b;
		}
		
	  public static function isTheHostConnectedToTheWeb() {
	    //check to see if the local machine is connected to the web
	    //uses sockets to open a connection
	  	$is_conn = false;
	    $connected = @fsockopen("www.iubar.it", 80);
	    if ($connected){
	        $is_conn = true;
	        fclose($connected);
	    }
	    return $is_conn;
	}

// // METODO DEPRECATO: 1) perchè bloccante 2) perchè non è chiaro cosa faccia
// private function download($remoteFile, $localFile, $overwrite=false){

// 	//echo "remoteFile: " . $remoteFile . $nl;
// 	//echo "localFile: " . $localFile . $nl;
// 	// $logger->log("remote file: " . $remoteFile);
// 	// $logger->log("local file: " . $localFile);
// 	//$logger->log("overwrite: " . $overwrite);

// 	// Time to cache in hours
// 	$cacheTime = 24;

// 	// Connection time out
// 	$connTimeout = 10;

// 	//if(file_exists($localFile) && (time() - ($cacheTime * 3600) < filemtime($localFile))){

// 		 //readfile($localFile); // Reads a file and writes it to the output buffer.

// 	//}else{
// 		 $url = parse_url($remoteFile);
// 		 $host = $url['host'];
// 		 $path = isset($url['path']) ? $url['path'] : '/';

// 		 if (isset($url['query'])) {
// 			  $path .= '?' . $url['query'];
// 		 }

// 		 $port = isset($url['port']) ? $url['port'] : '80';

// 		 $fp = @fsockopen($host, '80', $errno, $errstr, $connTimeout );

// 		 if(!$fp){
// 			  // If connection failed, return the cached file
// 			  //if(file_exists($localFile)){
// 				//   readfile($localFile);
// 			  // }
// 			 // $logger->log("socket error: " . $errstr);
// 		 }else{
// 			  // Header Info
// 			  $header = "GET $path HTTP/1.0\r\n";
// 			  $header .= "Host: $host\r\n";
// 			  $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6\r\n";
// 			  $header .= "Accept: */*\r\n";
// 			  $header .= "Accept-Language: en-us,en;q=0.5\r\n";
// 			  $header .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
// 			  $header .= "Keep-Alive: 300\r\n";
// 			  $header .= "Connection: keep-alive\r\n";
// 			  $header .= "Referer: http://$host\r\n\r\n";

// 			  $response = '';
// 			  fputs($fp, $header);
// 			  // Get the file content
// 			  while($line = fread($fp, 4096)){
// 				   $response .= $line;
// 			  }
// 			  fclose( $fp );

// 			  // Remove Header Info
// 			  $pos = strpos($response, "\r\n\r\n");
// 			  $response = substr($response, $pos + 4);

// 			 /////echo $response;


// 			  // Save the file content

// 			  $exists = false;
// 			  $writable = false;
// 			  if(file_exists($localFile)){
// 			  	//$logger->log("local file exists");
// 			  	$exists = true;
// 			  	if(is_writable($localFile)) {
// 			  		$writable = true;
// 			  	}else{
// 			  		//$logger->log("local file is not writable");
// 			  	}
// 			  }else{
// 			  		//$logger->log("local file doesn't exist");
// 			  }

// 			  $check1 = (!$exists) || ($overwrite);
// 			  $check2 = (!$exists) || ($writable);
// 			  $continue = $check1 && $check2;

// 			  if($continue){
// 				   // Create the file, if it doesn't exist already
// 				   $fp = fopen($localFile, 'wb+');
// 				   if($fp){
// 				  	 	fwrite($fp, $response);
// 				   		fclose($fp);
// 				   		//$logger->log("file downloaded");
// 				   }
// 			  }else{
// 			  	//$logger->log("download skipped");
// 			  }
// 		 }

// 	//}
// 	//$logger->logSeparator();
// }


public static function printStreamMetadata($url) {
      if (!($fp = @fopen($url, 'r'))){
			trigger_error("Unable to open URL ($url)", E_USER_ERROR);
		}      	
      $meta = stream_get_meta_data($fp);
          foreach(array_keys($meta) as $h){
              $v = $meta[$h];
              echo "".$h.": ".$v."<br/>";
              if(is_array($v)){
                  foreach(array_keys($v) as $hh){
                      $vv = $v[$hh];
                      echo "_".$hh.": ".$vv."<br/>";
                  }
              }
          }
      fclose($fp);
}

} // end class


?>