<?php

declare(strict_types=1);

namespace Iubar\Common;

use Iubar\Common\FileUtil;
use PHPUnit\Framework\TestCase;

/**
 * @TODO : su GitHub non ci sono le cartelle 'empty-dir' e 'locked-dir' e sul file .gitignore vengono incluse, perchè non ci sono?
 * Dove vengono create?
 * Test commentato in phpunit.xml
 */
class FileUtilTest extends TestCase {
	const REGEX_SQL = '/update_db_(.*)\.sql/i';
	public function testGetFileByPattern1() {
		$dir = __DIR__ . DIRECTORY_SEPARATOR . 'emtpy-dir';
		$files = FileUtil::getFileByPattern($dir, self::REGEX_SQL);
	}

	public function testGetFileByPattern2() {
		$dir = __DIR__ . DIRECTORY_SEPARATOR . 'locked-dir';
		$files = FileUtil::getFileByPattern($dir, self::REGEX_SQL);
	}
}
