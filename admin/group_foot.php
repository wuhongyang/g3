<?php
require_once '../library/global.fun.php';
$module = empty($_GET['module']) ? 'list' : trim($_GET['module']);
$link_array = getLevellink(10002,10003,10339,101);
$svn_password = 'websync';
$svn_username = 'websync';
$svn_path = 'svn://svn.vvku.com/htdocs/G3_WEB';

switch ($module) {
	case 'list':
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>101),
			'extparam'=>array("Tag"=>"List", "GroupId"=>intval($_GET['group_id']))
		);
		$list = request($param);
		$p = $list['Page'];
		$list = $list['List'];
		$template = 'group_foot_list.html';
		break;
	case 'info':
		$id = intval($_GET['id']);
		$versions = array();
		if($id > 0){
			$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>101),
				'extparam'=>array("Tag"=>"Info",'Id'=>$id)
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
		}
		$group_sites = getSvnList($svn_path,$svn_username,$svn_password,'/branch_themes/');
		
		$ktv_list = getSvnList('svn://svn.vvku.com/g3/kkyoo_ktv/client',$svn_username,$svn_password);
		// if($info['ktv_template']){
			// $ktv_versions = getSvnList('svn://svn.vvku.com/g3/kkyoo_ktv/client/',$svn_username,$svn_password,$info['ktv_template'].'/');
		// }
		$ktv_templates = array();
		if($info['ktv_template']){
			$ktv_templates = json_decode($info['ktv_template'], true);
		}
		if(!$group_sites){
			$group_sites = array();
		}
		array_push($group_sites, 'default');
		$template = 'group_foot_info.html';
		break;
	case 'save':
		$_POST['key'] = $_POST['key']?$_POST['key']:array();
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
		$post = $_POST;
		$ktv_templates = array();
		if($post['ktv_templates']){
			$post['ktv_template'] = json_encode($post['ktv_templates']);
		}
		$id = intval($post['id']);
		if($id > 0){ //编辑
			$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>102),
				'extparam'=>array("Tag"=>"Update",'Id'=>$id,'Data'=>$post)
			);	
		}else{//添加
			$param = array(
				'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>102),
				'extparam'=>array("Tag"=>"Insert",'Data'=>$post)
			);
		}
		$result = request($param);
		if($result['Flag'] == 100){
			alertMsg($result['FlagString'],$link_array['101']['url']);
		}else{
			alertMsg($result['FlagString'],-1);
		}
		break;
	case 'get_version':
		$svn_path = $_GET['type']==1? $svn_path.'/branch_themes/':'svn://svn.vvku.com/g3/kkyoo_ktv/client/';
		$versions = getSvnList($svn_path,$svn_username,$svn_password,$_GET['template'].'/');
		exit(json_encode((array)$versions));
		break;
	case 'sync':
		if($_POST['type']==1){
			$fromPath = $svn_path.'/branch_themes/'.$_POST['template'].'/'.$_POST['version'].'/';
			$topath = 'themes/g3/group_site/'.$_POST['template'];
		}elseif($_POST['type']==2){
			$fromPath = 'svn://svn.vvku.com/g3/kkyoo_ktv/client/'.$_POST['template'].'/'.$_POST['version'].'/';
			$topath = 'themes/g3/ktv/'.$_POST['template'];
		}
		
		if(empty($_POST['template']) || $_POST['template']==-1 || empty($_POST['version']) || $_POST['version']==-1){
			exit;
		}
		// exec('rm -rf '.$topath.'*');
		// $command = 'svn export '.$fromPath.' '.$topath.' --username '.$svn_username.' --password '.$svn_password.' --no-auth-cache --non-interactive --config-dir /home/daemon/.subversion --force';
		// exec($command, $output,$rst);
		$UPLOAD_API_PATH = 'http://upload.vvku.com/folder/'.$_SERVER['HOSTNAME'].'/folder';
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'FROM_PAHT'=>$fromPath,'TO_PATH' =>$topath,'TYPE'=>$_POST['type']);
		// print_r($opt);exit;
		$rst = socket_request($UPLOAD_API_PATH,$opt,true,600);
		echo $rst;
		exit;
		break;
	case 'sync_info':
		$id = intval($_GET['id']);
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>103),
			'extparam'=>array("Tag"=>"SyncInfo",'Id'=>$id)
		);
		$result = request($param);
		alertMsg($result['FlagString'],$link_array['101']['url']);
		break;	
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('group/'.$template,$tpl);
?>