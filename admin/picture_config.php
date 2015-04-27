<?php
require_once '../library/global.fun.php';
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
$module = $_GET['module'] ? $_GET['module'] : 'catList';
switch($module) {

	case 'catList'  :	//图片分类显示
		if( $_GET['tj'] )
			$mypost = $_GET;
		else 
			$mypost = array();
		$param = array(
			'extparam' => array(
				'Tag'    => 'CatList', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '101',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片分类列表查看'
			)
		);
		$result = request($param);
		$lists = $result['lists'];
		$page = $result['page'];
		$case['cat_name'] = $_GET['cat_name']; 
		$case['state'] = $_GET['state'];
		$tem = "picconfig/picCatList.html";
		$link_array = getLevelLink(10002,10011,10019,101);
		break;
	
	case 'addPicCat' :	//图片分类添加，和修改
		if( $_POST['tj'] ) 
		{
			$mypost = $_POST;
			//判断是更新还是修改操作
			if( $_GET['id'] > 0)
			{
				//更新
				$param = array(
					'extparam' => array(
						'Tag'    => 'AddPicCat', 
						'mypost' => $mypost 
					),
					'param' => array(
						'BigCaseId'		=> '10002',
						'CaseId'    	=> '10011',
						'ParentId'  	=> '10019', 
						'ChildId'   	=> '103',
						'Uin' 	    	=> '',
						'SessionKey'	=> '',
						'ChannelId' 	=> 0,
						'TargetUin' 	=> '',
						'Client'    	=> 'WEB ADMIN',
						'DoingWeight'   => 1,
						'MoneyWeight'	=> 1,
						'Desc'			=> '图片分类修改成功'
					)
				);
				$result = request($param);
			}else{
				//添加
				$param = array(
					'extparam' => array(
						'Tag'    => 'AddPicCat', 
						'mypost' => $mypost 
					),
					'param' => array(
						'BigCaseId'		=> '10002',
						'CaseId'    	=> '10011',
						'ParentId'  	=> '10019', 
						'ChildId'   	=> '102',
						'Uin' 	    	=> '',
						'SessionKey'	=> '',
						'ChannelId' 	=> 0,
						'TargetUin' 	=> '',
						'Client'    	=> 'WEB ADMIN',
						'DoingWeight'   => 1,
						'MoneyWeight'	=> 1,
						'Desc'			=> '图片分类添加'
					)
				);
				$result = request($param);
			}
			//编辑后执行操作
			if( $_GET['id'] > 0 ) 
				{
				if( $result['state'] == '1' ) 
					showMsg('修改成功','?module=catList');
				else 
					showMsg($result['result']);
			}
			//添加后执行操作
			if( $result['state'] == '1' ) 
			{
				showMsg($result['result'], '?module=catList');
			} 
			else 
			{
				showMsg($result['result']);
			}
		}
		$link_array = getLevelLink(10002,10011,10019,101);
		//如果是修改页面，查询分类信息
		if( $_GET['id'] )
		{
			$mypost = array('id' => $_GET['id']);
			$param = array(
				'extparam' => array(
					'Tag'    => 'CatList', 
					'mypost' => $mypost 
				),
				'param' => array(
					'BigCaseId'		=> '10002',
					'CaseId'    	=> '10011',
					'ParentId'  	=> '10019', 
					'ChildId'   	=> '101',
					'Uin' 	    	=> '',
					'SessionKey'	=> '',
					'ChannelId' 	=> 0,
					'TargetUin' 	=> '',
					'Client'    	=> 'WEB ADMIN',
					'DoingWeight'   => 1,
					'MoneyWeight'	=> 1,
					'Desc'			=> '图片分类列表查看'
				)
			);
			$result = request($param);
			$list = $result['lists']['0'];
			$link_array = getLevelLink(10002,10011,10019,101);
		}
		$tem = 'picconfig/addPicCat.html';
		break;

	case  'picList' :	//图片显示
		if( !empty($_GET['tj']) ) 
			$mypost = $_GET;
		else 
			$mypost = array();
		$mypost['show_page'] = true;//显示分页
		$param = array(
			'extparam' => array(
				'Tag'    => 'PicList', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '104',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片列表查看'
			)
		);
		$result = request($param);
		$lists = $result['lists'];
		$page = $result['page'];
		$cats = $result['cats'];

		$case['cat_id'] = $_GET['cat_id'];
		$case['state'] = $_GET['state'];
		$tem = 'picconfig/picList.html';
		$link_array = getLevelLink(10002,10011,10019,104);
		break;
	case 'addPic'   :		//添加图片
		$mypost = array('getAllCat' => '1');
		//查询所有图片分类
		$param = array(
			'extparam' => array(
				'Tag'    => 'CatList', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '101',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片分类列表查看'
			)
		);
		$result = request($param);
		$cats = $result;
		if( !empty($_POST['tj']) )
		{
			$mypost = $_POST;
			//调用上传文件的接口
			if($_FILES['img_path']['error'] == 0){
				$bytes = file_get_contents($_FILES['img_path']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				
				if($query['rst'] != 100){
					echo '图片上传失败';
					exit;
				}
				$mypost['img_path'] = $index;
			}
			if( $_GET['id'] > 0 ){
				$childid = '106';
				$desc = '修改图片';
			}else{
				$childid = '105';
				$desc = '添加图片';
			}
			$param = array(
				'extparam' => array(
					'Tag'    => 'PicAdd', 
					'mypost' => $mypost 
				),
				'param' => array(
					'BigCaseId'		=> '10002',
					'CaseId'    	=> '10011',
					'ParentId'  	=> '10019', 
					'ChildId'   	=> $childid,
					'Uin' 	    	=> '',
					'SessionKey'	=> '',
					'ChannelId' 	=> 0,
					'TargetUin' 	=> '',
					'Client'    	=> 'WEB ADMIN',
					'DoingWeight'   => 1,
					'MoneyWeight'	=> 1,
					'Desc'			=> $desc
				)
			);
			$result = request($param);
			if( $_GET['id'] > 0 ) {
				if( $result['state'] == '1' ) 
					showMsg('修改成功','?module=picList');
				else 
					showMsg($result['result']);
			}
			//添加后执行操作
			if( $result['state'] == '1' ) {
				exit("<script language='javascript'>if(confirm('添加成功，是否继续添加？')){history.go(-1);}else{location.href='?module=picList';}</script>");
			} else {
				showMsg($result['result']);
			}
		}
		if( $_GET['id'] > 0 ){
			//查询出当前数据的信息
			$mypost = array('id' => $_GET['id']);
			$param = array(
				'extparam' => array(
					'Tag'    => 'PicList', 
					'mypost' => $mypost 
				),
				'param' => array(
					'BigCaseId'		=> '10002',
					'CaseId'    	=> '10011',
					'ParentId'  	=> '10019', 
					'ChildId'   	=> '104',
					'Uin' 	    	=> '',
					'SessionKey'	=> '',
					'ChannelId' 	=> 0,
					'TargetUin' 	=> '',
					'Client'    	=> 'WEB ADMIN',
					'DoingWeight'   => 1,
					'MoneyWeight'	=> 1,
					'Desc'			=> '图片列表查看'
				)
			);
			$result = request($param);
			$message = $result['lists'][0];
		}
		$link_array = getLevelLink(10002,10011,10019,104);
		$tem = 'picconfig/addPic.html';
		break;
		/*
	case 'showOriPic' :	//查看原图

		//查找图片
		$mypost = array('id' => $_GET['id']);
		$param = array(
			'extparam' => array(
				'Tag'    => 'ShowOriPic', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '104',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片列表查看'
			)
		);
		$img = request($param);
		//查询出所有分类
		$mypost = array('getAllCat' => '1');

		$link_array = getLevelLink(10002,10011,10019,104);

		$param = array(
			'extparam' => array(
				'Tag'    => 'CatList', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '101',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片分类列表查看'
			)
		);
		$cats = request($param);
		$tem = 'picconfig/showOriPic.html';
		break;*/
	case 'delPicCat' :	//图片分类删除
		$mypost = array('id' => $_GET['id']);
		$param = array(
			'extparam' => array(
				'Tag'    => 'DelPicCat', 
				'mypost' => $mypost 
			),
			'param' => array(
				'BigCaseId'		=> '10002',
				'CaseId'    	=> '10011',
				'ParentId'  	=> '10019', 
				'ChildId'   	=> '107',
				'Uin' 	    	=> '',
				'SessionKey'	=> '',
				'ChannelId' 	=> 0,
				'TargetUin' 	=> '',
				'Client'    	=> 'WEB ADMIN',
				'DoingWeight'   => 1,
				'MoneyWeight'	=> 1,
				'Desc'			=> '图片分类删除'
			)
		);
		$result = request($param);
		if( $result['state'] == '1' )
			showMsg($result['result'], '?module=catList');
		else
			showMsg($result['result'], '?module=catList');
		break;
}
//ajax 图片联动
if( $module == 'linkPic' )
{

	$mypost = array('cat_id' => $_GET['cat_id']);
	$param = array(
		'extparam' => array(
			'Tag'    => 'PicList', 
			'mypost' => $mypost 
		),
		'param' => array(
			'BigCaseId'		=> '10002',
			'CaseId'    	=> '10011',
			'ParentId'  	=> '10019', 
			'ChildId'   	=> '104',
			'Uin' 	    	=> '',
			'SessionKey'	=> '',
			'ChannelId' 	=> 0,
			'TargetUin' 	=> '',
			'Client'    	=> 'WEB ADMIN',
			'DoingWeight'   => 1,
			'MoneyWeight'	=> 1,
			'Desc'			=> '图片列表查看'
		)
	);
	$result = request($param);
	$lists = $result['lists'];
	echo json_encode($lists);
	exit;
}
include template($tem, $tpl);