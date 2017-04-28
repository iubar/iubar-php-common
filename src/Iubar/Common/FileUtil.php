<?php

namespace Iubar\Common;

// TODO: http://stackoverflow.com/questions/3321547/how-to-use-regexiterator-in-php

 


class FileUtil {
	
	public function __construct(PsrLogLoggerInterface $logger){
		$this->logger = $logger;
	}	

	public static function clearDirectory($path){
		foreach (new \DirectoryIterator($path) as $fileInfo) {
			if(!$fileInfo->isDot()) {
				// $name = $fileInfo->getPathname(); meglio usare....
				$name = $fileInfo->getRealPath();
				if($fileInfo->isFile()){
					unlink($name);
				}else{
					$iterator = new \FilesystemIterator($name);
					$isDirEmpty = !$iterator->valid();					
					if($isDirEmpty){			
						rmdir($name);
					}else{
					 	self::clearDirectory($name);		
					}
				}
			}
		}
	}
	
public static function fullPath2File($file){
	$array = explode("/", $file); // $array = explode("\\", $file);
	$index = count($array)-1;
	$f = $array[$index];
	return $f;
}

public static function readFromFile($filename) {
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	//echo "Ho letto: $contents" . "<br/>";
	return $contents;
}

public static function deleteDir($dirPath) {
	if (! is_dir($dirPath)) {
		throw new InvalidArgumentException("$dirPath must be a directory");
	}
	if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
		$dirPath .= '/';
	}
	$files = glob($dirPath . '*', GLOB_MARK);
	foreach ($files as $file) {
		if (is_dir($file)) {
			self::deleteDir($file);
		} else {
			unlink($file);
		}
	}
	rmdir($dirPath);
}

public static function getFilesInPath($target_path, $ext = "", $recursive=true){
	
	// TODO: scrivere un metodo analogo che accetta anche come parametro un pattern 
	// per i nomi dei file da ricercare
	// Poi vedi anche il metodo rglob e se è il caso di cancellarlo perchè obsoleto, oppure no
	
	$array = array();
	$iterator = NULL;
	if(!is_dir($target_path)){
		die("Quit: error in getFilesInPath() wrong path: '$target_path'" . PHP_EOL);
	}		
	if($recursive){
		$dir_iterator = new \RecursiveDirectoryIterator($target_path);
		$iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::SELF_FIRST);
		// could use CHILD_FIRST if you so wish
	}else{
		$iterator = new \FilesystemIterator ($target_path);
	}
	// $filter = new \RegexIterator($iterator, '/t.(php|dat)$/');
	$tot_size = 0;
	foreach ($iterator as $fileInfo) {
	 
		if ($fileInfo->isFile()) {			
			$continue = false;
			if($ext){
// 				$fullname = $fileInfo->getPathname();
// 				$fileInfoname = $fileInfo->getFilename();
				// $tot_size += $fileInfo->getSize();
				$_ext = $fileInfo->getExtension(); 
				if($_ext == $ext){
					$continue = true;
				}
			}else{
				$continue = true;
			}
			if($continue){
				// echo $fileInfo . "\r\n";
				// echo "\t" . substr($fileInfo->getPathname(), 27) . ": " . $fileInfo->getSize() . " B; modified " . date("Y-m-d", $fileInfo->getMTime()) . "\r\n";
				// $tot_size += $fileInfo->getSize();								
				
 
					$array[] = $fileInfo;
 
			}
		}
	}

	// echo "\r\nTotal file size: ", $size, " bytes" . "\r\n";

	return $array;
}

public static function countFilesInPath($target_path){
	$recursive = false;
	$array = FileUtil::getFilesInPath($target_path, "", $recursive);
	$size = sizeof($array);
	return $size;
}

public static function writeToFile2($filename, $text) {

	// Verifica che il file esista e sia riscrivibile
	//if (is_writable($filename)) {

		if (!$handle = fopen($filename, 'w+')) {
			 echo "Non si riesce ad aprire il file ($filename)" . "<br/>";
			 exit;
		}

		// Scrive $somecontent nel file aperto.
		$bytes = fwrite($handle, $text);
		
		if (!bytes) {
			echo "Non posso scrivere nel file ($filename)"  . "<br/>";
			exit;
		}

		//echo "Ho scritto ($text) nel file ($filename)"  . "<br/>";

		fclose($handle);

	//} else {
	//	echo "Il file $filename non � accessibile"  . "<br/>";
	//}

}


