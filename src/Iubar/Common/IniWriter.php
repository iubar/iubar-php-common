<?php

namespace Iubar\Common;

use Iubar\Common\BaseClass;

class IniWriter extends BaseClass {
    
    protected static bool $use_quote = false;
        
	public static function write_ini_file(array $assoc_arr, string $path, $has_sections = false) {
		
		$content = '';
		if ($has_sections) {
			foreach ($assoc_arr as $key => $elem) {
				$content .= '[' . $key . ']' . PHP_EOL;
				foreach ($elem as $key2 => $elem2) {
					if (is_array($elem2)) {
						for ($i = 0; $i < count($elem2); $i++) {
							$elem = $elem2[$i];
							if (self::$use_quote) {
								$elem = "\"" . $elem . "\"";
							}
							$content .= $key2 . '[] = ' . $elem . PHP_EOL;
						}
					} elseif ($elem2 == '') {
						$content .= $key2 . ' = ' . PHP_EOL;
					} else {
					    if (self::$use_quote) {
							$elem2 = "\"" . $elem2 . "\"";
						}
						$content .= $key2 . ' = ' . $elem2 . PHP_EOL;
					}
				}
			}
		} else {
			foreach ($assoc_arr as $key => $elem) {
				if (is_array($elem)) {
					for ($i = 0; $i < count($elem); $i++) {
						if (isset($elem[$i])) {
							$elem = $elem[$i];
							if (self::$use_quote) {
								$elem = "\"" . $elem . "\"";
							}
							$content .= $key . '[] = ' . $elem . PHP_EOL;
						}
					}
				} elseif ($elem == '') {
					$content .= $key . ' = ' . PHP_EOL;
				} else {
				    if (self::$use_quote) {
						$elem = "\"" . $elem . "\"";
					}
					$content .= $key . ' = ' . $elem . PHP_EOL;
				}
			}
		}

		if (!($handle = fopen($path, 'w'))) {
			return false;
		}

		$success = fwrite($handle, $content);
		fclose($handle);

		return $success;
	}
}
