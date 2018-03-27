<?php

namespace Iubar\System;

class System {
	
	public static function isWindows(){
		$svr_os=strtolower((explode(' ', php_uname('s'))[0]));
		return $svr_os==='windows';
	}
	public static function isMac(){
		$svr_os=strtolower((explode(' ', php_uname('s'))[0]));
		return $svr_os==='mac';
	}
	public static function isLinux(){
		$svr_os=strtolower((explode(' ', php_uname('s'))[0]));
		return $svr_os==='linux';;
	}
 	
}
