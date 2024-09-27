<?php

namespace Iubar\Web;

class XmlUtil {
	public function __construct() {
		// nothing to do
	}

	public static function get_subtree($tagname, $xml) {
		$xml2 = '';
		$regex = '/<' . $tagname . '\s*(.*?)>(.*?)<\/' . $tagname . '>/';
		$matches = [];
		$found = preg_match_all($regex, $xml, $matches);
		// $matches[0] è la stringa completa
		// $matches[1] è l'attributo del tag (nella forma nome="valore")
		// $matches[2] è il sottoalbero xml del tag
		if ($found !== false){
    		if (isset($matches[2])) {
    			$xml2 = $matches[2][0];
    		}
		}
		return $xml2;
	}
}