public static function printFilesInDir($dir, $format, $filter) {
	if ($handle = opendir($dir)) {
	echo "dir: " . $dir . "<br/>";
	echo "<ul>";
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			$fullpath = $dir. "/". $file;
			if (filterExtension($file, $filter)) {
				if ($format == 1) {
					echo "<li><a href=\"" . $fullpath . "\">" . $file . "</a></li>";
				} else if ($format == 2) {
					echo "<li>" .  $file . "</li>";
				} else if ($format == 3) {
					echo "<li><a href=\"" . "?action=showlog&file=$fullpath" . "\">" . $file . "</a></li>";
				}
			}
		}
	}
	echo "</ul>";
	closedir($handle);
	}
}

public static function readFileAndPrint($file) {
	$handle = @fopen($file, "r"); // Open file form read.
	if ($handle) {
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096); // Read a line.
			echo	$buffer . "<br/>";
		}
		fclose($handle); // Close the file.
	} else {
		echo	"Impossibile aprire il file " . $file . "<br/>";
	}
}

public static function change_ext($filename, $new_ext){
	$new_ext = str_replace(".", "", $new_ext);
	$old_ext = FileUtil::getFileExtension($filename);
	$new_filename = str_replace($old_ext, $new_ext, $filename);
	return $new_filename;
}

public static function filterExtension($file, $filter) {
	$b = false;
	$ext = FileUtil::getFileExtension($file);
	foreach ($filter as $ext2) {
		if ($ext == $ext2) {
			$b=true;
		}
	}
	return $b;
	}

//function to return file extension from a path or file name
public static function getFileExtension($path) {
	$parts=pathinfo($path);
	return $parts['extension'];
}

//function to return file name from a path
public static function getFileName($path) {
	$parts=pathinfo($path);
	return $parts['basename'];
}

public static function printCsv($file) {
	$row = 1;
	$handle = fopen($file,"r");
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    $num = count($data);
	    echo "<p> $num campi sulla linea $row: <br /></p>\n";
	    $row++;
	    for ($c=0; $c < $num; $c++) {
		echo $data[$c] . "<br>\n";
	    }
	}
	fclose($handle);
}

public static function deleteFile($file) { // TODO: convertire in chekFile((SplFileInfo) $file)
	$b = false;
	if(is_file($file)) {
		$b = unlink($file);
	}
	return $b;
}

public static function createDir($folder, $clear_if_exists=false){
	$msg = "";
	$b = false;
	if (file_exists($folder)) {

	    $msg = "The folder " . $folder. " already exists" . PHP_EOL;
	    
	    if ($clear_if_exists){
	    	self::clearDirectory($folder);
	    }	    
	    
	} else {
		$b = @mkdir($folder, 0777);
		if( $b ) {
			$msg = "The folder " . $folder . " was created" . PHP_EOL;			
		}else{
			$msg = "An error was occurred. Attempting create folder " . $folder . PHP_EOL;
			throw new \RuntimeException($msg);
		}

	}
	return $msg;
}

public static function getCurrentDir2(){
    return end(explode('/', dirname(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : str_replace('\\','/',__FILE__))));
}

public static function getCurrentDir(){
	$fullpath = $_SERVER['SCRIPT_NAME'];
	$path_parts = pathinfo($fullpath);
	$path = $path_parts['dirname'];
	return $path;
}


public static function printFilePath(){
	echo "\n";
	echo "----------------------" . "\n";
	echo "\n";
	$fullpath = $_SERVER['SCRIPT_NAME'];
	echo "fullpath: " . $fullpath . "\n";

	$path_parts = pathinfo($fullpath);
	echo "dirname: " . $path_parts['dirname'] . "\n";
	echo "basename: " . $path_parts['basename'] . "\n";
	echo "extension: " . $path_parts['extension'] . "\n";
	echo "filename: " . $path_parts['filename'] . "\n"; // since PHP 5.2.0
	if(isset($_SERVER['REQUEST_URI'])){
		echo "REQUEST_URI: " . $_SERVER["REQUEST_URI"] . "\n";
	}
	echo "PHP_SELF: " .  $_SERVER['PHP_SELF'] . "\n";
	echo "__FILE__: " .  __FILE__ . "\n";
	echo "argv[0]: " .  $argv[0] . "\n";
}


