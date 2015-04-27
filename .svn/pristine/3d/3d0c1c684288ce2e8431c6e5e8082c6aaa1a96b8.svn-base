<?php

/**
 *   图片配置管理
 *   文件: picconfig.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class picconfig
{

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}

	/**
	 *   图片分类列表显示
	 *   @param	array	$message	分类查询条件
	 *   @return	array	$return		查询结果
	 */
	public function catList($message = array())
	{
		$r = $message;
		extract($message);

		//判断是否需要获得全部的分类
		if( $getAllCat == '1' )
		{
			$cats = $this->getCatName();
			return $cats;
		}
		$where = '1 = 1';
		if( !empty($cat_name) ) 
			$where .= " AND cat_name like '$cat_name%' ";

		if( !empty($state) ) 
			$where .= " AND state = '$state'";

		//查询一跳记录的时候执行
		if( !empty($id) )
			$where .= " AND id = '$id'";

		$table = DB_NAME_SYSTEM_CONFIG.'.pic_catagory';
		$dlhelper = new dlhelper($this->db);
		$lists = $dlhelper->findAllPage($table, $where);
		$page = $dlhelper->getPage();
		$return = array(
			'lists'  => $lists,
			'page'   => $page,
			'r'	=> $r
		);
		return $return;
	}

	/**
	 *   图片分类添加和修改
	 *   @param	array	$message	图片分类信息
	 *   @reutnr	array	$return		添加操纵结果
	 */
	public function addPicCat($message)
	{
		$return = array();
		extract($message);
		//验证数据是否正确
		if( empty($message['cat_name']) )
		{
			$return = array(
				'state'   => '0',
				'result'  => '请输入图片类别名称'
			);
			return $return;
		}
		$table = DB_NAME_SYSTEM_CONFIG.'.pic_catagory';

		//判断是否相同的图片类别名称
		$sql = "SELECT `id` FROM ". $table." WHERE cat_name = '$cat_name' "; 
		$num = $this->db->get_col($sql);
		if( $num > 0 )
		{
			if( $id > 0 ) 
			{
				$sql = " SELECT * FROM ".$table." WHERE id = '$id'";
				$mes = $this->db->get_row($sql);
				if( $cat_name != $mes['cat_name'] )
				{
					$return = array(
						'state'  => 0,
						'result' => '有重复的类别名称,请重新输入... '
					);
					return $return;
				}
			}
			else
			{
				$return = array(
					'state'  => 0,
					'result' => '有重复的类别名称,请重新输入... '
				);
				return $return;
			}
		}

		$data = array(
			'cat_name'   => $cat_name,
			'state'	     => $state,
			'ctime'	     => time()
		);
		$dl = new dlhelper($this->db);
		//更新数据
		if( $id > 0 ) 
		{
			$num = $dl->update($table, $data, "id = '$id'");
			if( $num > 0 ) 
			{
				$return = array(
					'state'	 => '1',
					'result' => '图片分类编辑成功',
					'mes'    => $data
				);
			}
			else 
			{
				$return = array(
					'state'	 => '0',
					'result' => '图片分类编辑失败',
					'mes'    => $data
				);
			}
			return $return;
		}
		//插入数据
		$num = $dl->insert($table, $data);
		if( $num > 0 ) 
		{
			$return = array(
				'state'	 => '1',
				'result' => '图片分类添加成功',
				'mes'    => $data
			);
		}
		else 
		{
			$return = array(
				'state'	 => '0',
				'result' => '图片分类添加失败',
				'mes'    => $data
			);
		}
		return $return;
	}

	/**
	 *   图片列表显示
	 *   @param	array	$message	图片查询条件
	 *   @return	array	$return		查询结果
	 */
	public function picList($message = array())
	{
		$r = $message;
		extract($message);
		$where = '1 = 1';

		if( !empty($cat_id) ) 
			$where .= " AND cat_id = '$cat_id' ";

		if( !empty($state) )
			$where .= " AND state = '$state' ";

		if( $id > 0 ) 
			$where .= " AND id = '$id'";

		$table = DB_NAME_SYSTEM_CONFIG.'.pic_manager';
		$dlhelper = new dlhelper($this->db);
		$lists = $dlhelper->findAllPage($table, $where);
		$newlist = array();

		//获取所有分类名称
		$cats = array();
		$cats = $this->getCatName();

		//对应类别id查询类别名称
		foreach($lists as $val) 
		{
			$cat_name = $this->getCatName($val['cat_id']);
			$val['cat_name'] = $cat_name[0]['cat_name'];
			$newlist[] = $val;
		}

		$page = $dlhelper->getPage();
		$return = array(
			'lists'  => $newlist,
			'page'   => $page,
			'r'	=> $r,
			'cats'  => $cats
		);
		return $return;
	}

	/**
	 *   根据id获得类别名称
	 *   @param	int	$id	   类别id
	 *   @return	string	$cat_name  类别名称
	 */
	public function getCatName($id = '')
	{
		$table = DB_NAME_SYSTEM_CONFIG.'.pic_catagory';
		$where = ' 1=1';
		if( !empty($id) )
			$where .= " AND id = '$id'";
		$sql = "SELECT `cat_name`,`id` FROM ".$table."  WHERE".$where;
		$cat_name = $this->db->get_results($sql);
		return $cat_name;
	}

	/**
	 *   图片添加
	 *   @param	array	$message	图片地址
	 *   @return	array	$return		返回操作结果
	 */
	public function picAdd($message)
	{
		$return = array();
		extract($message);
		//验证数据是否正确
		if( empty($message['cat_id']) )
		{
			$return = array(
				'state'   => '0',
				'result'  => '请选择图片类别'
			);
			return $return;
		}
		if( empty($message['pic_name']) )
		{
			$return = array(
				'state'   => '0',
				'result'  => '请输入图片名称'
			);
			return $return;
		}

		$table = DB_NAME_SYSTEM_CONFIG.'.pic_manager';
		$data = array(
			'cat_id'     => $cat_id,
			'pic_name'   => $pic_name,
			'state'	     => $state,
			'ctime'	     => time()
		);

		if( empty($id) )
			$data['img_path'] = $img_path;

		$dl = new dlhelper($this->db);
		//更新数据
		if( $id > 0 ) 
		{
			$num = $dl->update($table, $data, "id = '$id'");
			if( $num > 0 ) 
			{
				$return = array(
					'state'	 => '1',
					'result' => '图片编辑成功',
					'mes'    => $data
				);
			}
			else 
			{
				$return = array(
					'state'	 => '0',
					'result' => '图片编辑失败',
					'mes'    => $data
				);
			}
			return $return;
		}
		//插入数据
		$num = $dl->insert($table, $data);
		if( $num > 0 ) 
		{
			$return = array(
				'state'	 => '1',
				'result' => '图片分类添加成功',
				'mes'    => $data
			);
		}
		else 
		{
			$return = array(
				'state'	 => '0',
				'result' => '图片分类添加失败',
				'mes'    => $data
			);
		}
		return $return;
	}
	/**
	 *   查看原图
	 *   @param	array	$message	图片id 
	 *   @return	array	$return		图片地址
	 */
	public function showOriPic($message)
	{
		$id = $message['id'];
		$table = DB_NAME_SYSTEM_CONFIG.'.pic_manager'; 
		$sql = "SELECT * FROM ".$table." WHERE id = '$id'";
		$return = $this->db->get_row($sql);
		return $return;
	}

	/**
	 *   图片分类删除 
	 *   @param	array	$message	图片分类id
	 *   @return	array	$return		删除结果
	 */
	public function delPicCat($message)
	{
		$id = $message['id'];
		$table = DB_NAME_SYSTEM_CONFIG.'.pic_catagory';
		$sql = "DELETE FROM ".$table." WHERE id = '$id'";
		$num = $this->db->query($sql);
		if( $num > 0 )
		{
			$return = array(

				'state'   => '1',
				'result'  => '图片类别删除成功'
			);
		}
		else
		{
			$return = array(

				'state'   => '0',
				'result'  => '图片类别删除失败'
			);
		}
		return $return;
	}
}




