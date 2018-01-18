<?php

namespace Iubar\Excel;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Collection\PdoCollection;
use Goodby\CSV\Export\Standard\Collection\CallbackCollection;
use Goodby\CSV\Export\Standard\CsvFileObject;
use Iubar\Common\StringUtil;
use Iubar\Common\BaseClass;

// USAGE of the Goodby\CSV classes 
// $lexer = new Lexer(new LexerConfig());
// $interpreter = new Interpreter();
// $interpreter->addObserver(function(array $row) {
// 	// do something here.
// 	// for example, insert $row to database.
// });
// $lexer->parse('data.csv', $interpreter);

class GoodbyCsvUtil extends BaseClass {
	
	private $delimiter = ";";
	private $enclosure = "\"";
	private $file = NULL;
	private $columns = array();
	private static $MAX_LEN = 50000;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function getColIndex($colName){
		$index = -1;
		$n = 0;
		foreach($this->columns as $column){
			if($colName == $column){
				$index = $n;
				break;
			}
			$n++;
		}
		return $index;
	}
	
	public function setFile($file){
		$this->file = $file;
	}
	
	public function getFile(){
		return $this->file;
	}
		
	public function getColumns(){
		return $this->columns;
	}	
	
	public function setDelimiter($delimiter){
		$this->delimiter = $delimiter;
	}
	
	public function getDelimiter(){
		return $this->delimiter;
	}	

	public function setEnclosure($enclosure){
		$this->enclosure = $enclosure;
	}
	
	public function getEnclosure(){
		return $this->enclosure;
	}

	public function removeEnclosure($values){
		$result = array();
		foreach ($values as $value){
			$value_cleaned = str_replace($this->getEnclosure(), "", $value);
			$result[] = $value_cleaned;
		}
		return $result;
	}
	
	public function initColumnsName(){
		$this->columns = $this->get_csv_header();	
	}
	
	public function get_csv_header(){
		$row = 0;
		$handle = fopen ($this->file, "r");
		if ($handle !== FALSE) {
			//$size = filesize($filename) + 1;
			while (($data = fgetcsv($handle, self::$MAX_LEN, $this->delimiter, $this->enclosure)) !== FALSE) {
				if($row==0){
					return $data;
					//$dump[$row] = $data;
					//echo $data[1] . $BR;
				}else{
					//echo "skipped " . $data[1] . $BR;
					break;
				}
				$row++;
			}
			fclose($handle);
		}
		return null;
	}

	public function exportToSql($tablename, $from_row=0){
		$data = array();
		$columns = $this->columns;
		$n = 0;
		$interpreter = new Interpreter();
		// $pdo->query('CREATE TABLE IF NOT EXISTS user (id INT, `name` VARCHAR(255), email VARCHAR(255))');
		$interpreter->addObserver(function(array $values) use (&$data, $columns, $from_row, &$n) {
			if($n>=$from_row){
				$query = "INSERT INTO " . $tablename . " (" . StringUtil::toCsv($columns) . ") VALUES (" . StringUtil::toCsv($values, "NULL") .")";
				$this->logDebug("Query is " . $query);
				$data[] = $query;
			}
			$n++;
		});
		$this->getLexer()->parse($this->file, $interpreter);
		return $data;
	}
		
	public function importoToDb($pdo, $tablename, $test=false){	
		$columns = $this->columns;
		$interpreter = new Interpreter();
		// $pdo->query('CREATE TABLE IF NOT EXISTS user (id INT, `name` VARCHAR(255), email VARCHAR(255))');
		$interpreter->addObserver(function(array $values) use ($pdo, $columns) {
			$query = "INSERT INTO " . $tablename . " (" . StringUtil::toCsv($columns) . ') VALUES (' . StringUtil::repeatString("?, ", count($columns)-1) . "?)";
			$this->logDebug("Query is " . $query);
			if(!$test){
				$stmt = $pdo->prepare($query);
				$stmt->execute($values);
			}
		});	
		$this->getLexer()->parse($this->file, $interpreter);
	}

	public function toArray($columns){
		$data = array();
		$interpreter = new Interpreter();
		$interpreter->addObserver(function(array $row) use (&$data) {
			    	
			$i = 0;
			$rowAssociative = array();
			foreach ($columns as $column){
				$rowAssociative[$column] = $row[$i];
				$i++;
			}
			$data[] = $rowAssociative; 	    
		});
		
		$this->getLexer()->parse($this->file, $interpreter);
		
		print_r($data);
		return $data;
	}

	public function getLexer(){
		$config = new LexerConfig();
		$config
		->setDelimiter($this->delimiter) // Customize delimiter. Default value is comma(,)
		->setEnclosure("'")  // Customize enclosure. Default value is double quotation(")
		->setEscape("\\")    // Customize escape character. Default value is backslash(\)
		->setToCharset('UTF-8') // Customize target encoding. Default value is null, no converting.
		->setFromCharset('UTF-8') // Customize CSV file encoding. Default value is null.
		;	
		$lexer = new Lexer($config);
		return $lexer;	
	}	
	
	public function getExporter(){
		$config = new ExporterConfig();
		$config
		->setDelimiter($this->delimiter) // Customize delimiter. Default value is comma(,)
		->setEnclosure("'")  // Customize enclosure. Default value is double quotation(")
		->setEscape("\\")    // Customize escape character. Default value is backslash(\)
		->setToCharset('UTF-8') // Customize file encoding. Default value is null, no converting.
		->setFromCharset('UTF-8') // Customize source encoding. Default value is null.
		->setFileMode(CsvFileObject::FILE_MODE_WRITE) 	// Customize file mode and choose either write or append. 
													  	// Default value is write ('w'). See fopen() php docs
														// const FILE_MODE_WRITE  = 'w';
														// const FILE_MODE_APPEND = 'a';
		
		;
		$exporter = new Exporter($config);
		return $exporter;		
	}

	
	public function exportoFromDb($pdo, $tablename, $file='php://output'){
		$query = "SELECT * FROM " . $tablename;		
		$stmt = $pdo->prepare($query);
		$this->logDebug("Query is " . $query);
		$stmt->execute();
		$count = $stmt->rowCount();
		$this->getExporter()->export($file, new PdoCollection($stmt));
		// "php://output" is a write-only stream that allows you to write to the output buffer mechanism in the same way as print and echo. 
		
		return $count;		
	}
	
	public function callbackDemo($file='php://stdout'){
		$data = array();
		$data[] = array('user', 'name1');
		$data[] = array('user', 'name2');
		$data[] = array('user', 'name3');
		
		$collection = new CallbackCollection($data, function($row) {
			// apply custom format to the row
			$row[1] = $row[1] . '!';
		
			return $row;
		});
			
		$this->getExporter()->export($file, $collection);
				
	}

} // end class

