<?php

namespace Iubar\Web\Avatar;

class Gravatar {
	// Vedi anche

	// https://github.com/laravolt/avatar

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 */
	public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = []) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ($img) {
			$url = '<img src="' . $url . '"';
			foreach ($atts as $key => $val) {
				$url .= ' ' . $key . '="' . $val . '"';
			}
			$url .= ' />';
		}
		return $url;
	}
}
