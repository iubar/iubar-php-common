<?php

namespace Iubar\Document;

use Iubar\Common\BaseClass;

require_once __DIR__ . '/../Common/BaseClass.php';

// TODO: Abbandonare Spreadsheet_Excel_Reader in favore di https://github.com/PHPOffice/PHPExcel

class ExcelReaderUtil extends BaseClass {
	
	public static $lettersArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	
	private $data = NULL;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function loadData($filename){
		$b = true; 	// "true" storing the extended information		
					// or "false" to conserve memory for large worksheets by not storing the extended information about cells like fonts, colors, etc
		$this->data = new \Spreadsheet_Excel_Reader($filename, $b, "UTF-8");		
	}
	
	public function getData(){
		return $this->data;
	}
			
	public function rowColCount($sheet=0, $row=1) {
		return count($this->data->sheets[$sheet]['cells'][$row]);
	}
		
	public function dumpToArray($sheet=0) {
	        $arr = array();
	        $data = $this->data;
	        for($row=1; $row<=$data->rowcount($sheet); $row++)
	                for($col=1; $col<=$data->colcount($sheet); $col++)
	                        $arr[$row][$col] = $data->val($row, $col, $sheet);
	        return $arr;
	}
	
	public function dumpCsv($row_numbers=false, $col_letters=false, $sheet=0, $table_class='excel'){
			$outs = array();
			$data = $this->data;
			for($row=1; $row<=$data->rowcount($sheet); $row++){
					$outs_inner = array();
					for($col=1; $col<=$data->colcount($sheet); $col++){
							// Account for Rowspans/Colspans
							$rowspan = $data->rowspan($row, $col, $sheet);
							$colspan = $data->colspan($row, $col, $sheet);
							for($i=0; $i<$rowspan; $i++){
									for($j=0; $j<$colspan; $j++){
											if ($i>0 || $j>0){
													$data->sheets[$sheet]['cellsInfo'][$row+$i][$col+$j]['dontprint']=1;
											}
									}
							}
	
							if(!$data->sheets[$sheet]['cellsInfo'][$row][$col]['dontprint']){
									$val = $data->val($row, $col, $sheet);
									$val = ($val=='')?'':addslashes(htmlentities($val));
	
									$outs_inner[] = "\"{$val}\""; # Quote or not?
									#$outs_inner[] = $val;
							}
					}
					$outs[] = implode(',', $outs_inner);
			}
			$out = implode("\r\n", $outs);
			return($out);
	}
	
	public function dump(){
		$html_content = $this->data->dump(true, true);
		// oppure
		// $html_content = $data->dump($row_numbers=false,$col_letters=false,$sheet=0,$table_class='excel');
		return $html_content;
	}
	
	public function searchValueForRow($sheet_index, $row_fixed, $txt, $offset_col_name=""){
		$MAX_COL = 50;
		$col_num = -1;
		$offset_col = $this->colName2Number($offset_col_name); // in caso di errore restituire 0
		for ($col = $offset_col; $col <= $MAX_COL; $col++) {
			$value = trim($this->data->val($row_fixed, $col, $sheet_index));
			if($value!=""){
				// $msg = "value: " . $value;
				// $this->logDebug($msg);
			}
			if($txt!=""){
				$pos = stripos($value, $txt); // case-insensitive
				if ($pos !== false) {
					$col_num  = $col;
					break;
				}
			}else if(($value == "") && ($txt == "")){
				$col_num  = $col;
				break;
			}else{
				// norhing to do
			}
		}
		return $col_num;
	}
	
	public function searchValueForColumn($sheet_index, $col_name, $txt, $offset=0, $max_rows=50000){
		$row_num = -1;
		$col = $this->colName2Number($col_name);
		$msg = "searchValueForColumn(): col " . $col_name . " is " . $col . " | searching for '$txt'";
		$this->logDebug($msg);
		$msg = "offset " . $offset . " max_rows " . $max_rows;
		$this->logDebug($msg);
		for ($row = $offset; $row <= $max_rows; $row++) {
			$value = trim($this->data->val($row, $col, $sheet_index));		
			if($value!=""){
				// $msg = "coord " . $row . ":" . $col . " value is " . $value;
				// $this->logDebug($msg);
			}
			if($txt!=""){
				$pos = stripos($value, $txt); // case-insensitive
				if ($pos !== false) {
					$row_num  = $row;
					break;
				}
			}else if(($value == "") && ($txt == "")){
				$row_num  = $row;
				break;
			}else{
				// norhing to do
			}
		}
		return $row_num;
	}
	
	public function getSheetIndexFromName($label) {
		$xls = $this->data;
	    foreach ($xls -> boundsheets as $key => $item){
	        if ($item['name'] == $label){
	            return $key;
	         }
	    }
	    return false;
	}
	
