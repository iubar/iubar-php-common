<?php

namespace Iubar\Common;

class LangUtil {
	
	/**
	 * 
	 * ie: $a = array(1,2,array(3,4, array(5,6,7), 8), 9);
	 * 
	 * @return \Iubar\Common\RecursiveIteratorIterator[]
	 */
	public static function array_flat(array $array){
		$flat_array = array();		
		$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));
		foreach($it as $v) {
			$flat_array[] = $v;
		}
		return $flat_array;
	}
	
	public static function getScriptPath(){
		$fullpath = $argv[0];
		$path_parts = pathinfo($fullpath);
		$script_path = $path_parts['dirname'];
		echo "SCRIPT FULLPATH: " . $fullpath . StringUtil::NL . StringUtil::NL;
		echo "SCRIPT PATH: " . $script_path . StringUtil::NL . StringUtil::NL;
		return $script_path ;		
	}
	
	public static function getCombinations($base, $n){
		// SOURCE: http://stackoverflow.com/questions/4279722/php-recursion-to-get-all-possibilities-of-strings/8880362#8880362
		// USAGE: var_dump(getCombinations(array("a","b","c","d"),2)); 
		
		$baselen = count($base);
		if($baselen == 0){
			return;
		}
		if($n == 1){
			$return = array();
			foreach($base as $b){
				$return[] = array($b);
			}
			return $return;
		}else{
			//get one level lower combinations
			$oneLevelLower = LangUtil::getCombinations($base,$n-1);
	
			//for every one level lower combinations add one element to them that the last element of a combination is preceeded by the element which follows it in base array if there is none, does not add
			$newCombs = array();
	
			foreach($oneLevelLower as $oll){
	
				$lastEl = $oll[$n-2];
				$found = false;
				foreach($base as  $key => $b){
					if($b == $lastEl){
						$found = true;
						continue;
						//last element found
	
					}
					if($found == true){
						//add to combinations with last element
						if($key < $baselen){
	
							$tmp = $oll;
							$newCombination = array_slice($tmp,0);
							$newCombination[]=$b;
							$newCombs[] = array_slice($newCombination,0);
						}
	
					}
				}
	
			}
	
		}
	
		return $newCombs;
	}
	
// 	public static function intersect_array($array1, $array2){ // TODO: verificare perchè non uso la funzione nativa di PHP array_intersect()
// 		$result = array();
// 		foreach ($array1 as $elem1){
// 			foreach ($array2 as $elem2){
// 				if($elem1===$elem2){
// 					$result[] = $elem1;
// 				}
// 			}			
// 		}
// 		return $result;
// 	}
	
	public static function array_equals($array1, $array2){
		$b=true;
		$array_merged = array_unique(array_merge($array1, $array2));
		$array_intersect = array_intersect($array1, $array2);
		if (count(array_diff($array_merged, $array_intersect)) === 0) {
			$b = true;
		}
		return $b;
	}
	public static function isAnAssociativeArray($array){
		return array_keys($array) !== range(0, count($array) - 1);
	}
	public static function is_assoc($var){ // metodo analogo a isAnAssociativeArray()
		return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
	}
		
	public static function merge_distinct($array1, $array2){
		$r = array_merge($array1, $array2);
		$r = array_unique($r);
		$r = array_values($r);
		return $r;
	}
	public static function multi_replace($rep_array, $txt){
		foreach($rep_array as $key=>$value){
			$txt = str_replace($key, $value, $txt);
		}
		return $txt;
	}
	public static function is_in_array($array, $key, $key_value){
		$within_array = 'no';
		foreach( $array as $k=>$v ){
			if( is_array($v) ){
				$within_array = is_in_array($v, $key, $key_value);
				if( $within_array == 'yes' ){
					break;
				}
			} else {
				if( $v == $key_value && $k == $key ){
					$within_array = 'yes';
					break;
				}
			}
		}
		return $within_array;
		
// 		$test = array(
// 				0=> array('ID'=>1, 'name'=>"Smith"),
// 				1=> array('ID'=>2, 'name'=>"John")
// 		);
// 		print_r(is_in_array($test, 'name', 'Smith'));
		
	}

	public static function objectToArray($obj) {
		$array = json_decode(json_encode($obj), true);
		return $array;
	}
	
	
} // end class