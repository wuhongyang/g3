<?php
/**
* MongoDB Gridfs 存取图片
* 图片上传，修改，读取，删除
*/
class gridfs
{
	private $gridfs;

	function __construct(){
		//$db = domain::main()->GroupDBConn('mongo');
        $db = db::connect(config('mongodb','pic'),'mongo');
		$this->gridfs = $db->getGridfs();
	}

// 	//删除文件
// 	function delete($where,$optios = array()){
// 		if(empty($where)) return array('Flag'=>101,'FlagString'=>'条件不能为空');
// 		foreach($where as $val){
// 			if(empty($val)) return array('Flag'=>101,'FlagString'=>'条件不能为空');
// 		}
// 		$rst = $this->gridfs->remove($where, $optios);
// 		if( ! $rst) return array('Flag'=>101,'FlagString'=>'删除失败');
// 		return array('Flag'=>100,'FlagString'=>'删除成功');
// 	}

// 	//保存文件
// 	function save($path,$extra = array()){
// 		if( ! file_exists($path)) return array('Flag'=>101,'FlagString'=>'文件不存在');
// 		$id = $this->gridfs->storeFile($path,(array)$extra);
// 		$md5 = md5_file($path);
// 		@unlink($path);
// 		if(empty($id)) return array('Flag'=>102,'FlagString'=>'保存失败');
// 		return array('Flag'=>100,'File'=>$md5);
// 	}

// 	//保存唯一文件
// 	function saveUnique($path,$unique,$extra = array()){
// 		$unique = (array)$unique;
// 		$extra = (array)$extra;
// 		if( ! file_exists($path)) return array('Flag'=>101,'FlagString'=>'文件不存在');
// 		if(empty($unique)) return array('Flag'=>102,'FlagString'=>'索引值错误');
// 		$exists = $this->gridfs->findOne($unique,array('md5'));
// 		if( ! empty($exists)) $this->delete($unique);
// 		$extra = array_merge($extra,$unique);
// 		$id = $this->gridfs->storeFile($path,$extra);
// 		$md5 = md5_file($path);
// 		@unlink($path);
// 		if(empty($id)) return array('Flag'=>103,'FlagString'=>'保存失败');
// 		return array('Flag'=>100,'File'=>$md5);
// 	}

// 	//二进制数据保存
// 	function saveBytes($bytes, $extra = array()){
// 		if(empty($bytes)) return array('Flag'=>101,'FlagString'=>'无效数据');
// 		$id = $this->gridfs->storeBytes($bytes,(array)$extra);
// 		if(empty($id)) return array('Flag'=>102,'FlagString'=>'保存失败');
// 		$md5 = md5($bytes);
// 		return array('Flag'=>100,'File'=>$md5);
// 	}

// 	//二进制数据保存为唯一
// 	function saveBytesUnique($bytes,$unique,$extra = array()){
// 		$unique = (array)$unique;
// 		$extra = (array)$extra;
// 		if(empty($bytes)) return array('Flag'=>101,'FlagString'=>'无效数据');
// 		if(empty($unique)) return array('Flag'=>102,'FlagString'=>'索引值错误');
// 		$exists = $this->gridfs->findOne($unique,array('_id'));
// 		if( ! empty($exists)) $this->delete($unique);
// 		$extra = array_merge($extra,$unique);
// 		$id = (array)$this->gridfs->storeBytes($bytes,$extra);
// 		if(empty($id)) return array('Flag'=>103,'FlagString'=>'保存失败');
// 		$md5 = md5($bytes);
// 		return array('Flag'=>100,'File'=>$md5);
// 	}

	//按条件获取文件数据
	function getFile($where){
		$bytes = NULL;
		$file = $this->gridfs->findOne($where);
		if($file) $bytes = $file->getBytes();
		if(strlen($bytes) > 0){
			return array('Flag'=>100,'Bytes'=>base64_encode($bytes));
		}else{
			return array('Flag'=>101,'FlagString'=>'文件不存在');
		}
	}

// 	//按MD5获取文件数据
// 	function getByMd5($md5){
// 		$bytes = NULL;
// 		$file = $this->gridfs->findOne(array('md5'=>$md5));
// 		if($file) $bytes = $file->getBytes();
// 		if(strlen($bytes) > 0){
// 			return array('Flag'=>100,'Bytes'=>base64_encode($bytes));
// 		}else{
// 			return array('Flag'=>101,'FlagString'=>'文件不存在');
// 		}
// 	}

}

