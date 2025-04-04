<?php

//  @see https://stackoverflow.com/questions/3321547/how-to-use-regexiterator-in-php?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa

function demo0() {
	// Create recursive dir iterator which skips dot folders
	$dir = new RecursiveDirectoryIterator('./system/information', FilesystemIterator::SKIP_DOTS);

	// Flatten the recursive iterator, folders come before their files
	$it = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

	// Maximum depth is 1 level deeper than the base folder
	$it->setMaxDepth(1);

	// Basic loop displaying different messages based on file or folder
	foreach ($it as $fileinfo) {
		if ($fileinfo->isDir()) {
			printf("Folder - %s\n", $fileinfo->getFilename());
		} elseif ($fileinfo->isFile()) {
			printf("File From %s - %s\n", $it->getSubPath(), $fileinfo->getFilename());
		}
	}
}
function demo1() {
	//we want to iterate a directory
	$Directory = new \RecursiveDirectoryIterator('/var/dir');

	//we need to iterate recursively
	$It = new \RecursiveIteratorIterator($Directory);

	//We want to stop decending in directories named '.Trash[0-9]+'
	$Regex1 = new \RecursiveRegexIterator($Directory, '%([^0-9]|^)(?<!/.Trash-)[0-9]*$%');

	//But, still continue on doing it **recursively**
	$It2 = new \RecursiveIteratorIterator($Regex1);

	//Now, match files
	$Regex2 = new \RegexIterator($It2, '/\.php$/i');
	foreach ($Regex2 as $v) {
		echo $v . "\n";
	}
}

function demo2() {
	$directory = new \RecursiveDirectoryIterator(__DIR__);
	$flattened = new \RecursiveIteratorIterator($directory);

	// Make sure the path does not contain "/.Trash*" folders and ends eith a .php or .html file
	$files = new \RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+\.(?:php|html)$#Di');

	foreach ($files as $file) {
		echo $file . PHP_EOL;
	}
}

////////////////////////////////////////////////////////////////////////

abstract class FilesystemRegexFilter extends \RecursiveRegexIterator {
	protected $regex;
	public function __construct(RecursiveIterator $it, $regex) {
		$this->regex = $regex;
		parent::__construct($it, $regex);
	}
}

class FilenameFilter extends FilesystemRegexFilter {
	// Filter files against the regex
	public function accept() {
		return !$this->isFile() || preg_match($this->regex, $this->getFilename());
	}
}

class DirnameFilter extends FilesystemRegexFilter {
	// Filter directories against the regex
	public function accept() {
		return !$this->isDir() || preg_match($this->regex, $this->getFilename());
	}
}

function demo3() {
	$dir = 'c:\php_tmp';
	$directory = new \RecursiveDirectoryIterator($dir);
	$directory->setMaxDepth(1); // Two levels, the parameter is zero-based.
	// Filter out ".Trash*" folders
	//$filter = new DirnameFilter($directory, '/^(?!\.Trash)/'); // filtro esclusivo
	$filter = new DirnameFilter($directory, '/^(.*)/'); // filtro inclusivo
	// Filter PHP/HTML files
	$filter = new FilenameFilter($filter, '/\.(?:php|html)$/');

	foreach (new \RecursiveIteratorIterator($filter) as $file) {
		// Nota qui $file è di tipo \SplFileInfo
		if (is_file($file)) {
			echo "\t F: " . $file . PHP_EOL;
		} elseif (is_dir($file)) {
			echo "\t D: " . $file . PHP_EOL;
		} else {
			echo "\t ?: " . $file . PHP_EOL;
		}
		print_r($file);
	}
}
///////////////////
function getFileByPattern($path = '.', $regex = '') {
	// $regex example '/^.*\.(php|dat)$/' oppure /^.+\.php$/i
	$iterator = new \RecursiveDirectoryIterator($path);
	$filter = new \RegexIterator($iterator->getChildren(), $regex);
	$filelist = [];
	foreach ($filter as $entry) {
		$filelist[] = $entry->getFilename();
	}
	return $filelist;
}

function searchFileByPattern($path, $regex) {
	$result = [];
	$directory = new \RecursiveDirectoryIterator($path);
	$flattened = new \RecursiveIteratorIterator($directory);
	$files = new \RegexIterator($flattened, $regex); // esempio '/^.*\.(jpg|jpeg|png|gif)$/i'
	foreach ($files as $file) {
		$result[] = $file;
	}
	return $result;
}

// demo3();

// $files = FileUtil::searchFileByPattern("C:/Users/Borgo/iubar/paghe/install/windows/nsi/db/update/sql", "/Update_Db_(Paghe|Addizionali|Anag|Fisco)_da_015500_a_015600\.sql/");

$path = 'C:\Users\Borgo\iubar\paghe\db\sql\install';
$regex = '/Get(.*)\.sql/i';
$regex = '/Db(.*)\.sql/i';
echo $path . PHP_EOL;
echo $regex . PHP_EOL;

$files = getFileByPattern($path, $regex);
print_r($files);
