<?php

namespace Iubar\Common;

class StringBuilder {
	private $str = [];

	public function __construct() {}

	public function append($str) {
		$this->str[] = $str;
	}

	public function toString() {
		return implode($this->str);
	}
} // end class