public static function writeContent($filename, $somecontent){
	// In our example we're opening $filename in append mode.
	// The file pointer is at the bottom of the file hence
	// that's where $somecontent will go when we fwrite() it.
	if (!$handle = fopen($filename, 'wb+')) {
		 echo "Cannot open file ($filename)" . StringUtil::NL;
		 exit;
	}

	// Write $somecontent to our opened file.
	if (fwrite($handle, $somecontent) === FALSE) {
		echo "Cannot write to file ($filename)" . StringUtil::NL;
		exit;
	}

	//echo "Success, wrote ($somecontent) to file ($filename)" . StringUtil::NL;

	fclose($handle);

}

public static function appendContent($filename, $somecontent){
	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {

		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $somecontent will go when we fwrite() it.
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)" . StringUtil::NL;
			 exit;
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)" . StringUtil::NL;
			exit;
		}

		//echo "Success, wrote ($somecontent) to file ($filename)" . StringUtil::NL;

		fclose($handle);

	} else {
		echo "The file $filename is not writable" . StringUtil::NL;
	}
}

/**
 * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
 *
 * @param string $str
 * @return int the result is in bytes
 * @author Svetoslav Marinov
 * @author http://www.www
 */
public static function filesize2bytes($str) {
    $bytes = 0;

    $bytes_array = array(
        'B' => 1,
        'KB' => 1024,
        'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024,
        'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
    );

    $bytes = floatval($str);

    if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[$matches[1]])) {
        $bytes *= $bytes_array[$matches[1]];
    }

    $bytes = intval(round($bytes, 2));

    return $bytes;
}

public static function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

public static function formatBytes2($file, $type) {
    switch($type){
        case "KB":
            $filesize = filesize($file) * .0009765625; // bytes to KB
        break;
        case "MB":
            $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
        break;
        case "GB":
            $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
        break;
    }
    if($filesize <= 0) {
        return $filesize = 'unknown file size';}
    else{return round($filesize, 2).' '.$type;}
}

public static function getTotFileSize($array){
	$size = 0;
	foreach($array as $filename){
		$size = $size + getFileSize($filename);
	}
	return $size;
}

public static function getFileSize($filename){
	$size = 0;
		if (file_exists($filename)) {
			$size =  filesize($filename);
		}
	return $size;
}

public static function dirSize($directory) {
	// http://www.php.net/manual/en/book.spl.php Standard PHP Library (SPL)
    $size = 0;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
        $size+=$file->getSize();
    }
    return $size;
}


public static function appendToFile2($filename, $content){

	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {

	    // In our example we're opening $filename in append mode.
	    // The file pointer is at the bottom of the file hence
	    // that's where $somecontent will go when we fwrite() it.
	    if (!$handle = fopen($filename, 'a')) {
			echo "SEVERE: Can't open the file: '$filename'" . "\r\n";
			exit(1);
	    }

	    // Write $somecontent to our opened file.
	    if (fwrite($handle, $content) === FALSE) {
			echo "SEVERE: Error writing into the file: '$filename'" . "\r\n";
			exit(1);
	    }

	    //echo "Success, wrote ($content) to file ($filename)";

	    fclose($handle);

	} else {
	    echo "SEVERE: The file '$filename' is not writable" . "\r\n";
	    exit(1);
	}
}

public static function appendToFile($file, $content){
	if (is_writable($file)) {
		file_put_contents($file, $content, FILE_APPEND);
	}else{
		echo "SEVERE: The file '$file' is not writable" . "\r\n";
		exit(1);
	}
}

public static function createUtf8File($filename){
	if (!file_exists($filename)) {
		
		$path_parts = pathinfo($filename);
		
		$path = $path_parts['dirname'];
 		if(!is_dir($path)){
			die("createUtf8File(): path " . $path . " not found");
		}
		
		$handle = fopen($filename, 'wb+');
		//echo "Output file does not exist: creating $filename\r\n";
		if ($handle) {
			self::writeUtf8Header($handle);
			fclose($handle);
		}
	}
}

