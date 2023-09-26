<?php

namespace Iubar\System;

class Desktop {
	private static bool $isLinux = false;

	public static function openBrowser(string $url) : void {
		if (System::isWindows()) {
			$cmd = 'start ' . $url;
			exec($cmd);
		} elseif (self::$isLinux) {
			die('Quit: Linux system detected.' . PHP_EOL);
		} else {
			die('Quit: Unknown system detected.' . PHP_EOL);
		}
	}
	
	public static function openChrome(string $url) : void {
		$cmd = '';
		// 		if($default){
		// 			$cmd = "start link" . " " . $url;
		// 		}else{
		// 			$cmd = "chrome.exe" . " " . $url;
		// 			// $cmd = "iexplore.exe" . " " . $url;
		// 		}

		if (System::isWindows()) {
			// C:\Program Files (x86)\Google\Chrome\Application\chrome.exe
			// C:\Program Files\Google\Chrome\Application\chrome.exe

			$reg_key = 'HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\App Paths\chrome.exe';

			$path = Desktop::readRegistry($reg_key);

			$cmd = "\"" . $path . "\"" . ' ' . "\"" . $url . "\"";

			echo 'Eseguo: ' . $cmd . PHP_EOL;
			$output = [];
			$exit_code = 0;
			exec($cmd, $output, $exit_code);
			if(!empty($output)){
			     echo "Cmd output: " . PHP_EOL;
			     print_r($output);
			     echo PHP_EOL;
			}
			if($exit_code){
			    echo "ERROR, exit_code is " . $exit_code . PHP_EOL;
			}
		} elseif (self::$isLinux) {
			die('Quit: Linux system detected.' . PHP_EOL);
		} else {
			die('Quit: Unknown system detected.' . PHP_EOL);
		}
	}

	public static function readRegistry(string $reg_key, string $value = '') : string {
		// http://ss64.com/nt/reg.html
		// https://technet.microsoft.com/en-us/library/cc742028.aspx
		//
		// REG QUERY [ROOT\]RegKey /v ValueName
		// REG QUERY [ROOT\]RegKey /ve  --This returns the (default) value

		$cmd = null;
		if (!$value) {
			$cmd = "REG QUERY \"" . $reg_key . "\" /ve";
		} else {
			$cmd = "REG QUERY \"" . $reg_key . "\" /v" . ' ' . $value;
		}

		// echo 'eseguo: ' . $cmd . PHP_EOL;

		exec($cmd, $output, $return_vars);
		
		// echo "\$output: " . PHP_EOL;
		// print_r($output);
		// echo "\$return_vars: " . PHP_EOL;
		// print_r($return_vars);

		$line = trim($output[2]);
		// echo "\$line: " . $line . PHP_EOL;
		// ad esempio line può valere
		// (Predefinito)    REG_SZ    C:\Program Files (x86)\Google\Chrome\Application\chrome.exe
		$tokens = explode('    ', $line);
		$result = trim($tokens[2]);
		return $result;
	}

	public static function getWorkspace() : string {
		$workspace = '';
		echo 'I have been run on ' . php_uname('s') . PHP_EOL;
		$user = get_current_user();
		if (System::isWindows()) {
			$env = getenv('WORKSPACE_PHP');
			if (!$env) {
				// set the default value for the workspace path
				$user_home = 'C:/Users' . '/' . $user;
				$workspace = $user_home . '/' . 'workspace_php';
			}
		} elseif (self::$isLinux) {
			die('Quit: Linux system detected.' . PHP_EOL);
		} else {
			die('Quit: Unknown system detected.' . PHP_EOL);
		}
		return $workspace;
	}
}

// Firefox OpenURL

// To open URL in a new tab, enter:
// $ /usr/bin/firefox -new-window http://www.cyberciti.biz/

// To open URL in a new window, enter:
// $ /usr/bin/firefox www.cyberciti.biz

// Firefox Search option

// You can search words (term) with your default search engine, enter:
// $ /usr/bin/firefox -search "term"
// $ /usr/bin/firefox -search "linux add user to group"
 
