<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);
$groupId = intval($_GET['group']);
//规模数组
$maxuser_arr = array(
	"50" => "50人弦音房间",
	"100" => "100人弦音房间",
	"300" => "300人弦音房间",
	"500" => "500人弦音房间"
);

if($module=='roomList' || empty($module)){
	//当前使用站
	$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
	$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

	if(!$_GET['group']){
	    $_GET['group'] = $__ADMIN_CURGROUP['groupid'];
	    $groupId = intval($_GET['group']);
	}
	$link_array = getLevelLink(10002,10003,10020,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10003,
			"ParentId"   => 10020,
			"ChildId"	 => 101,
			"Desc"		 => "房间列表"
		),
		'extparam'=>array(
			"Tag" 		 => "RoomsList",
			"SearchData" => $_GET,
			"GroupId" => $groupId
		)
	);
	$result = request($param);
	$page = $result['li']['page'];
	$roomList = array();
	if($result['li']){
		unset($result['li']['page']);
		$roomList = (array)$result['li'];
	}
	$status_array = array(0=>'冻结',1=>'正常',2=>'指定成员',3=>'黑名单',4=>'关闭');
    $temp = 'rooms_list.html';
}elseif($module == 'freeze'){
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10003,
			"ParentId"   => 10020,
			"ChildId"	 => 102,
			"Desc"		 => "冻结，解冻房间"
		),
		'extparam'=>array(
			"Tag" 	=> "Freeze",
			"Id" 	=> $_GET['id'],
			"GroupId" => $groupId
		)
	);
	$result = request($param);
	ShowMsg($result['FlagString'],'?module=roomList&group='.$groupId);
}elseif($module == 'room_update'){
    $_POST['name'] = empty($_POST['name'])? $_POST['id'] : addslashes(htmlspecialchars(mb_substr($_POST['name'], 0, 20, 'UTF-8'),ENT_QUOTES));
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10003,
			"ParentId"   => 10020,
			"ChildId"	 => 103,
			"Desc"		 => "修改房间",
		),
		'extparam'=>array(
			"Tag" 	=> "RoomUpdate",
			"Id" 	=> intval($_POST['id']),
			"Name"  => $_POST['name'],
			"SortId" => intval($_POST['sortid']),
			"RobotNum" => intval($_POST['robot_num']),
			"RoomVersion" => $_POST['room_version'],
			"Maxuser" => $_POST['maxuser'],
			"EntertainerQuota"=>intval($_POST['entertainer_quota']),
			"ui_version"=>intval($_POST['ui_version']),
			"GroupId" => $groupId
		)
	);
	$result = request($param);
	if($result['Flag']==100){
		ShowMsg($result['FlagString'],'?module=roomList&group='.$groupId);
	}
	else{
		ShowMsg($result['FlagString'],-1);
	}
}/*elseif($module == 'room_add'){
	$data = $_POST;
	$data['region_id'] = ($_POST['area']==-1) ? intval($_POST['city']) : intval($_POST['area']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10020,
			"ChildId"	 => 104,
			"Desc"		 => "添加房间"
		),
		'extparam'=>array(
			"Tag" 	=> "RoomAdd",
			"Data"  => $data
		)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],'?module=roomList');
	}else{
		ShowMsg($result['FlagString'],-1);
	}
}elseif($module == 'admin_recmd'){
	$roomid = intval($_GET['id']);
	if($_POST){
		$Showlist['rule'] = $_POST['rule'];
		$Showlist['desc'] = $_POST['info']['desc'];
		$param = array(
				'param'=>array(
						"BigCaseId"  => 10002,
						"CaseId"	 => 10003,
						"ParentId"   => 10020,
						"ChildId"	 => 105,
						"Desc"		 => "热荐/取消",
						'GroupId'	 => $groupId
				),
				'extparam'=>array(
						"Tag" 	=> "SetRoomRec",
						"Showlist"  => $Showlist,
						"Roomid"  => $_POST['roomid'],
						"Recstatus"  => $_POST['recstatus'],
				)
		);
		$result = request($param);
		if($result['Flag'] == 100){
			ShowMsg($result['FlagString'],'?module=roomList');
		}else{
			ShowMsg($result['FlagString'],'?module=roomList');
		}
	}else{
		$param = array(
				'param'=>array(
						"BigCaseId"  => 10002,
						"CaseId"	 => 10003,
						"ParentId"   => 10020,
						"ChildId"	 => 105,
						"Desc"		 => "热荐/取消",
						'GroupId'	 => $groupId
				),
				'extparam'=>array(
						"Tag" 	=> "GetRoomRec",
						"Roomid"  => $roomid,
				)
		);
		$result = request($param);
		$data = json_decode(urldecode($result['Data']['program_list']),true);
		$desc = $data['desc'];
		$data = $data['rule'];
	}
    $temp = 'roomrecmd_info.html';
}*/elseif($module == 'rooms_ui_list'){
    $link_array = getLevelLink(10002,10069,10337,101);
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>101,"Desc"=>"查看UI列表"),
        'extparam'=>array("Tag"=>"GetRoomsUi",'IsPage'=>true)
    );
    $result = request($param);
    $rooms_ui = (array)$result['Result'];
    $page = $result['Page'];
    unset($result);
    $status = array('未启用','启用');
    $temp = $module.'.html';
}elseif($module == 'update_rooms_ui'){
    $link_array = getLevelLink(10002,10069,10337,101);
    if($_GET['id'] > 0){
        $param = array(
            'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>101,"Desc"=>"查看UI列表"),
            'extparam'=>array("Tag"=>"GetRoomsUi","Data"=>array('id'=>(int)$_GET['id']))
        );
        $result = request($param);
        $edit = $result['Result'][0];
        $edit['pics'] = json_decode($edit['pics'],true);
    }
    if(!empty($_POST)){
		/*$filename = empty($edit['files'])? date('YmdHis') : $edit['files'];
		$file0 = __BASE__.'/themes/roomui/'.$filename.'_start.swf';
		$file1 = __BASE__.'/themes/roomui/'.$filename.'_skin.swf';
		$file2 = __BASE__.'/themes/roomui/'.$filename.'_layout.xml';
		if(!empty($_FILES['files']['tmp_name']['start'])){
			$rst0 = move_uploaded_file($_FILES['files']['tmp_name']['start'],$file0);
            if(!$rst0){
                alertMsg('上传启动界面文件失败，请检查"themes/roomui"目录权限');
            }
		}
		if(!empty($_FILES['files']['tmp_name']['skin'])){
            $rst1 = move_uploaded_file($_FILES['files']['tmp_name']['skin'],$file1);
            if(!$rst1){
                alertMsg('上传房间皮肤文件失败，请检查"themes/roomui"目录权限');
            }
		}
		if(!empty($_FILES['files']['tmp_name']['layout'])){
            $rst2 = move_uploaded_file($_FILES['files']['tmp_name']['layout'],$file2);
            if(!$rst2){
                alertMsg('上传房间布局文件失败，请检查"themes/roomui"目录权限');
            }
		}
        */
        $_POST['uptime'] = time();
        //$_POST['files'] = $filename;
        //if(empty($_POST['files'])) unset($_POST['files']);
        $_POST = array_merge((array)$edit,(array)$_POST);
        $_POST['pics'] = json_encode($_POST['pics']);
        $param = array(
            'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>102,"Desc"=>"更新UI"),
            'extparam'=>array("Tag"=>"UpdateRoomUi","Data"=>$_POST)
        );
        $result = request($param);
        if($result['Flag'] != 100) alertMsg($result['FlagString']);
        alertMsg($result['FlagString'],'?module=rooms_ui_list');
    }
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode($cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode($pic['lists']);
    $temp = $module.'.html';
}elseif($module == 'del_rooms_ui'){
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>103,"Desc"=>"删除UI列表"),
        'extparam'=>array("Tag"=>"DeleteRoomUi","Data"=>(int)$_GET['id'])
    );
    $result = request($param);
    alertMsg($result['FlagString'],'?module=rooms_ui_list');
}elseif($module == 'ui_package_list'){
    $link_array = getLevelLink(10002,10002,10399,101);
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>101,"Desc"=>"查看UI方案"),
        'extparam'=>array("Tag"=>"GetUiPackage", "Data"=>array("page"=>1))
    );
    $result = request($param);
    $rooms_ui = $result['Result'];
    $page = $result['Page'];
    $status = array('未启用','启用');
    $temp = $module.'.html';
}elseif($module == 'copy_ui_package'){
	$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>104,"Desc"=>"复制房间方案"),
			'extparam'=>array("Tag"=>"CopyUiPackage","Id"=>$_GET['id'])
	);
	$result = request($param);
	alertMsg($result['FlagString'],'?module=ui_package_list');
}elseif($module == 'update_ui_package'){
    $videocode = array('h264');
    $videosize = array('640*480','320*240','160*120');
    $audiocode = array('NellyMoser','Speex');
    $audiohz = array('96000','44100','32000','22050','16000','11025','8000');
    $highquality_audiocode = array('HE-AAC');
	if(!empty($_POST)){
		$_POST['play_media_conf'] = json_encode((array)$_POST['play_media_conf']);
		$_POST['admin_media_conf'] = json_encode((array)$_POST['admin_media_conf']);
		$_POST['p2p_media_conf'] = json_encode((array)$_POST['p2p_media_conf']);
		$gift = array();
		if(count($_POST['gift_cate_name']) != count(array_unique($_POST['gift_cate_name']))){
			alertMsg("存在相同分类名称", -1);
		}
		foreach((array)$_POST['gifts'] as $k=>$v){
			if($_POST['gift_cate_name'][$k]){
				$gift[] = array("cate_name"=>$_POST['gift_cate_name'][$k], "ids"=>$_POST['gifts'][$k]);
			}
		}
		unset($_POST['gift_cate_name']);
		unset($_POST['gifts']);
		$_POST['gifts'] = addslashes(json_encode($gift));
        if(empty($_POST['width'])) $_POST['width'] = '100%';
        if(empty($_POST['height'])) $_POST['height'] = '100%';
		$_POST['uptime'] = time();
        $param = array(
            'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>102,"Desc"=>"更新UI方案"),
            'extparam'=>array("Tag"=>"UpdateUiPackage","Data"=>$_POST)
        );
        $result = request($param);
		if($result['Flag']==100){
			alertMsg($result['FlagString'],'?module=ui_package_list');
		}else{
			alertMsg($result['FlagString']);
		}
	}
	//查询房间UI版本
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>101,"Desc"=>"查看UI列表"),
        'extparam'=>array("Tag"=>"GetRoomsUi",'Data'=>array('status'=>1),'Getpic'=>true)
    );
    $result = request($param);
    $rooms_ui = $result['Result'];
	//礼物列表
    $param = array(
    		'param' => array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>131,"Desc"=>"分站礼物信息"),
    		'extparam' => array("Tag"=>"GetGiftCate", "NoPage"=>true)
    );
    $result = request($param);
    $gifts = $result['List'];
    $gift_name = array();
    foreach($gifts as $k=>$v){
    	if($v['status'] == 0){
    		unset($gifts[$k]);
    	}else{
    		$info = array(
    				'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>112,"Desc"=>"分站礼物列表"),
    				'extparam'=>array("Tag"=>"PropsList","Data"=>array('props_status'=>1, 'cate_id'=>$v['cate_id']))
    		);
    		$result = request($info);
    		$gifts[$k]['gifts'] = $result['list'];
    		foreach($result['list'] as $one){
    			$gift_name[$one['id']] = $one['props_name'];
    		}
    	}
    }
    //表情列表
    $param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
    		'extparam' => array('Tag'=>'ExpressionCateList','NoPage'=>true)
    );
    $result = request($param);
    $expression = $result['List'];
    foreach($expression as $k=>$v){
    	if($v['status'] == 0){
    		unset($expression[$k]);
    	}
    }
    //印章列表
    $param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10443,'ChildId'=>101),
    		'extparam' => array('Tag'=>'StampCateList','NoPage'=>true)
    );
    $result = request($param);
    $stamp = $result['List'];
    foreach($stamp as $k=>$v){
    	if($v['status'] == 0){
    		unset($stamp[$k]);
    	}
    }
	//功能道具
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>110,"Desc"=>"功能道具列表"),
		'extparam'=>array("Tag"=>"FunctionPropsList","Data"=>array('props_status'=>1))
	);
	$func_props = request($info);
	$func_props = $func_props['list'];
	//游戏道具列表
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10648,"ChildId"=>111,"Desc"=>"游戏道具列表"),
		'extparam'=>array("Tag"=>"GamePropsList","Data"=>array('props_status'=>1))
	);
	$game_props = request($info);
	$game_props = $game_props['list'];
	//互动游戏列表
	$info = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>101,"Desc"=>"分站游戏列表"),
		'extparam'=>array("Tag"=>"InteractList","Data"=>array('interact_status'=>1))
	);
	$flash_games = request($info);
	$flash_games = $flash_games['list'];
    
	//修改信息
	if($_GET['id']>0){
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>101,"Desc"=>"查看UI方案"),
			'extparam'=>array("Tag"=>"GetUiPackage",'Data'=>array('id'=>$_GET['id']))
		);
		$result = request($param);
		$edit = $result['Result'][0];
		$edit['play_media_conf'] = json_decode($edit['play_media_conf'],true);
		$edit['admin_media_conf'] = json_decode($edit['admin_media_conf'],true);
		$edit['p2p_media_conf'] = json_decode($edit['p2p_media_conf'],true);
		$edit['gifts'] = json_decode($edit['gifts'], true);
		$edit['gift_name'] = array();
        
		foreach((array)$edit['gifts'] as $k=>$v){
			if($v['ids']){
				$edit['gifts'][$k]['gift_id'] = explode(",", $v['ids']);
				foreach($edit['gifts'][$k]['gift_id'] as $gift_id){
					$edit['gift_name'][$k][$gift_id] = $gift_name[$gift_id];
				}
			}
		}
        
        $edit['expression_name'] = fill_id_with_name($edit['expression'], make_id_to_name($expression, 'cate_id', 'cate_name'));
        $edit['stamp_name'] = fill_id_with_name($edit['stamp'], make_id_to_name($stamp, 'parent_id', 'name'));
        $edit['func_name'] = fill_id_with_name($edit['func_props'], make_id_to_name($func_props, 'id', 'props_name'));
        $edit['game_name'] = fill_id_with_name($edit['game_props'], make_id_to_name($game_props, 'id', 'props_name'));
        $edit['flash_name'] = fill_id_with_name($edit['flash_games'], make_id_to_name($flash_games, 'id', 'interact_name'));
        $edit['tricky_name'] = fill_id_with_name($edit['tricky'], $gift_name);
	}
    $temp = $module.'.html';
}elseif($module == 'del_ui_package'){
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>103,"Desc"=>"删除UI方案"),
        'extparam'=>array("Tag"=>"DeleteUiPackage","Data"=>(int)$_GET['id'])
    );
    $result = request($param);
    alertMsg($result['FlagString'],'?module=ui_package_list');
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('rooms/'.$temp,$tpl);

function make_id_to_name($arr, $id_row, $name_row){
    $id_to_name = array();
    
    foreach($arr as $one){
		$id_to_name[$one[$id_row]] = $one[$name_row];
	}
    
    return $id_to_name;
}

function fill_id_with_name($ids, $id_to_name_arr){
    if(!$ids){
        return array();    
    }
    
    $id_with_name = array();
    
    if(!is_array($ids)){
        $ids = explode(',', $ids);
    }
    foreach($ids as $id){
		$id_with_name[$id] = $id_to_name_arr[$id] ? $id_to_name_arr[$id] : "<span style='color:red'>错误数据(id:".$id.")</span>";
	}
    
    return $id_with_name;
}