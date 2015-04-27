<?php
//初始化
require_once 'common.php';
$redirect = $_GET['redirect'];
$domain = $_GET['domain'];
$dplogin = $_GET['dplogin'];
unset($_GET['redirect'],$_GET['domain'],$_GET['dplogin']);
if($dplogin==1){
	$param = array(
		'extparam' => array('Tag'=>'DpUserLogin','Data'=>array('isQQLogin'=>1,'openid'=>$_GET['openid'],'GroupId'=>$GroupData['groupid'])),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10279,'ChildId'=>101,'Desc'=>'站用户登录','GroupId'=>$GroupData['groupid'])
	);
	$backurl='http://'.$_SERVER['HTTP_HOST'].'/group/';
}
else{
	$param = array(
		'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10090,'ChildId'=>101,'DoingWeight'=>1,'Desc'=>'qq登录','GroupId'=>$GroupData['groupid']),
		'extparam' => array('Tag'=>'OpenidLogin','Data'=>$_GET)
	);
	$backurl=empty($redirect) ? 'http://'.$_SERVER['HTTP_HOST'] : $redirect;
}
$userlogin = request($param);


if($userlogin['Flag'] == 100){
	exit('<script type="text/javascript">
	if("'.$domain.'" != ""){
		document.domain = "'.$domain.'";
	}
	if(window.opener){
		window.opener.document.cookie="USER_LOGIN_TOKEN='.$userlogin['Token'].';path=/;domain=.'.$_SERVER['HTTP_HOST'].'";
		("refresh" in window.opener)? window.opener.refresh() : window.opener.location.href = "'.$backurl.'";
		self.window.close();
	}else{
		window.location.href = "'.$backurl.'";
	}
	</script>');
}else{
	$data = array('openid'=>$_GET['openid'],'nick'=>$_GET['nick'],'picurl'=>$_GET['picurl']);
	$param = array(
		'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10128,'ChildId'=>103,'DoingWeight'=>1,'Desc'=>'qq注册','GroupId'=>$GroupData['groupid']),
		'extparam' => array('Tag'=>'Register','Type'=>4,'Data'=>$data)
	);
	$rst = request($param);
	if($rst['Flag'] == 100){
		$url = !empty($redirect) ? $redirect : 'http://'.$_SERVER['HTTP_HOST'].'/passport/?qq&bind_index&info='.base64_encode(json_encode($data));
		exit('<script type="text/javascript">
		if(window.opener){
			window.opener.location.href="'.$url.'";
			self.window.close();
		}else{
			window.location.href="'.$url.'";
		}
		</script>');
	}else{
		exit('<script type="text/javascript">alert("账号异常");if(window.opener){self.window.close();}</script>');
	}
}