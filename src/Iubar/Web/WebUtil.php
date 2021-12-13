<?php

namespace Iubar\Web;

class WebUtil {
	
	public function __construct(){
		// nothing to do
	}
	
	public static function getClientIp() { // Solo per PHP >=5.3
	    $ip = getenv('HTTP_CLIENT_IP')?:
	    getenv('HTTP_X_FORWARDED_FOR')?:
	    getenv('HTTP_X_FORWARDED')?:
	    getenv('HTTP_FORWARDED_FOR')?:
	    getenv('HTTP_FORWARDED')?:
	    getenv('REMOTE_ADDR');
	
	    return $ip;
	}

/////////////////// URL
	
	public static function isUrl($str){
		$b = false;
		$regex = "((https?|ftp)\:\/\/)?"; // Scheme
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		$regex .= "(\:[0-9]{2,5})?"; // Port
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
		
		if(preg_match("/^$regex$/", $str)){
			$b = true;
		}
		return $b;
	}
	
	public static function isUrl2($str){
		if(filter_var($str, FILTER_VALIDATE_URL)){
			$b = true;
		}
		return $b;
	}
	
/////////////////// WEB PAGE
	
	public static function curPageURL() { // retruns the current page url
		
		// on IIS web server you should use $_SERVER['PATH_INFO']
		$pageURL = 'http';
		if(isset($_SERVER["HTTPS"])){
			if ($_SERVER["HTTPS"] == "on") {
				$pageURL .= "s";
			}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public static function curPageName() { // retruns the current page file name
		return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
	}	
	
	/////////////////// EXTRACT DATA

	public static function extractLinks($html){
		$links = array();
		$a_links = array();
		$img_links = array();
		
		$doc = new \DOMDocument();
		@$doc->loadHTML($html);
		
		$tags = $doc->getElementsByTagName('img');
		
// 		foreach ($tags as $tag) {
// 			echo $tag->getAttribute('src');
// 		}
		foreach($tags as $tag) {
			//LOOP THROUGH ATTRIBUTES FOR CURRENT LINK
			foreach($tag->attributes as $attributeName=>$attributeValue) {
				//IF CURRENT ATTRIBUTE CONTAINS THE WEBSITE ADDRESS
				if($attributeName == 'src') {
					// echo "image found: " . $attributeValue->value . PHP_EOL;
					$img_links[] = $attributeValue->value;
				}
			}
		}		
		
		$tags = $doc->getElementsByTagName('a');
		
// 		foreach ($tags as $tag) {
// 			echo $tag->getAttribute('href');
// 		}
		foreach($tags as $tag) {
			//LOOP THROUGH ATTRIBUTES FOR CURRENT LINK
			foreach($tag->attributes as $attributeName=>$attributeValue) {
				//IF CURRENT ATTRIBUTE CONTAINS THE WEBSITE ADDRESS
				if($attributeName == 'href') {
					$a_links[] = $attributeValue->value;
				}
			}
		}
		
		$links = array_merge($a_links, $img_links);
		return $links;
	}
	/////////////////// BROKEN LINKS CHECKER	
	
	public static function check_brokenlink($pageToCheck) { 
	// Attenzione il metodo verifica solo i link che sono attributo del tag <a>
	// (ad esempio non prende in considerazione l'attributo "src" del tag "img")
	
	//$pageToCheck    = $_GET['link'];
	
	$badLinks       = array();
	$goodLinks      = array();
	$changedLinks   = array();
	$badStatusCodes = array('308', '404');
		
	//INITIALIZE DOMDOCUMENT
	$domDoc = new \DOMDocument;
	$domDoc->preserveWhiteSpace = false;
	
	//IF THE PAGE BEING CHECKED LOADS
	if(@$domDoc->loadHTMLFile($pageToCheck)) { //note that errors are suppressed so DOMDocument doesn't complain about XHTML
		//LOOP THROUGH ANCHOR TAGS IN THE MAIN CONTENT AREA
		$pageLinks = $domDoc->getElementsByTagName('a');
		foreach($pageLinks as $currLink) {
			//LOOP THROUGH ATTRIBUTES FOR CURRENT LINK
			foreach($currLink->attributes as $attributeName=>$attributeValue) {
				//IF CURRENT ATTRIBUTE CONTAINS THE WEBSITE ADDRESS
				if($attributeName == 'href') {
					
					
					//IF CURRENT ATTRIBUTE CONTAINS THE WEBSITE ADDRESS
					if($attributeName == 'href') {
						//INITIALIZE CURL AND TEST THE LINK
						$ch = curl_init($attributeValue->value);
						curl_setopt($ch, CURLOPT_NOBODY, true);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						curl_exec($ch);
						$returnCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
						$finalURL   = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
						curl_close($ch);
					
						//TRACK THE RESPONSE
						if(in_array($returnCode, $badStatusCodes)) {
							$badLinks[]     = array('name'=>$currLink->nodeValue, 'link'=>$attributeValue->value);
						} elseif($finalURL != $attributeValue->value) {
							$changedLinks[] = array('name'=>$currLink->nodeValue, 'link'=>$attributeValue->value, 'newLink'=>$finalURL);
						} else {
							$goodLinks[]    = array('name'=>$currLink->nodeValue, 'link'=>$attributeValue->value);
						}
					}

					
				}
			}
		}
	}
	

		
	 //DISPLAY RESULTS
	 print '<h2>Bad Links</h2>';
	 print '<pre>' . print_r($badLinks, true) . '</pre>';
	 print '<h2>Changed Links</h2>';
	 print '<pre>' . print_r($changedLinks, true) . '</pre>';
	 print '<h2>Good Links</h2>';
	 print '<pre>' . print_r($goodLinks, true) . '</pre>';
		
	}
	
	public static function check_url($url) {
		// 	USAGE:
		// 		$check_url_status = WebUtil::check_url($url);
		// 		if ($check_url_status == '200') // I think you can also check for 301 and 302 status codes: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
		// 			echo "Link Works";
		// 		else
		// 			echo "Broken Link";
			
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		$headers = curl_getinfo($ch);
		curl_close($ch);
	
		return $headers['http_code'];
	}
	

	public static function check_url2($url) {
		// 	USAGE:
		// 		if (WebUtil::check_url2($url))
		// 			echo "Link Works";
		// 		else
		// 			echo "Broken Link";
				
		$headers = @get_headers( $url);
		$headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
		// print_r($headers);
		return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
	}
	
	public static function remoteFileExists($url) {
		
		// USAGE:
		// $exists = WebUtil::remoteFileExists('http://stackoverflow.com/favicon.ico');
		// if ($exists) {
		// 	echo 'file exists';
		// } else {
		// 	echo 'file does not exist';
		// }
		
		$curl = curl_init($url);
	
		//don't fetch the actual page, you only want to check the connection is ok
		curl_setopt($curl, CURLOPT_NOBODY, true);
	
		//do request
		$result = curl_exec($curl);
	
		$ret = false;
	
		//if request did not fail
		if ($result !== false) {
			//if request was ok, check response code
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
			if ($statusCode == 200) {
				$ret = true;
			}
		}
	
		curl_close($curl);
	
		return $ret;
	}
	
 
	public static function rel2abs($rel, $base){
 
		if (parse_url($rel, PHP_URL_SCHEME) != '')
			return ($rel);
	
			if ($rel[0] == '#' || $rel[0] == '?')
				return ($base . $rel);
	
                extract(parse_url($base));
                $host = null;
                $scheme = null;
	
				if(isset($path)){
		 
					$path = preg_replace('#/[^/]*$#', '', $path);
				}else{
					$path = '';
				}
	
		 
				if ($rel[0] == '/')
					$path = '';
	
			 
					$abs = '';
	
			 
					if (isset($user)) {
						$abs .= $user;
	
						/* password too? */
						if (isset($pass))
							$abs .= ':' . $pass;
	
							$abs .= '@';
					}
	
					$abs .= $host;
	
				 
					if (isset($port))
						$abs .= ':' . $port;
	
						$abs .= $path . '/' . $rel . (isset($query) ? '?' . $query : '');
	
						/* replace '//' or '/./' or '/foo/../' with '/' */
						$re = ['#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'];
						for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {
						}
	
		 
	
						return ($scheme . '://' . $abs);
	}
 
	
	public static function getRoot(){
		$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
		return $root;
	}
	
	public static function parseRoot($url){

		// $parsedUrl = parse_url('http://localhost/some/folder/containing/something/here/or/there');
		// $root = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/';
		// If you're also interested in other URL components prior to the path (e.g. credentials), you could also use strstr() on the full URL, with the "path" as the needle, e.g.		
		// $url = 'http://user:pass@localhost:80/some/folder/containing/something/here/or/there';
		$parsedUrl = parse_url($url);
		$root = strstr($url, $parsedUrl['path'], true) . '/';
		return $root;
	}
		
	
} // end class