/** @Deprecated
 * 
 * @param unknown $filename
 * @param unknown $content
 */
public static function appendToFileUtf8($filename, $content){
	// Let's make sure the file exists and is writable first.

	if (!file_exists($filename)) {
		self::createUtf8File($filename);
	}

	if (is_writable($filename)) {

	    // In our example we're opening $filename in append mode.
	    // The file pointer is at the bottom of the file hence
	    // that's where $somecontent will go when we fwrite() it.
	    if (!$handle = fopen($filename, 'a')) {
			echo "Cannot open file ($filename)";
			exit;
	    }

	    // Write $somecontent to our opened file.
	    //if (fwrite($handle, utf8_encode($content)) === FALSE) { // NON HO BISOGNO DI USARE utf8_encode
		if (fwrite($handle, $content) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
	    }

	    //echo "Success, wrote ($content) to file ($filename)";

	    fclose($handle);

	} else {
	    echo "The file $filename is not writable";
	}

}

public static function writeToFile($file, $content){
	file_put_contents($file, $content); // overwrite content if file exists
}

public static function writeUtf8Header($fh){
	$header = "\xEF\xBB\xBF"; // header utf8
	fwrite($fh, $header); // E' NECESSARIO AFFINCHE' IL FILE SIA CREATO IN UTF8 !!!!
}

public static function createFile($file){
	$handle = fopen($file, "wb+"); // delete file if it exists
	fclose($handle);
}

public static function writeToFileUtf8($filename, $content){
	$fh = fopen($filename, 'wb+') or die("can't open file");
	FileUtil::writeUtf8Header($fh);
	fwrite($fh, utf8_encode($content));
	fclose($fh);
}

// public static function createFileUtf8($path, $content = ''){ // TODO: verificare differenza con writeToFileUtf8()
// 	$file=fopen($filename, "w");
// 	# Now UTF-8 - Add byte order mark
// 	fwrite($file, pack("CCC", 0xef,0xbb,0xbf)); // Pack data into binary string
// 	fwrite($file, $content);
// 	fclose($file);
// }

public static function writeToFileUtf8_2($filename, $content){
	file_put_contents($filename, "\xEF\xBB\xBF".  $content);
}

