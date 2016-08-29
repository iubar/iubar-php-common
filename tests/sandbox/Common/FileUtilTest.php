<?php

use Iubar\Common\FileUtil;

$inc = __DIR__ . "/../../../../vendor/autoload.php";
require_once($inc);

$target_path = "C:/Users/Borgo/workspace_php/php/php_iubar_builder/resources/pagheopen/jar";
$files1 = FileUtil::getFilesInPath($target_path, $ext = "", false);
$files2 = FileUtil::getFilesInPath($target_path, $ext = "", true);
 

echo "Files: " . count($files1) . PHP_EOL;
echo "Files: " . count($files2) . PHP_EOL;
 

echo "==================================" . PHP_EOL;

foreach ($files1 as $filesinfo) {
	echo "file " . $filesinfo->getBasename() . PHP_EOL;
}

echo "==================================" . PHP_EOL;

foreach ($files2 as $filesinfo) {
	echo "file " . $filesinfo->getBasename() . PHP_EOL;
}

echo "==================================" . PHP_EOL;

echo "END" . PHP_EOL;

 