<?php
class data_export{
	
	private $db = null;
	private $file_name = "data_export.sql";
	private $lock_name = "lock";
	private $new_file = false;
	
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	function export_table($table, $new_file=false){
		$this->new_file = $new_file;
		$tables = explode("&", $table);
		if(!$this->open_file()){
			return array("Flag"=>102, "FlagString"=>"导出失败，有另一个程序正在导出，稍后再试");
		}
		foreach($tables as $one){
			$arr = explode(".", $one);
			if($this->write_file($arr[0], $arr[1]) != 0){
				return array("Flag"=>102, "FlagString"=>"导出错误");
			}
		}
		return array("Flag"=>100, "FlagString"=>"导出成功");
	}
	
	function export_end(){
		$file_path = $_SERVER['DOCUMENT_ROOT']."/admin/data_export/";
		$md5 = md5_file($file_path.$this->file_name);
		$fd = fopen($file_path.$this->file_name, 'a');
		fwrite($fd, "\r\n/*md5:".$md5."*/");
		fclose($fd);
		@unlink($file_path.$this->lock_name);
		return array("Flag"=>100, "FlagString"=>"操作成功", "MD5"=>$md5);
	}
	
	function force_end(){
		$file_path = $_SERVER['DOCUMENT_ROOT']."/admin/data_export/";
		@unlink($file_path.$this->lock_name);
		return array("Flag"=>100, "FlagString"=>"操作成功");
	}
	
	private function open_file(){
		$file_path = $_SERVER['DOCUMENT_ROOT']."/admin/data_export/";
		if($this->new_file && file_exists($file_path.$this->lock_name)){
			return false;
		}
		if($this->new_file){
			@unlink($file_path.$this->file_name);
			touch($file_path.$this->lock_name);
		}
		return true;
	}
	
	private function write_file($database, $table){
		$file_path = $_SERVER['DOCUMENT_ROOT']."/admin/data_export/";
		
		$sql = "SELECT `SCHEMA_NAME`,`DEFAULT_CHARACTER_SET_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` = '".$database."'";
		$row = $this->db->get_row($sql, "ASSOC");
		$database_str .= "CREATE DATABASE IF NOT EXISTS `".$row['SCHEMA_NAME']."` DEFAULT CHARACTER SET ".$row['DEFAULT_CHARACTER_SET_NAME'].";\n";
		$database_str .= "USE `".$row['SCHEMA_NAME']."`;\n";
		$fd = fopen($file_path.$this->file_name, 'a');
		fwrite($fd, $database_str);
		fclose($fd);
		
		$search = array("=", "$", ">", "<", "|", "&", "(", ")", "{", "}", ";", "!");
		$replace = array("\\=", "\\$", "\\>", "\\<", "\\|", "\\&", "\\(", "\\)", "\\{", "\\}", "\\;", "\\!");
		$user = str_replace($search, $replace, $_SERVER['kkyooDB_NAME']);
		$pwd = str_replace($search, $replace, $_SERVER['kkyooDB_PASS']);
		return exec("/usr/local/mysql/bin/mysqldump -h ".$_SERVER['kkyooDB_HOST']." -u ".$user." -p".$pwd." ".$database." ".$table.">>".$file_path.$this->file_name);
	}
}