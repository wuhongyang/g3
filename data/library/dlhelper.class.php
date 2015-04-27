<?php
/*
* 查询辅助类
* 该类封装了一些常用的表查询操作与分页功能
*
* @author Leon(tmkook@gmail.im)
* @date 2010-12-20
* @copyright 杭州奥点科技有限公司
*/
require_once 'page.class.php';
class dlhelper
{
	public $db;
	public $total;
	public $showNum = 20;
	public $page;
	public $ispage; // 分页class
	public $showPage = true; //是否显示分页控制
	public $isShow = 0;
	private $debug=false;

	/**
	* 构造函数
	* 初始化DB类
	*/
	public function __construct($db){
		if(is_object($db)){
			$this->db = $db;
		}else{
			die('No DB Object');
		}
		//是否有分页类
		if(class_exists('extpage')){
			$this->ispage = 'extpage';
			$this->pageInit();
		}elseif(class_exists('page')){
			$this->ispage = 'page';
			$this->pageInit();
		}else{
			$this->ispage = false;
		}
	}

	/**
	* 分页初始化
	*/
	protected function pageInit(){
		$page = intval($_GET['page']); //当前页
		if($page > 1){
			$this->page = $page;
		}else{
			$this->page = 1;
		}
	}

	/**
	* 查询表内容并分页
	*
	* @parame table 传入一个表名
	* @parame order 传入一个排序键值
	* @return imresult 传入表的查询结果
	*/
	public function findAllPage($table,$where='',$order='',$select='*'){
		if($where){
			$where = "WHERE {$where}";
		}
		if($order){
			$order = "ORDER BY {$order}";
		}
		if($this->showPage == true)
			$limit = 'LIMIT '.($this->page-1) * $this->showNum.','.$this->showNum;

		/*get_var 和 get_col*/
		if($this->showPage == true){
			$sql = "SELECT COUNT(*) FROM {$table} {$where};";
			if(strpos($sql,'group by') || strpos($sql,'GROUP BY')){
				$total = $this->db->get_col($sql);
			}else{
				$total = $this->db->get_var($sql);
			}
			$this->total = $total;
		}

		$sql = "SELECT {$select} FROM {$table} {$where} {$order} {$limit}";
		$result = $this->db->get_results($sql,'ASSOC');
		if($this->debug == true){
			echo $sql.'<br />';
			echo mysql_error();
		}
		return $result;
	}

	/*
	* 统计表中一列的总和
	* @parame table 表名
	* @parame field 要统计列名
	* @parame where 条件
	*/
	public function countSum($table,$field,$where=''){
		if($where){
			$where = "WHERE {$where}";
		}
		$sql = "SELECT SUM({$field}) AS sum FROM {$table} {$where}";
	 	$sum = $this->db->get_row($sql);
		if($this->debug == true){
			echo $sql.'<br />';
			echo mysql_error();
		}
		return ($sum['sum'])? $sum['sum'] : 0;
	}

	/*
	* 获得分页结果
	*/
	public function getPage(){
		if($this->showPage == true){
			$p = new $this->ispage(
				array('total'=>$this->total,'perpage'=>$this->showNum)
			);
			if( $this->isShow == '1' )
				return $p->show1();
			return $p->show();
		}else{
			return false;
		}
	}

	//插入数据
	public function insert($table,$post){
		$fields = implode('`,`',array_keys($post));
		$values = implode("','",array_values($post));
		$sql = "INSERT INTO {$table} (`{$fields}`)VALUES('{$values}')";
		if($this->debug == true){
			echo $sql.'<br />';
			echo mysql_error();
		}
		if($this->db->query($sql)){
			$insert_id = $this->db->insert_id();
			if($insert_id){
				return $insert_id;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}

	//更新数据
	public function update($table,$data,$where=''){
		if(is_array($data)){
			$data = $this->_updateData($data);
		}
		if($where){
			$where = "WHERE {$where} ";
		}
		$sql = "UPDATE {$table} SET {$data} {$where}";
		if($this->debug == true){
			echo $sql.'<br />';
			echo mysql_error();
		}
		return $this->db->query($sql);
	}

	public function delete($table,$where){
		$sql = "DELETE FROM {$table} WHERE {$where}";
		if($this->debug == true){
			echo $sql.'<br />';
			echo mysql_error();
		}
		return $this->db->query($sql);
	}

	//更新数据格式解析
	public function _updateData($post){
		$update = array();
		$alias = $this->_alias;
		foreach($post as $key=>$val){
			if(isset($alias[$key])){
				$update[] = "`$alias[$key]`='$val'";
			}else{
				$update[] = "`$key`='$val'";
			}
		}
		return implode(',',$update);
	}

	//显示调试SQL语句
	public function debug($debug=false){
		$this->debug = $debug;
	}

}
