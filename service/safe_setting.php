<?php
session_start();
define('BASE_URL_EMAIL', 'http://'.$_SERVER['HTTP_HOST'].'/service/safe_setting.php');
require_once 'common.php';

$module =  $_GET['module'];

switch($module){
	case 'password':
		if(isset($_POST) && !empty($_POST)){
			//$user = !empty($user['Phone']) ? $user['Phone'] : $user['Email'];
			$param = array(
				'extparam' => array('Tag'=>'ResetPassword','User'=>$user['Login'],'Data'=>$_POST),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10021,'ChildId'=>101)
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				$param = array(
						'param'=>array("BigCaseId"=>10001,"CaseId"=>10001,"ParentId"=>10001,"ChildId"=>101,"Desc"=>"修改密码后登陆","SessionKey"=>md5($_POST['new_password']),"Uin"=>$user),
						'extparam'=>array('Tag'=>'UserLogin','Remember'=>$remember)
				);
				request($param);
				alertMsg('密码修改成功','/service/safe_setting.php?module=password');
// 				alertMsg('密码修改成功，请重新登录','/passport/index.php?account');
			}else{
				alertMsg($rst['FlagString'],-1);
			}
		}else{
			$temp = 'password';
		}
		break;
	case 'phone':
		if(isset($_POST) && !empty($_POST)){
			$mypost = $_POST;
			$mypost['user'] = $user;
			
			//手机验证更新下一步标识
			$uniqueId = !empty($mypost['uniqueid']) ? $mypost['uniqueid'] : $_SESSION['uniqueId'];
			unset($_SESSION['uniqueId']);
			updateNext($uniqueId);
			
			$param = array(
				'extparam'=>array('Tag'=>'BindPhone','mypost'=>$mypost),
				'param'    => array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10089','ChildId'=>'102','Desc'=>'绑定用户手机号码')
			);
			$result = request($param);
			if( $result['Flag'] == '100' ){	
				ShowMsg('手机验证成功','/service/safe_setting.php?module=phone');
			}else{
				ShowMsg($result['FlagString'], -1);
			}
		}else{
			$temp = 'phone';
		}
		break;
	case 'getCode':
		$business = !empty($_POST['business']) ? $_POST['business'] : '手机绑定';

		$mypost = array('phone'	=> $_POST['phone'],'user'=>$user,'module'=>$business);
		
		$param = array(
			'extparam' => array('Tag'=>'SendPhoneCode','mypost'=>$mypost),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10089,'ChildId'=>101,'Desc'=>'发送短信')
		);
		//$result = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'SendCode','Phone'=>$_POST['phone'],'Module'=>$business)));
		$result = request($param);
		if($result['Flag'] == 100){
			$_SESSION['uniqueId'] = $result['UniqueId'];
		}
		exit(json_encode($result));
		break;
	case 'email':
		if( $_POST['email'] ){
			$email = $_POST['email'];
			$mypost = array('user'=>$user,'email' => $email);
			$param = array(
				'extparam' => array('Tag'=>'EmailValida','mypost'=>$mypost),
				'param'    => array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10088','ChildId'=>'101','Desc'=>'发送邮件')
			);
			$result = request($param);
			if($result['Flag'] != 100){
				ShowMsg($result['FlagString'], '?module=email');
			}
			$module = 'email';
			$temp = "email_ok";
		}else{
			$temp = 'email';
		}
		break;
	case 'bindemail' :	//用户点击链接，邮箱验证
		$mypost['bindcode'] = $_GET['bindcode'];
		$mypost['user'] = $user;
		$param = array(
				'extparam'=> array('Tag'=>'BindEmail','mypost'=>$mypost),		
				'param'=>array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10088','ChildId'=>'102','Desc'=>'邮箱绑定')
		);
		$result = request($param);
		
		$email = $result['email'];
		$url ='http://'.$_SERVER['HTTP_HOST']; 
		if( $result['Flag'] == 100 ){
			ShowMsg('邮箱验证成功', 'http://'.$_SERVER['HTTP_HOST'].'/');
		}else{
			ShowMsg($result['FlagString'],-1);
		}
		break;
	case 'idcard':
		if(isset($_POST) && !empty($_POST)){			
			$param = array(
				'extparam'=>array('Tag'=>'UserRenzheng','Uin'=>$user['Uin'],'Data'=>$_POST),
				'param'=>array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10087','ChildId'=>'101','Desc'=>'用户认证')
			);
			$result = request($param);
				
			if( $result['Flag'] == '100' ){
				ShowMsg('身份认证成功', '?module=idcard');
			}else{
				ShowMsg($result['FlagString'], -1);
			}
		}else{
			$info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));
			$info['name'] = $info['Name'];
			$info['idcard'] = $info['IdCard'];
			$temp = 'idcard';
		}	
		break;
	case 'way':
		$way = isset($_GET['w']) ? $_GET['w'] : '1';
		$module = $way==1 ? 'email' : 'phone';
		if($way == 1){ //修改邮箱
			$email_url = '?module=viaEmail&w=1';//emailstep1
			$phone_url = '?module=viaPhone&w=1';
		}else{ //修改手机
			$email_url = '?module=viaEmail&w=2';
			$phone_url = '?module=viaPhone&w=2';
		}
		$temp = 'way';
		break;
	case 'viaEmail':
		$way = isset($_GET['w']) ? intval($_GET['w']) : 1;
		if($way == 1){ //通过邮箱改邮箱
			$module = 'email';
			$mypost = array('user'=>$user,'email'=>$user['Email']);	
			$param = array(
				'extparam' => array('Tag'=> 'EmailChange','mypost' => $mypost),
				'param'=>array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10088','ChildId'=>'103','Desc'=> '修改邮箱绑定')
			);
			
		}else{ //通过邮箱改手机
			if(empty($user['Phone'])){
				alertMsg('还没有绑定邮箱，不能通过邮箱修改',-1);
			}
			$module = 'phone';
			$mypost = array('Email'=>$user['Email'],'Phone'=>$user['Phone']);
			$param  = array(
				'extparam' => array('Tag'=>'EmailChangePhone','mypost'=>$mypost),	
				'param'    => array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10089','ChildId'=>'104','Desc'=>'修改邮箱绑定')
			);
		}
		$result = request($param);
		if($result['Flag'] != 100){
			alertMsg($result['FlagString'],-1);
		}else{
			$email = $user['Email'];
			$temp = 'email_ok';
		}
		break;
	case 'viaPhone':
		$way = isset($_GET['w']) ? intval($_GET['w']) : 1;
		if($way == 1){ //通过手机改邮箱
			header("Location:?module=changeEmail&w=1");
		}else{ //通过手机改手机
			/*$phone = substr_replace($user['Phone'], '****', 3,4);
			$temp = 'change_phone';*/
			header("Location:?module=changePhone&w=2");
		}
		break;
	case 'changePhone':
		$module = 'phone';
		$phone = substr_replace($user['Phone'], '****', 3,4);
		if( !empty($_POST) ){
			$tag = 'ChangePhone';
			$mypost = $_POST;
			$mypost['email'] = $user['Email'];
			
			//手机验证更新下一步标识
			$uniqueId = !empty($mypost['uniqueid']) ? $mypost['uniqueid'] : $_SESSION['uniqueId'];
			unset($_SESSION['uniqueId']);
			updateNext($uniqueId);
			
			$param = array(
				'extparam' => array('Tag'=>'ChangePhone','mypost'=>$mypost),
				'param'    => array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10089','ChildId'=>'103','Desc'=>'手机号码修改')
			);
			$result = request($param);
			
			if( $result['Flag'] == '100' ){
				ShowMsg('验证成功', '?module=phone&bindcode='.$result['code']);
			}
			else{
				ShowMsg($result['FlagString'], -1);
			}	
		}
		$temp = 'change_phone';
		break;
	case 'changeEmail':
		if(empty($user['Phone'])){
			alertMsg('还没有绑定手机，不能通过手机修改',-1);
		}
		$module = 'email';
		$phone = substr_replace($user['Phone'], '****', 3,4);
		if($_POST){
			$phone 	 = $user['Phone'];
			$msgcode = intval($_POST['msgcode']);
			$mypost = array(
					'phone'   => $phone,
					'msgcode' => $msgcode,
					'email'   => $user['Email']
			);
			$tag = 'PhoneChangeEmail';
			$param = array(
					'extparam' => array(
						'Tag'    => $tag,
						'mypost' => $mypost
					),
					'param'    => array(
						'BigCaseId'	=> '10004',
						'CaseId'    	=> '10013',
						'ParentId'  	=> '10089',
						'ChildId'   	=> '104',
						'Uin' 	    	=> $Uin,
						'SessionKey'	=> '',
						'ChannelId' 	=> '0',
						'TargetUin' 	=> '0',
						'Client'    	=> 'WEB ADMIN',
						'DoingWeight'   => '0',
						'MoneyWeight'	=> '0',
						'Desc'		=> '修改邮箱绑定'
					)
			);
			$result = request($param);
			if( $result['Flag'] == '100' )
				ShowMsg('验证成功', '?module=email&bindcode='.$result['code']);
			else
				ShowMsg($result['FlagString'], -1);
		}
		$temp = "change_phone";
		break;
	default: 
		//404错误
		header('Content-type:text/html; charset=utf-8');
		header("HTTP/1.1 404 Not Found");
		require(dirname(dirname(__FILE__)).'/404.html');
		die();
		break;
}
if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template('pass_manager/'.$temp.'.html',$tpl);