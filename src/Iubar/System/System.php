<?php

namespace Iubar\System;

class System {
	
	public static function isWindows(){
		$isWindows = false;
		$svr_os=strtolower((explode(' ', php_uname('s'))[0]));
		$isWindows=$svr_os==='windows';
		// $isLinux=$svr_os==='linux';
		return $isWindows;
	}
	
}

?>