<?php
require_once '../library/global.fun.php';

$module = $_GET['module'] ? $_GET['module'] : 'GroupList';

$svn_password = 'websync';
$svn_username = 'websync';
$svn_path = 'svn://svn.vvku.com/htdocs/G3_WEB';

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));

switch($module) {
	case 'GroupList'  :		//显示群组
		$link_array = getLevelLink(10002,10003,10002,101);
        $param = array(
            'extparam'  => array(
                'Tag'   => 'TplList',
                'Data'  => array('NoPage'=>true)
            ),
            'param'     => array(
                'BigCaseId' => 10002,
                'CaseId'    => 10069,
                'ParentId'  => 10648,
                'ChildId'   => 101,
                'Desc'      => '模板列表读取'
            )
        );
        $res = request($param);
        $template_arr = array();
        foreach($res['Data'] as $one){
            $template_arr[$one['id']] = $one['name'];
        }
        
		$param = array(
			'extparam'=>array('Tag'=>'listGroup','Data'=>array('Province'=>$_GET['province'],'City'=>$_GET['city'],'Area'=>$_GET['area'],'Key_name'=>$_GET['type'],'Val'=>$_GET['q'],'Recommend'=>$_GET['recommend'],'IsUse'=>$_GET['is_use'],'TplId'=>$_GET['tpl_id'])),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'群列表')
		);
		$result = request($param);
		$lists = $result['lists'];
		$page = $result['page'];
		include template('group/group.html',$tpl);
		break;
	case 'groupInfo':
		$groupid = intval($_GET['group_id']);
		$param = array(
			'extparam'=>array('Tag'=>'GroupInfo','Info'=>array('Group_id'=>$groupid)),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'站详情')
		);
		$info = request($param);
		exit(json_encode((array)$info['GroupInfo']));
		break;
	case 'info':
		$id = intval($_GET['edit']);
		if($id > 0){
			$param = array(
				'extparam'=>array('Tag'=>'listGroup','Data'=>array('Id'=>$id,'IsUse'=>$_GET['is_use'])),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'群列表')
			);
			$case = request($param);
			$case = $case['lists'][0];
			$room_ui_json = empty($case['room_ui']) ? '[]' : $case['room_ui'];
			$case['room_ui'] = json_decode($case['room_ui'], true);
			$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10002,"ChildId"=>101),
				'extparam'=>array("Tag"=>"ExtInfo",'GroupId'=>$case['groupid'])
			);
			$info = request($param);
			$info = $info['Info'];
			if($info['template']){
				$versions = getSvnList($svn_path,$svn_username,$svn_password,'/branch_themes/'.$info['template'].'/');
			}
			$ext = array();
			if($info['ext']){
				$ext = (array)json_decode($info['ext'], true);
			}
			foreach($ext as $key=>$val){
				$key1 = rawurldecode($key);
				$ext[$key1]['descr'] = rawurldecode($val['descr']);
				$ext[$key1]['value'] = rawurldecode($val['value']);
			}
			$ktv_templates = array();
			if($info['ktv_template']){
				$ktv_templates = json_decode($info['ktv_template'], true);
			}
		}else{
			$ext = json_decode('{"kkyooDB_HOST":{"descr":"kkyooDB_HOST","value":""},"kkyooDB_NAME":{"descr":"kkyooDB_NAME","value":""},"kkyooDB_PASS":{"descr":"kkyooDB_PASS","value":""},"kkyooDB_PORT":{"descr":"kkyooDB_PORT","value":""},"mongoDB_HOST":{"descr":"mongoDB_HOST","value":""},"mongoDB_NAME":{"descr":"mongoDB_NAME","value":""},"mongoDB_PASS":{"descr":"mongoDB_PASS","value":""},"mongoDB_PORT":{"descr":"mongoDB_PORT","value":""}}', true);
			foreach($ext as $key=>$val){
				$key1 = rawurldecode($key);
				$ext[$key1]['descr'] = rawurldecode($val['descr']);
				$ext[$key1]['value'] = rawurldecode($val['value']);
			}
		}
		//模板
		$param = array(
			'extparam'=>array('Tag'=>'TplList','Data'=>array('NoPage'=>1,'Status'=>1)),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>101,'Desc'=>'模板列表')
		);
		$moduleList = request($param);
		$moduleList = (array)$moduleList['Data'];
		//房间界面
		$param = array(
	        'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>101,"Desc"=>"查看UI列表"),
	        'extparam'=>array("Tag"=>"GetRoomsUi", 'Data'=>array('status' => 1))
	    );
	    $result = request($param);
	    $room_ui = (array)$result['Result'];
	    $rooms_ui = array();
	    foreach ($room_ui as $key => $value) {
	    	$rooms_ui[$value['id']] = $value['name'];
	    }
	    unset($room_ui);
		//页面界面
		$group_sites = getSvnList($svn_path,$svn_username,$svn_password,'/branch_themes/');
		if(!$group_sites){
			$group_sites = array();
		}
		array_push($group_sites, 'default');

		//flash_ktv模板
		$flash_ktv_list = getSvnList('svn://svn.vvku.com/g3/kkyoo_ktv/client',$svn_username,$svn_password);
		//html_ktv模板
		$html_ktv_list = getSvnList($svn_path,$svn_username,$svn_password,'/branch_htmlktv/');
		
		$ktv_list = array_merge($flash_ktv_list,$html_ktv_list);
		$ktv_templates = array();
		if($info['ktv_template']){
			$ktv_templates = json_decode($info['ktv_template'], true);
		}
		include template('group/group_info.html',$tpl);
		break;
	case 'addGroup':
		$tag = 'AddGroup';
		$childId = 102;
	case 'editGroup'://群组添加
		if($_GET['recommend'] > 0){
			$param = array(
				'extparam'=>array('Tag'=>'Recommend','Id'=>(int)$_GET['recommend']),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'站推荐设置')
			);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=GroupList');
		}
		if(!empty($_POST['GroupName'])){
			$_POST['key'] = $_POST['key']?$_POST['key']:array();
			if(empty($_POST['key'])){
				alertMsg('请配置底部信息');
			}
			foreach($_POST['key'] as $key=>$value){
				if(!empty($_POST['key'][$key]) && is_string($_POST['key'][$key])){
					$val = rawurlencode(addslashes(trim($value)));
					$_POST['ext'][$val] = array(
						"descr" => rawurlencode(addslashes(trim($_POST['descr'][$key]))),
						"value" => rawurlencode(addslashes(trim($_POST['value'][$key])))
					);
				}
			}
			unset($_POST['descr']);
			unset($_POST['key']);
			unset($_POST['value']);

			if($_POST['Id'] > 0){
				$tag = 'EditGroup';
				$childId = 103;
			}

			$param = array(
				'extparam'=>array('Tag'=>$tag,'Data'=>$_POST),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>$childId,'Desc'=>'群列表')
			);
			$result = request($param);
			if($result['Flag'] == 100){
				showMsg('操作成功','?module=GroupList');
			}else{
				alertMsg($result['FlagString'], -1);
			}
		}
		break;
	case 'get_version':
		if($_GET['type'] == 1){
			//页面界面模板
			$svn_path = $svn_path.'/branch_themes/';
		}else{
			//房间界面模板
			if(strpos($_GET['template'],'html') === false){
				//flash界面模板
				$svn_path = 'svn://svn.vvku.com/g3/kkyoo_ktv/client/';
			}else{
				//html界面模板
				$svn_path = $svn_path.'/branch_htmlktv/';
			}
		}
		$versions = getSvnList($svn_path,$svn_username,$svn_password,$_GET['template'].'/');
		exit(json_encode((array)$versions));
		break;
	case 'sync':
		if($_POST['type']==1){
			//页面界面模板
			$fromPath = $svn_path.'/branch_themes/'.$_POST['template'].'/'.$_POST['version'].'/';
			$topath = 'themes/g3/group_site/'.$_POST['template'];
		}elseif($_POST['type']==2){
			//房间界面模板
			if(strpos($_POST['template'],'html') === false){
				//flash界面模板
				$fromPath = 'svn://svn.vvku.com/g3/kkyoo_ktv/client/'.$_POST['template'].'/'.$_POST['version'].'/';
				$topath = 'themes/g3/ktv/'.$_POST['template'];
			}else{
				//html界面模板
				$fromPath = $svn_path.'/branch_htmlktv/'.$_POST['template'].'/'.$_POST['version'].'/';
				$topath = $_POST['template'];
				$_POST['type'] = 3;//重置bucket类型
			}
		}
		if(empty($_POST['template']) || $_POST['template']==-1 || empty($_POST['version']) || $_POST['version']==-1){
			exit;
		}
		$UPLOAD_API_PATH = 'http://upload.vvku.com/folder/'.$_SERVER['HOSTNAME'].'/folder';
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'FROM_PAHT'=>$fromPath,'TO_PATH' =>$topath,'TYPE'=>$_POST['type']);
		$rst = socket_request($UPLOAD_API_PATH,$opt,true,600);
		echo $rst;
		exit;
		break;
	case 'sync_info':
		$group_id = intval($_GET['group_id']);
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10002,"ChildId"=>116),
			'extparam'=>array("Tag"=>"SyncInfo",'GroupId'=>$group_id)
		);
		$result = request($param);
		alertMsg($result['FlagString'],'?module=GroupList');
		break;
	case 'openNum'://修改房间额度
		if(!empty($_POST['groupid']) && !empty($_POST['uin']) && isset($_POST['open_num'])){
			$param = array(
				'extparam'=>array('Tag'=>'openNum','Data'=>array('Group_id'=>$_POST['groupid'],'Uin'=>$_POST['uin'],'Open_num'=>$_POST['open_num'])),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>112,'Desc'=>'修改房间额度')
			);
			$result = request($param);
			alertMsg($result['FlagString'],'?module=GroupList');
		}
		include template('group/openNum.html',$tpl);
		break;
	case 'game_manager':
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam'=>array('Tag'=>'EditGame','Data'=>$_POST),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>110,'Desc'=>'游戏管理')
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=GroupList');
			}else{
				alertMsg($rst['FlagString']);
			}
		}
		$group_id = intval($_GET['group_id']);
		$param = array(
			'extparam'=>array('Tag'=>'GroupInfo','Info'=>array('Group_id'=>$group_id)),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'站详情')
		);
		$groupInfo = request($param);
		$groupInfo = $groupInfo['GroupInfo'];
		if(empty($groupInfo)){
			alertMsg('站不存在');
		}
		$games = (array)json_decode($groupInfo['games'],true);
		if(empty($games)){
			$games = array(1=>array('name'=>'','url'=>''));
		}else{
			foreach ($games as $key => $val) {
				$games[$key]['name'] = urldecode($val['name']);
				$games[$key]['url'] = urldecode($val['url']);
			}
		}
		include template('group/game_manager.html',$tpl);
		break;
	case 'upload_gameicon':
		$key = isset($_GET['key']) ? intval($_GET['key']) : intval($_POST['key']);
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if(empty($_FILES)){
				alertMsg('图片太大，请重新上传');
			}
			if(!empty($_FILES['icon']['tmp_name'])){
				if(strpos($_FILES['icon']['type'], 'image') === false){
					alertMsg("上传图片格式必须为jpg，png，gif格式");
				}
				$size = $_FILES['icon']['size']/(pow(1024, 2));
				if($size > 2){
					alertMsg("上传图片不能大于2M，请重新上传");
				}
				$bytes = file_get_contents($_FILES['icon']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt),true);
				$rst = json_encode(array('Flag'=>$query['rst'],'File'=>$index));
			}else{
				alertMsg('不是一个上传图片');
			}
		}
		include template('group/upload_gameicon.html',$tpl);
		break;
	case 'game_interface_setting':
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam'=>array('Tag'=>'GameInterfaceSave','Data'=>$_POST),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>111,'Desc'=>'目睹游戏链接设置')
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=GroupList');
			}else{
				alertMsg($rst['FlagString']);
			}
		}else{
			$group_id = intval($_GET['group_id']);
			if($group_id < 1){
				alertMsg('非法参数');
			}
			$games = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'SCList')));
			$games = (array)$games['Data']['Result'];

			$param = array(
				'extparam'=>array('Tag'=>'GameInterfaceList','GroupId'=>$group_id),
				'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'目睹游戏链接')
			);
			$list = request($param);
			$list = (array)$list['List'];
			$urls = array();
			foreach ($list as $key => $val) {
				$urls[$val['parent_id']] = urldecode($val['url']);
			}
			include template('group/game_interface_setting.html',$tpl);
		}
		break;
	case 'practice_account_list':
		$link_array = getLevelLink(10002,10003,10002,101);
		$groupid 	= intval($_GET['group_id']);
		
		$param 		= array(
				'extparam'=>array(
					'Tag'	=>'GroupInfo',
					'Info'	=>array(
						'Group_id'=>$groupid
						)
					),
				'param'=>array(
					'BigCaseId'	=>10002,
					'CaseId'	=>10003,
					'ParentId'	=>10002,
					'ChildId'	=>101,
					'Desc'		=>'站详情'
					)
		);
		$info_res 	= request($param);
		if($info_res['Flag'] != 100){
			ShowMsg("获取站点信息失败", -1);
		}
		$group_name = $info_res['GroupInfo']['name'];
		
		$param 		= array(
				'extparam'=>array(
						'Tag'		=>'PracticeAccountList',
						'GroupId'	=>$groupid,
				),
				'param'=>array(
						'BigCaseId'	=>10002,
						'CaseId'	=>10003,
						'ParentId'	=>10002,
						'ChildId'	=>113,
						'Desc'		=>'体验账号列表读取'
				)
		);
		$list_res 				= request($param);
		$practice_account_list 	= $list_res['Data'];
		$page 					= $list_res['Page'];
		
		include template('group/practice_account_list.html',$tpl);
		break;
	case 'practice_account_edit':
		$link_array = getLevelLink(10002,10003,10002,101);
		
		$id 		= intval($_GET['id']);
		$param 		= array(
				'extparam'=>array(
						'Tag'		=>'PracticeAccountDetail',
						'Id'		=>$id,
				),
				'param'=>array(
						'BigCaseId'	=>10002,
						'CaseId'	=>10003,
						'ParentId'	=>10002,
						'ChildId'	=>113,
						'Desc'		=>'体验账号列表读取'
				)
		);
		$detail_res = request($param);
		$detail		= $detail_res['Data'];
		
		include template('group/practice_account_edit.html',$tpl);
		break;
	case 'practice_account_edit_submit':
		if(!$_POST['accounts']){
			echo json_encode(array("Flag"=>101, "FlagString"=>"添加账号不能为空"));
			exit;
		}
		if(!$_POST['role_name']){
			echo json_encode(array("Flag"=>101, "FlagString"=>"体验角色不能为空"));
			exit;
		}
		
		$accounts 	= array_map("htmlspecialchars", array_map("addslashes", $_POST['accounts']));
		$room_ids 	= array_map("intval", $_POST['room_ids']);
		$role_name	= htmlspecialchars(addslashes($_POST['role_name']));
		$id			= intval($_POST['id']);
		$groupid 	= intval($_GET['group_id']);
		
		$param 		= array(
				'extparam'=>array(
						'Tag'		=>'SavePracticeAccount',
						'GroupId'	=>$groupid,
						'RoleName'	=>$role_name,
						'Accounts'	=>$accounts,
						'RoomId'	=>$room_ids,
						"Id"		=>$id
				),
				'param'=>array(
						'BigCaseId'	=>10002,
						'CaseId'	=>10003,
						'ParentId'	=>10002,
						'ChildId'	=>114,
						'Desc'		=>'体验账号保存'
				)
		);
		$list_res 				= request($param);
		echo json_encode($list_res);
		exit;
	case 'practice_account_del':
		$id 		= intval($_GET['id']);
		$param 		= array(
				'extparam'=>array(
						'Tag'		=>'DelPracticeAccount',
						'Id'		=>$id,
				),
				'param'=>array(
						'BigCaseId'	=>10002,
						'CaseId'	=>10003,
						'ParentId'	=>10002,
						'ChildId'	=>115,
						'Desc'		=>'体验账号列表删除'
				)
		);
		$del_res = request($param);
		
		ShowMsg($del_res['FlagString'], "?module=practice_account_list&group_id=".$_GET['group_id']);
}
