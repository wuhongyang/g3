<?php
require_once '../library/global.fun.php';
$module = !empty($_GET['module']) ? $_GET['module'] : 'list';
$link_array = getLevellink(10002,10040,10231,101);

if($module == 'list'){
	//请求列表
	$param = array(
        'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10231,'ChildId'=>101),
        'extparam'=>array('Tag'=>'GetLinkList'),
    );
    $lists = request($param);
    $page = $lists['Page'];
	$lists = (array)$lists['Data'];
	
	foreach ($lists as $key => $value) {
		$picInfo = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$value['img_pic_id']))));
		if($picInfo['lists'][0]['img_path'] > 0){
			$lists[$key]['logo'] =  cdn_url(PIC_API_PATH.'/p/'.$picInfo['lists'][0]['img_path'].'/94/37.jpg');
		}
	}
	$tpl = 'list.html';
}elseif ($module == 'add') {
	if(isset($_POST) && !empty($_POST)){
		$data = $_POST;
		//请求添加
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10231,'ChildId'=>103),
	        'extparam'=>array('Tag'=>'SaveLink','Data'=>$data),
	    );
	    $rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg($rst['FlagString'],'?module=list');
		}else{
			alertMsg($rst['FlagString']);
		}
	}
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode((array)$cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode((array)$pic['lists']);
	$tpl = 'info.html';
}elseif ($module == 'update') {
	if(isset($_POST) && !empty($_POST)){
		$info = $_POST;
		//请求修改
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10231,'ChildId'=>103),
	        'extparam'=>array('Tag'=>'SaveLink','Data'=>$info),
	    );
	    $rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg($rst['FlagString'],'?module=list');
		}else{
			alertMsg($rst['FlagString']);
		}
	}
	$id = intval($_GET['id']);
	$param = array(
        'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10231,'ChildId'=>101),
        'extparam'=>array('Tag'=>'GetLinkList','Data'=>array('Id'=>$id)),
    );
    $rst = request($param);
    $info = $rst['Data'];
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode((array)$cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode((array)$pic['lists']);
	$tpl = 'info.html';
	
}elseif($module == 'delete'){
	$id = intval($_GET['id']);
	//请求删除
	$param = array(
        'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10231,'ChildId'=>101),
        'extparam'=>array('Tag'=>'DelLink','Data'=>array('Id'=>$id)),
    );
    $rst = request($param);
	if($rst['Flag'] == 100){
		alertMsg($rst['FlagString'],'?module=list');
	}else{
		alertMsg($rst['FlagString']);
	}

}


$template = template::getInstance();
$template->setOptions(get_config('template','admin'));
include template("friend_link/".$tpl,$template);
