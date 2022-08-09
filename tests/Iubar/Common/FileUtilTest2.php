<?php

declare(strict_types=1);

namespace Iubar\Common;

use Iubar\Common\FileUtil;
use PHPUnit\Framework\TestCase;

/**
 *
 * The following command runs the test on a single method:
 *
 * phpunit --filter testSaveAndDrop EscalationGroupTest escalation/EscalationGroupTest.php
 *
 * phpunit --filter methodName ClassName path/to/file.php
 *
 * For newer versions of phpunit, it is just:
 *
 * phpunit --filter methodName path/to/file.php
 *
 *
 */
class FileUtilTest2 extends TestCase {
	const REGEX = '/(.*)\.php/i';
	const PATH = __DIR__ . DIRECTORY_SEPARATOR . '..';
	const EXT = 'php';
	const RECURSIVE = true;

	public function testGetFileByPattern1() {
		echo PHP_EOL;
		echo 'path: ' . self::PATH . PHP_EOL;

		$this->printSeparator();
		$iterators = FileUtil::getFileByPattern(self::PATH, self::REGEX); // returns a RegexIterator Object (NOTA: il metodo non analizza le sotto cartelle)
		// NOTA: inefficace: $this->printList($iterators); perchè l'iteratore non è stampabile a video per definizione
		foreach ($iterators as $entry) {
			echo '--> ' . $entry->getFilename() . PHP_EOL;
		}
		$files4 = FileUtil::getLastFileByPattern(self::PATH . '/Common', self::REGEX); // returns null|string // Non analizza le sottoartelle
		$this->printSeparator();

		$this->printList($files4);
		$this->printSeparator();

		$files2 = FileUtil::getFilesInPath(self::PATH, self::EXT, self::RECURSIVE); // returns a list of SplFileInfo Object
		$this->printList($files2);
		$this->printSeparator();

		$files3 = FileUtil::searchFileByPattern(self::PATH, self::REGEX); // returns a list of SplFileInfo Object // il metodo analizza sempre le sotto cartelle
		$this->printList($files3);
		$this->printSeparator();

		$this->assertArrayEquals($files2, $files3);

		$files5 = FileUtil::rglob('*.php', 0, self::PATH); // The type $list is of type string
		$this->printList($files5);
		echo "\$files5 size " . count($files5) . PHP_EOL;
		$this->printSeparator();

		$this->assertEquals(count($files2), count($files5));
	}

	private function printList($list) {
		$i = 0;
		if (!$list) {
			echo 'WARNING: null/empty detected ' . PHP_EOL;
		} elseif (!is_array($list)) {
			$elem = $list;
			$this->printElemInfo($elem);
			print_r($elem);
			echo PHP_EOL;
		} else {
			foreach ($list as $elem) {
				if ($i == 0) {
					$this->printElemInfo($elem);
				}
				print_r($elem);
				echo PHP_EOL;
				$i++;
			}
		}
	}

	/**
	 * @see https://stackoverflow.com/questions/3838288/phpunit-assert-two-arrays-are-equal-but-order-of-elements-not-important
	 * @see https://phpunit.readthedocs.io/en/8.4/assertions.html#assertequalscanonicalizing
	 */
	private function assertArrayEquals($array1, $array2) {
		$this->assertEquals(0, count(array_diff($array1, $array2)));
		$this->assertEquals(0, count(array_diff($array2, $array1)));
	}

	private function printElemInfo($elem) {
		if (is_object($elem)) {
			echo "INFO: The object \$list is of type " . get_class($elem) . PHP_EOL;
		} else {
			echo "INFO: The type \$list is of type " . gettype($elem) . PHP_EOL;
		}
	}

	private function printSeparator() {
		echo '*****************************************************' . PHP_EOL;
	}
}
