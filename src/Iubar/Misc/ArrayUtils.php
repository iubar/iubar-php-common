<?php

namespace Iubar\Misc;

class ArrayUtils {
	
	public static function getValue($array, $key, $default = null){
		if (isset($array[$key])){
			return $array[$key];
		} else {
			return $default;
		}
	}
	
}