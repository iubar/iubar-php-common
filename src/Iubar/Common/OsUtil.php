<?php

namespace Iubar\Common;

class OsUtil {

	public function __construct(){
		
	}
	
	public static function getInfoFromHttpUserAgent_demo(){
		$os_list = array(
				// Match user agent string with operating systems
				'Windows 3.11' => 'Win16',
				'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
				'Windows 98' => '(Windows 98)|(Win98)',
				'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
				'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
				'Windows Server 2003' => '(Windows NT 5.2)',
				'Windows Vista' => '(Windows NT 6.0)',
				'Windows 7' => '(Windows NT 7.0)',
				'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
				'Windows ME' => 'Windows ME',
				'Open BSD' => 'OpenBSD',
				'Sun OS' => 'SunOS',
				'Linux' => '(Linux)|(X11)',
				'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
				'QNX' => 'QNX',
				'BeOS' => 'BeOS',
				'OS/2' => 'OS/2',
				'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
		);
		
		// Loop through the array of user agents and matching operating systems
		foreach($os_list as $curr_os=>$match){
			// Find a match
			if (preg_match("/(" . $match . ")/i", $_SERVER['HTTP_USER_AGENT'])){
				// We found the correct match
				break;
			}
		}
		// You are using Windows Vista
		echo "You are using " . $curr_os . PHP_EOL;
				
	}
	public static function is_windows_2(){
		// Purpose:    Check if server is Windows
		return in_array(strtolower(PHP_OS), array("win32", "windows", "winnt"));
	}
	
	public static function is_linux_2(){
		// Purpose:    Check if server is Linux
		return in_array(strtolower(PHP_OS), array("linux", "superior operating system"));
	}
	public static function is_windows(){
		// Purpose:    Check if server is Windows
		$isWindows = false;
		$svr_os=strtolower(reset(explode(' ', php_uname('s'))));
		$isWindows=$svr_os==='windows';
		return $isWindows;
	}
	public static function is_linux(){
		// Purpose:    Check if server is Linux
		$isLinux = false;
		$svr_os=strtolower(reset(explode(' ', php_uname('s'))));
		$isLinux=$svr_os==='linux';
		return $isLinux;
	}
	
	public static function php_uname_demo(){
		echo php_uname();
		// 		php_uname([mode]); // returns information about the operating system PHP is running on
		// 		mode is a single character that defines what information is returned:
		// 		◦ 'a': This is the default. Contains all modes in the sequence "s n r v m".
		// 		◦ 's': Operating system name. eg. FreeBSD.
		// 		◦ 'n': Host name. eg. localhost.example.com.
		// 		◦ 'r': Release name. eg. 5.1.2-RELEASE.
		// 		◦ 'v': Version information. Varies a lot between operating systems.
		// 		◦ 'm': Machine type. eg. i386.

		echo PHP_OS;
		
		/* Some possible outputs:
		Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686
		Linux
		
		FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001
		FreeBSD
		
		Windows NT XN1 5.1 build 2600
		WINNT
		*/
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		    echo 'This is a server using Windows!';
		} else {
		    echo 'This is a server not using Windows!';
		}
	}
		
	
} // end class
