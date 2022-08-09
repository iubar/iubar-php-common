<?php

namespace Iubar\Web;

use PHPUnit\Framework\TestCase;
use Iubar\Web\WebUtil;

class WebUtilTest extends TestCase {
	public function test1() {
		$pageToCheck = 'http://www.iubar.it';
		$page_url = 'http://www.iubar.it';
		$image_url = 'http://www.iubar.it/tools/mailinglist/img/pollo.jpg';

		WebUtil::check_brokenlink($pageToCheck);
		echo PHP_EOL;

		$check_url_status = WebUtil::check_url($page_url);
		if ($check_url_status == '200') {
			// I think you can also check for 301 and 302 status codes: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
			echo "Link Works: $page_url" . PHP_EOL;
		} else {
			echo "Broken Link: $page_url" . PHP_EOL;
		}

		if (WebUtil::check_url2($page_url)) {
			echo "Link Works 2: $page_url" . PHP_EOL;
		} else {
			echo "Broken Link 2: $page_url" . PHP_EOL;
		}

		$check_url_status = WebUtil::check_url($image_url);
		if ($check_url_status == '200') {
			// I think you can also check for 301 and 302 status codes: http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
			echo "Link Works: $image_url" . PHP_EOL;
		} else {
			echo "Broken Link: $image_url" . PHP_EOL;
		}

		if (WebUtil::check_url2($image_url)) {
			echo "Link Works 2: $image_url" . PHP_EOL;
		} else {
			echo "Broken Link 2: $image_url" . PHP_EOL;
		}
	}

	public function test2() {
		if (false) {
			$txt_content = "Pippo e pluto vanno al <img src=\"http://www.iubar.it\">ok</img> ciao ciao";
			$links = WebUtil::extractLinks($txt_content);
			print_r($links);
		}

		$url = 'http://www.iubar.it/tools/mailinglist/img/logo_iubar.png';
		$ok = WebUtil::check_url2($url);
		echo PHP_EOL . PHP_EOL;
		if (!$ok) {
			$msg = 'ERROR: broken link detected: ' . $url;
			echo $msg . PHP_EOL;
		} else {
			$msg = 'OK: ' . $url;
			echo $msg . PHP_EOL;
		}
	}
} // end class
