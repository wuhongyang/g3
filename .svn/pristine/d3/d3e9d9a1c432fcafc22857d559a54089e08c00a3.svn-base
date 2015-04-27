<?php
include_once('library/common.php');

$user = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$group_id),'extparam'=>array('Tag'=>'GetLogin')));
if($user['Flag'] != 100){
	$back_url=base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REDIRECT_URL']);
	alertMsg('请先登录','/passport/?account&login&url='.$back_url);
}

$module = isset($_GET['module']) ? $_GET['module'] : 'vip_display';
$module = 'vip_display';

//会员角色
$vips = array(
	10255 => array('name'=>'基本会员','price'=>1000,'parent_id'=>10029),
	10257 => array('name'=>'黄金会员','price'=>3000,'parent_id'=>10422),
	10258 => array('name'=>'白金会员','price'=>4000,'parent_id'=>10423),
	10259 => array('name'=>'钻石会员','price'=>5000,'parent_id'=>10424),
	10260 => array('name'=>'亲友团'  ,'price'=>750, 'parent_id'=>10425)
);

// function isVipCanBuy($group_id,$uin,$price){
	// $param = array(
		// 'extparam'=>array('Tag'=>'IsVipCanBuy','GroupId'=>$group_id,'Price'=>$price),
		// 'param'=>array('BigCaseId'=>10005,'CaseId'=>10018,'ParentId'=>10137,'ChildId'=>101,'Uin'=>$uin)
	// );
	// $rst = request($param);
	// return ($rst['Flag'] === 100);
// }

switch ($module) {
	case 'vip_display':
		// $param = array(
			// 'extparam'=>array('Tag'=>'GetVipInfo','GroupId'=>$group_id),
			// 'param'=>array('BigCaseId'=>10005,'CaseId'=>10018,'ParentId'=>10137,'ChildId'=>101,'Uin'=>$user['Uin'])
		// );
		// $info = request($param);
		// $info = $info['Info'];
		//是否开通会员
		$template = 'vip_display';
		break;
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].='vvai/tpl/shop/vip/';
$tmp_config['cache_dir'].='tpl/';

$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template($template.'.html',$tpl);