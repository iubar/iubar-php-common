<?php


namespace Iubar\Common;

use Iubar\Common\BaseClass;

require 'BaseClass.php';

class IniWriter extends BaseClass {
	


public static function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
	$use_quote = false;
	$content = "";
	if ($has_sections) {
		foreach ($assoc_arr as $key=>$elem) {
			$content .= '['.$key.']' . PHP_EOL;
			foreach ($elem as $key2=>$elem2) {
				if(is_array($elem2)){
					for($i=0;$i<count($elem2);$i++){
						$elem = $elem2[$i];
						if($use_quote){
							$elem = "\"" . $elem . "\"";
						}						
						$content .= $key2."[] = " . $elem . PHP_EOL; 
					}
				} else if($elem2==""){
					$content .= $key2." = " . PHP_EOL;
				}else{
					if($use_quote){
						$elem2 = "\"" . $elem2 . "\"";
					}
					$content .= $key2." = " . $elem2 . PHP_EOL;
				}
			}
		}
	} else {
		foreach ($assoc_arr as $key=>$elem) {
			if(is_array($elem)) {
				for($i=0;$i<count($elem);$i++){
					if(isset($elem[$i])){
						$elem = $elem[$i];
						if($use_quote){
							$elem = "\"" . $elem . "\"";
						}
						$content .= $key . "[] = " . $elem . PHP_EOL;
					}
					
				}
			}else if($elem==""){
				$content .= $key." = ". PHP_EOL;
			}else {
				if($use_quote){
					$elem = "\"" . $elem . "\"";
				}
				$content .= $key." = " . $elem . PHP_EOL;
			}
		}
	}

	if (!$handle = fopen($path, 'w')) {
		return false;
	}

	$success = fwrite($handle, $content);
	fclose($handle);

	return $success;
}


}