//////////////////////////////////////////////////////// REMOVE TREE

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 */
public static function unlinkRecursive($dir, $deleteRootToo=true){
    if(!$dh = @opendir($dir)){
        return;
    }
    while (false !== ($obj = readdir($dh))){
        if($obj == '.' || $obj == '..'){
            continue;
        }

        if (!unlink($dir . '/' . $obj)){
            FileUtil::unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);

    if ($deleteRootToo){
        rmdir($dir);
    }

    return;
}

public static function delTree($dir) {
	$files = glob( $dir . '*', GLOB_MARK );
	foreach( $files as $file ){
		//if( substr( $file, -1 ) == '/' ){
		if (is_dir($file)){
			delTree( $file );
		}else{
			unlink( $file );
		}
	}
    if (is_dir($dir)){
    	rmdir( $dir );
    }
}

public static function deleteAll($directory, $empty = false) { // $empty==false means don't delete the root path
    if(substr($directory,-1) == "/") {
        $directory = substr($directory,0,-1);
    }

    if(!file_exists($directory) || !is_dir($directory)) {
        return false;
    } elseif(!is_readable($directory)) {
        return false;
    } else {
        $directoryHandle = opendir($directory);

        while ($contents = readdir($directoryHandle)) {
            if($contents != '.' && $contents != '..') {
                $path = $directory . "/" . $contents;

                if(is_dir($path)) {
                    deleteAll($path);
                } else {
                    unlink($path);
                }
            }
        }

        closedir($directoryHandle);

        if($empty == false) {
            if(!rmdir($directory)) {
                return false;
            }
        }

        return true;
    }
}

public static function is_empty_dir($dir) {
	// NOTE: you should obviously be checking beforehand if $dir is actually a directory,
	// and that it is readable, as only relying on this you would assume that in both cases
	// you have a non-empty readable directory.

    if (($files = @scandir($dir)) && count($files) <= 2) {
        return true;
    }
    return false;
}

public static function destroyDir($dir, $only_content=true) {
	if(strlen($dir)>3){


	$last_char = substr($dir, -1);
	if($last_char!="/"){
		$dir = $dir . "/";
	}

	$array = array();
	$mydir = opendir($dir);
	while(false !== ($file = readdir($mydir))) {
		if($file != "." && $file != "..") {
			chmod($dir . $file, 0777);
			if(is_dir($dir . $file)) {
				chdir('.');
				FileUtil::destroyDir($dir.$file);
				FileUtil::rmdir($dir.$file) or DIE("rmdir command: warning, couldn't delete " . $dir . $file . "\r\n");
				$array[] = $dir.$file;
			}else{
				unlink($dir.$file) or DIE("unlink command: warning, couldn't delete " . $dir . $file . "\r\n");
				$array[] = $dir.$file;
			}
		}
	}
	closedir($mydir);

	if(!$only_content){
		//chown($dir, 666); //Insert an Invalid UserId to set to Nobody Owern; 666 is my standard for "Nobody"
		chmod($dir, 0777);
		FileUtil::rmdir($dir);
		$array[] = $dir;
	}
	}else{
		die("Error: " . $dir);
	}
	return $array;

// in alternativa potrei utilizzre la funzione glob()
// $files = glob("/some/dir/*.txt"); // oppure glob($dir.'*.*')
// foreach($files as $file) unlink($file);
}


public static function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") FileUtil::rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

public static function array2string($array){
	$str = "";
	foreach($array as $elem){
		if($str==""){
			$str = $elem;
		}else{
			$str = $str . StringUtil::NL . $elem;
		}
	}
	return $str;
}

/* 
function smartcopy2($source, $dest){
	if(is_dir($source) && is_dir($dest)){
		$cp = new copyDir();

		// set the directory to copy
		if (!$cp->setCopyFromDir($source)) {
			echo $cp->viewError();
			echo "ERROR: smartcopy2() error 1" . StringUtil::NL;
			die;
		}
		// set the directory to copy to
		if (!$cp->setCopyToDir($dest)) {
			echo $cp->viewError();
			echo "ERROR: smartcopy2() error 2" . StringUtil::NL;
			die;
		}
		$cp->copySubFolders(false); // include sub folders when copying
		$cp->overWriteFiles(true); // overwrite existing files
		if (!$cp->createCopy( true )) { // create a copy and recurse through sub folders
			echo $cp->viewError();
			echo "ERROR: smartcopy2() error 3" . StringUtil::NL;
			die;
		}
	}else {

		if (!copy($source, $dest)) {
			echo "ERROR: failed to copy " . $file . StringUtil::NL;
		}else{
			echo "INFO: file " . $file . " copied to " . $temp_dest . StringUtil::NL;
		}
	}
}

*/


/** 
     * Copy file or folder from source to destination, it can do 
     * recursive copy as well and is very smart 
     * It recursively creates the dest file or directory path if there weren't exists
      * Situtaions : 
     * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
      * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
      * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest     
      * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
      * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
      * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
      * @todo 
     *     - Should have rollback technique so it can undo the copy when it wasn't successful
      *  - Auto destination technique should be possible to turn off 
     *  - Supporting callback function 
     *  - May prevent some issues on shared enviroments : http://us3.php.net/umask
      * @param $source //file or folder 
     * @param $dest ///file or folder 
     * @param $options //folderPermission,filePermission 
     * @return boolean 
     */ 
	 
    public static function smartCopy($source, $dest, $options=array('folderPermission'=>0755, 'filePermission'=>0755)){ 
        $result=false; 
        
		// echo "DEBUG: smartCopy( " . $source . " , " . $dest . ")" . "\r\n";
        if (is_file($source)) { 
            if ($dest[strlen($dest)-1]=='/') { 
                if (!file_exists($dest)) { 
                    cmfcDirectory::makeAll($dest,$options['folderPermission'], true);
                 } 
                $__dest=$dest."/".basename($source); 
            } else { 
                $__dest=$dest; 
            } 
            $result=copy($source, $__dest); 
            chmod($__dest,$options['filePermission']); 
            
        } else if(is_dir($source)) { 
            if ($dest[strlen($dest)-1]=='/') { 
                if ($source[strlen($source)-1]=='/') { 
                    //Copy only contents 
                } else { 
                    //Change parent itself and its contents 
                    $dest=$dest.basename($source); 
                    mkdir($dest); 
                    chmod($dest,$options['filePermission']); 
                } 
            } else { 
                if ($source[strlen($source)-1]=='/') { 
                    //Copy parent directory with new name and all its content 
                    mkdir($dest,$options['folderPermission']); 
                    chmod($dest,$options['filePermission']); 
                } else { 
                    //Copy parent directory with new name and all its content 
                    @mkdir($dest,$options['folderPermission']); 
                    chmod($dest,$options['filePermission']); 
                } 
            } 

            $dirHandle=opendir($source); 
            while($file=readdir($dirHandle)) { 
                if($file!="." && $file!="..") { 
                     if(!is_dir($source."/".$file)) { 
                        $__dest=$dest."/".$file; 
                    } else { 
                        $__dest=$dest."/".$file; 
                    } 
                    //echo "$source/$file ||| $__dest<br />"; 
                    $result = FileUtil::smartCopy($source."/".$file, $__dest, $options); 
                } 
            } 
            closedir($dirHandle); 
            
        } else { 
            $result=false; 
        } 
        return $result; 
    } 

	
/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 */

 
 public static function copyr($source, $dest) {
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        FileUtil::copyr("$source/$entry", "$dest/$entry");
    }

    // Clean up
    $dir->close();
    return true;
}


