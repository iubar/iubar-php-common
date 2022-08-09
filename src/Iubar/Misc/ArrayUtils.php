<?php

namespace Iubar\Misc;

class ArrayUtils {
	public static function getValue($array, $key, $default = null) {
		if (isset($array[$key])) {
			return $array[$key];
		} else {
			return $default;
		}
	}

	public static function removeValue(array $array, $del_value) {
		if (($key = array_search($del_value, $array)) !== false) {
			unset($array[$key]);
		}
		return $array;
	}
}
