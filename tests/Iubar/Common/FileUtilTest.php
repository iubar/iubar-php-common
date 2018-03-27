<?php

declare(strict_types=1);

namespace Iubar\Tests\Crypt;

use PHPUnit\Framework\TestCase;
use Iubar\Common\FileUtil;

class FileUtilTest extends TestCase {

	const REGEX_SQL = "/update_db_(.*)\\.sql/i";

	public function testGetFileByPattern1(){
		$dir = __DIR__ . DIRECTORY_SEPARATOR . 'emtpy-dir';
		$files = FileUtil::getFileByPattern($dir, self::REGEX_SQL);
	}

	public function testGetFileByPattern2(){
		$dir = __DIR__ . DIRECTORY_SEPARATOR . 'locked-dir';
		$files = FileUtil::getFileByPattern($dir, self::REGEX_SQL);
	}

}