public static function bfglob($path, $pattern = '*', $flags = 0, $depth = 0) {

// Description
// non-recursive implementation for recursive glob with a depth parameter. The search is done breadth-first and specifying -1 for the depth means no limit.
// Parameters:
// $path   - path of folder to search
// $pattern- glob pattern
// $flags  - glob flags
// $depth  - 0 for current folder only, 1 to descend 1 folder down, and so on. -1 for no limit.

	$matches = array();
	$folders = array(rtrim($path, DIRECTORY_SEPARATOR));

	while($folder = array_shift($folders)) {
		$matches = array_merge($matches, glob($folder.DIRECTORY_SEPARATOR.$pattern, $flags));
		if($depth != 0) {
			$moreFolders = glob($folder.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
			$depth   = ($depth < -1) ? -1: $depth + count($moreFolders) - 2;
			$folders = array_merge($folders, $moreFolders);
		}
	}
	return $matches;
}

public static function getRelativePath($from, $to)
{
	// some compatibility fixes for Windows paths
	$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
	$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
	$from = str_replace('\\', '/', $from);
	$to   = str_replace('\\', '/', $to);

	$from     = explode('/', $from);
	$to       = explode('/', $to);
	$relPath  = $to;

	foreach($from as $depth => $dir) {
		// find first non-matching dir
		if($dir === $to[$depth]) {
			// ignore this directory
			array_shift($relPath);
		} else {
			// get number of remaining dirs to $from
			$remaining = count($from) - $depth;
			if($remaining > 1) {
				// add traversals up to first matching dir
				$padLength = (count($relPath) + $remaining - 1) * -1;
				$relPath = array_pad($relPath, $padLength, '..');
				break;
			} else {
				$relPath[0] = './' . $relPath[0];
			}
		}
	}
	return implode('/', $relPath);
}

public static function checkDir($path) {
	$msg = "";
	if (! is_dir($path)) {
		$msg = "The path '" . $path . "' does not exist.";
	} else {
// Il frammento seguente non è compatibile con Samba		
// 		if (! is_readable($path)) {
// 			$msg = "The path '" . $path . "' is not readable.";
// 		} else {
// 			// echo 'The path is readable';
// 		}
	}
	return $msg;
}

public static function checkFile($filename) { // TODO: convertire in chekFile((SplFileInfo) $file) 
	$msg = "";
	if (! is_file($filename)) {
		$msg = "The file '" . $filename . "' does not exist.";
	} else {
		// echo 'The file exists';
		if (! is_readable($filename)) {
			$msg = "The file '" . $filename . "' is not readable.";
		} else {
			// echo 'The file is readable';
		}
	}
	return $msg;
}

public static function checkIsWritable($path){
	$msg = "";
	if (! is_readable($path)) {
		$msg = "The path '" . $path . "' is not readable.";
	} else {
		if (! is_writable($path)) {
			$msg = "The path '" . $path . "' is not writable.";
		} else {
			// echo 'The path is writable';
		}
	}
	return $msg;
}


public static function searchFileByPattern($path, $regex){
	$result = array();
	$directory = new \RecursiveDirectoryIterator($path);
	$flattened = new \RecursiveIteratorIterator($directory);
	$files = new \RegexIterator($flattened, $regex); // esempio "/^.*\.(jpg|jpeg|png|gif)$/i"
	foreach($files as $file) {
		$result[] = $file;
	}
	return $result;
}

public static function getFileByPattern($path='.', $regex=''){ // $regex example '/^.(php|dat)$/' oppure /^.+\.php$/i
	$iterator = new \RecursiveDirectoryIterator($path);
	$filter = new \RegexIterator($iterator->getChildren(), $regex);
	// 	$filelist = array();
	// 	foreach($filter as $entry) {
	// 		$filelist[] = $entry->getFilename();
	// 	}
	return $filter;
}


/**
 * Recursive glob()
 */

/**
 * @param int $pattern
 *  the pattern passed to glob()
 * @param int $flags
 *  the flags passed to glob()
 * @param string $path
 *  the path to scan
 * @return mixed
 *  an array of files in the given path matching the pattern.
 */

public static function rglob($pattern='*', $flags = 0, $path='') {
	
	/*
	
	Glob Valid flags:
			
	* GLOB_MARK - Adds a slash to each directory returned	
	* GLOB_NOSORT - Return files as they appear in the directory (no sorting)	
	* GLOB_NOCHECK - Return the search pattern if no files matching it were found	
	* GLOB_NOESCAPE - Backslashes do not quote metacharacters	
	* GLOB_BRACE - Expands {a,b,c} to match 'a', 'b', or 'c'	
	* GLOB_ONLYDIR - Return only directory entries which match the pattern	
	* GLOB_ERR - Stop on read errors (like unreadable directories), by default errors are ignored.	
	*/
	
	
	
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files, FileUtil::rglob($pattern, $flags, $path)); }
    return $files;
}

