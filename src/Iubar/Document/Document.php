<?php

namespace Iubar\Document;

use Iubar\Common\BaseClass;
use Iubar\Common\StringUtil;

class Document extends BaseClass {
	public static function search_and_replace($file, $search, $replace) {
		$file_contents = file_get_contents($file);
		$file_contents = str_replace($search, $replace, $file_contents);
		file_put_contents($file, $file_contents);
	}

	public static function search_and_replace_once($file, $search, $replace) {
		$file_contents = file_get_contents($file);
		$file_contents = StringUtil::replaceOnce2($search, $replace, $file_contents);
		file_put_contents($file, $file_contents);
	}
}
