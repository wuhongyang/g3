<?php
/**
* 数组存储到文件
* @date 2012-08-14
* @author dl
* @version $Id$
*/
class array2file
{
	private $file_path = '';
	private $add_rows  = array();
	private $read_rows = array();
	
	public function __construct($file_path){
		$this->file_path = $file_path;
		if(file_exists($file_path)){
			$this->read_rows = include $file_path;
		}
		if(empty($this->read_rows)){
			$this->read_rows = array();
		}
	}
	
	public function add($row){
		if(is_array($row) && !empty($row)){
			$this->add_rows[] = $row;
		}
	}
	
	public function edit($row,$id){
		if( ! isset($this->read_rows[$id])){
			return false;
		}
		$this->read_rows[$id] = $row;
		return true;
	}
	
	public function remove($id){
		unset($this->read_rows[$id]);
		return true;
	}

	public function getRow($id){
		if( ! isset($this->read_rows[$id])){
			return null;
		}
		return $this->read_rows[$id];
	}
	
	public function searchRow($s_key, $s_value){
		$result = array();
		foreach($this->read_rows as $one){
			if($one[$s_key] == $s_value){
				$result[] = $one;
			}
		}
		return $result;
	}
	
	public function getAll(){
		return $this->read_rows;
	}
	
	public function save(){
		foreach($this->add_rows as $row){
			$this->read_rows[] = $row;
		}
		$string = "<?php \r\n return " . var_export($this->read_rows,true). ";\r\n";
		$fp = fopen($this->file_path, 'w');
		if ($fp) {
			flock($fp, LOCK_EX);
			fwrite($fp, $string);
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->add_rows = array();
			return true;
		} else {
			return false;
		}
	}

}