	public function getSheetNameFromIndex($index) {
		$xls = $this->data;
		$sheet_name = $xls->boundsheets[$index]['name'];
	    return $sheet_name;
	}
	
	public function getSheetsInfo() {
		$xls = $this->data;
		$array = array();
		$i = 0;
	    foreach ($xls -> boundsheets as $key => $item){
	        $sheet_name = $item['name'];
	        $array[$i] = $sheet_name;
	        $i++;
	    }
	    return $array;
	}
	
	public function printSheetsInfo() {
		$xls = $this->data;
		$sheet_num = count($xls->sheets);
		$msg = "total_sheets: " . $sheet_num;
		$this->logDebug($msg);
		$sheet_index = 0;
		foreach ($xls -> boundsheets as $key => $item){
			$msg  = "item: " . $item['name'];
			$this->logDebug($msg);
			$row_num = $xls->rowcount($sheet_index);
			$col_num = $xls->colcount($sheet_index);
			$msg = "sheet " . $sheet_index . " rows " . $row_num . " cols " . $col_num;
			$this->logDebug($msg);
			$sheet_index++;
		}
		return false;
	}
	
	public function printCellInfo($sheet=0, $row, $col){
		$data = $this->data;
		// The type of data in the cell: number|date|unknown
		$type = $data->type($row,$col,$sheet);
		// The raw data stored for the cell. For example, a cell may contain 123.456 but display as 123.5 because of the cell's format. Raw accesses the underlying value.
		$raw =  $data->raw($row,$col,$sheet);
		// If the cell has a hyperlink associated with it, the url can be retrieved.
		$link = $data->hyperlink($row,$col,$sheet);
		// Rowspan/Colspan of the cell.
		$row_span = $data->rowspan($row,$col,$sheet);
		$col_span = $data->colspan($row,$col,$sheet);
	
		$msg1 = "type " . $type;
		$msg2 = "raw " . $raw;
		$msg3 = "link " . $link;
		$msg4 = "span " . $row_span . " / " . $col_span;
		
		$this->logDebug($msg1);
		$this->logDebug($msg2);
		$this->logDebug($msg3);
		$this->logDebug($msg4);
	}
	
	public function getRowsBetween($row_start, $row_end){
		$array = array();
		$tot = $row_end - $row_start + 1;
		for ($i = $row_start; $i <= $row_end; $i++) {
			$array[] = $i;
		}
		return $array;
	}
	
	public function getColsBetween($col_start, $col_end){
		$array = array();
		$tot = $col_end - $col_start + 1;
		$msg = "col_start " . $col_start . " col_end " . $col_end;
		$this->logDebug($msg);
		for ($i = $col_start; $i <= $col_end; $i++) {
			$array[] = $i;
		}
		return $array;
	}
	
	/**
	 * @deprecated è un wrapper inutile su data->val()
	 * @param unknown $sheet_index
	 * @param unknown $row
	 * @param unknown $col
	 */
	public function readXls($sheet_index, $row, $col){
		$value = $this->data->val($row, $col, $sheet_index);
		$txt = "reading: sheet " . $sheet_index . " col " . $col . " row " . $row;
		$msg = "txt: " . $txt . " - value: " . $value;
		$this->logDebug($msg);
		return $value;
	}
	
	
	public function colName2Number($col_name){
		$pos = -1;
		$col_name = strtoupper($col_name);
		$i = 0;
		foreach (ExcelReaderUtil::$lettersArray as $letter) {
			if ($letter == $col_name) {
				$pos = $i + 1; // le colonne sono indicizzate partendo da 1
			}
			$i++;
		}
		return $pos;
	}
	
	public function colNumber2Name($col_num){
		// es: colNumber2Name(1) restiuirà "A"
		if($col_num<=0){
			return "NO_COL";
		}
		$array = ExcelReaderUtil::$lettersArray;
		$result = $array[($col_num-1)];
		return $result;
	}



// -------------------------------------------------- USAGE

	// require_once("excel/php-excel-reader/excel_reader2.php");
	
	/*
	
	// DOCS
	
	$this->data = new Spreadsheet_Excel_Reader("test.xls", true, "UTF-8");
	Retrieve the formatted value of a cell (what is displayed by Excel) on the first (or only) worksheet:
	$this->data->val($row,$col)
	You can also use column names rather than numbers:
	$this->data->val(10,'AZ')
	
	$this->data->val($row,$col,$sheet_index)

	echo $xls->dump(false,false,$xls->sheetByName('Plan3'));
	
	foreach ($xls->sheets[$sheet]['cells'] as $row){
	                        $coldata[] = $row[$column];
	                        $TempString = $row[$column];
	                        echo $TempString." ";
	                }
	
	foreach ($xls->sheets[$sheet]['cells'] as $row){
	                        $coldata[] = $row[$column];
	                        $TempString = $row[$column];
	                        echo $TempString." ";
	                }

*/

} // end class

?>