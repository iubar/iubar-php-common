<?php

function demo0(){
    // Create recursive dir iterator which skips dot folders
    $dir = new RecursiveDirectoryIterator('./system/information',
        FilesystemIterator::SKIP_DOTS);
    
    // Flatten the recursive iterator, folders come before their files
    $it  = new RecursiveIteratorIterator($dir,
        RecursiveIteratorIterator::SELF_FIRST);
    
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
function demo1(){
    
    //we want to iterate a directory
    $Directory = new \RecursiveDirectoryIterator("/var/dir");
    
    //we need to iterate recursively
    $It        = new \RecursiveIteratorIterator($Directory);
    
    //We want to stop decending in directories named '.Trash[0-9]+'
    $Regex1    = new \RecursiveRegexIterator($Directory,'%([^0-9]|^)(?<!/.Trash-)[0-9]*$%');
    
    //But, still continue on doing it **recursively**
    $It2       = new \RecursiveIteratorIterator($Regex1);
    
    //Now, match files
    $Regex2    = new \RegexIterator($It2,'/\.php$/i');
    foreach($Regex2 as $v){
        echo $v."\n";
    }
    
    
}



function demo2(){
    
    $directory = new \RecursiveDirectoryIterator(__DIR__);
    $flattened = new \RecursiveIteratorIterator($directory);
    
    // Make sure the path does not contain "/.Trash*" folders and ends eith a .php or .html file
    $files = new \RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+\.(?:php|html)$#Di');
    
    foreach($files as $file) {
        echo $file . PHP_EOL;
    }
    
    
}

////////////////////////////////////////////////////////////////////////

abstract class FilesystemRegexFilter extends RecursiveRegexIterator {
    protected $regex;
    public function __construct(RecursiveIterator $it, $regex) {
        $this->regex = $regex;
        parent::__construct($it, $regex);
    }
}

class FilenameFilter extends FilesystemRegexFilter {
    // Filter files against the regex
    public function accept() {
        return ( ! $this->isFile() || preg_match($this->regex, $this->getFilename()));
    }
}

class DirnameFilter extends FilesystemRegexFilter {
    // Filter directories against the regex
    public function accept() {
        return ( ! $this->isDir() || preg_match($this->regex, $this->getFilename()));
    }
}

function demo3(){
    
    $dir = "c:\php_tmp";
    $directory = new \RecursiveDirectoryIterator($dir);
    $directory->setMaxDepth(1); // Two levels, the parameter is zero-based.
    // Filter out ".Trash*" folders
    //$filter = new DirnameFilter($directory, '/^(?!\.Trash)/'); // filtro esclusivo
    $filter = new DirnameFilter($directory, '/^(.*)/'); // filtro inclusivo
    // Filter PHP/HTML files
    $filter = new FilenameFilter($filter, '/\.(?:php|html)$/');
    
    foreach(new \RecursiveIteratorIterator($filter) as $file) { // Nota qui $file è di tipo \SplFileInfo
        if(is_file($file)){
            echo "\t F: " . $file . PHP_EOL;
        }else if(is_dir($file)){
            echo "\t D: " . $file . PHP_EOL;
        }else{
            echo "\t ?: " . $file . PHP_EOL;
        }
        print_r($file);
    }
    
}
///////////////////


 

// demo3();


// $files = FileUtil::searchFileByPattern("C:/Users/Borgo/iubar/paghe/install/windows/nsi/db/update/sql", "/Update_Db_(Paghe|Addizionali|Anag|Fisco)_da_015500_a_015600\.sql/");
print_r($files);