private static function timestampToDate($timestamp){	
	$date = new \DateTime();
	$date->setTimestamp($timestamp);
	return $date;	
}

private static function timestampToString($timestamp){
	$date = self::timestampToDate($timestamp);
	$datetimeFormat = 'Y-m-d H:i:s';
	$str = $date->format($datetimeFormat);
	return $str;
}

public static function getLastFileByPattern($path='.', $pattern=''){  // FIXME: il flag case-unsentive (/i) sembra non funzionare. Ad esempio FileUtil::getLastFileByPattern(__DIR__, '/^.*.php$/i'); 
	$last_file = null;
	$iterator = self::getFileByPattern($path, $pattern);
	print_r($iterator);
	
	$filelist = array();
	foreach($iterator as $entry) {
		echo $entry->getFilename() . PHP_EOL;
		echo self::timestampToString($entry->getMTime()) . PHP_EOL; // Get last modification time
		$mtime = $entry->getMTime();
		$filename = $entry->getFilename();
		$filelist[$mtime] = $filename;
	}
	if(count($filelist)>0){
	ksort($filelist);
	$filelist = array_values($filelist);
	$last_file = $filelist[count($filelist)-1];
	}
	return $last_file;
}


/*
    Glob Valid flags:

        * GLOB_MARK - Adds a slash to each directory returned
        * GLOB_NOSORT - Return files as they appear in the directory (no sorting)
        * GLOB_NOCHECK - Return the search pattern if no files matching it were found
        * GLOB_NOESCAPE - Backslashes do not quote metacharacters
        * GLOB_BRACE - Expands {a,b,c} to match 'a', 'b', or 'c'
        * GLOB_ONLYDIR - Return only directory entries which match the pattern
        * GLOB_ERR - Stop on read errors (like unreadable directories), by default errors are ignored.
*/
 
 

} // end class


?>