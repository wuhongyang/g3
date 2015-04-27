<?php
define('__BASE__',dirname(dirname(__FILE__)));
$GLOBALS['__PAGE_EXEC_TIME__'] = microtime(true);//记录全局时间
require_once __BASE__.'/data/library/global.fun.php';
require_once __BASE__.'/library/pagecache.class.php';

/* 自动加载类 */
spl_autoload_register('loadWebLibrary');

/**
* 自动加载类文件(默认只加载base目录文件)
* @param string $name 类名
*/
function loadWebLibrary($name){
	$suffix = array('.php','.class.php');
	foreach($suffix as $ext){
		$path = __BASE__."/library/{$name}{$ext}";
		if(file_exists($path)){
			include $path;
			break;
		}
	}
}

/*注释无效代码
//清除Varnish缓存服务器缓存
function cleanVarnishCache($urls,$back){
    alertMsg('操作成功',$back);
	$urls = (array)$urls;
	//$hosts = (array)get_config('cache_host');
	//foreach($hosts as $host){
		foreach($urls as $u){
			exec("/usr/local/varnish/bin/varnishadm -T {$_SERVER['REMOTE_ADDR']}:3500 ban.url {$u}");
		}
	//}
	require dirname(__FILE__).'/clean_client_cache.html';
	exit;
}*/

/**
* 传入配置文件名，返回配置文件内容
* @param string $file 文件名称
* @param array
*/
function get_config(){
	$keys = func_get_args();
	static $config_ini;
	$file = $keys[0];
	unset($keys[0]);
	if(empty($config_ini[$file])){
		$path = __BASE__."/config/{$file}.php";
		if(file_exists($path)){
			include $path;
			$config_ini[$file] = $config;
		}else{
			$config_ini[$file] = NULL;
		}
	}
	if(empty($keys)){
		return $config_ini[$file];
	}else{
		$conf = $config_ini[$file];
		foreach($keys as $k){
			$conf = $conf[$k];
		}
		return $conf;
	}
}

/**
* 请求接口数据
* @param string $query api接口地址
* @param array $parameter 接口参数格式：array(param=>四级科目,extparam=>接口参数)
* @return array
*/
function request($parameter,$http=FALSE) {
	$query = '/data/index.php';
	$parameter = json_encode($parameter);
	$_POST['parameter'] = $parameter;
	if(empty($http)){
		ob_start();
		require __BASE__.$query;
		$data = json_decode(ob_get_clean(),true);
	}else{
		$data = json_decode(socket_request('http://'.$_SERVER['HTTP_HOST'].$query,$_POST),true);
	}
	unset($_POST['parameter']);
	return $data;
}

/**
 * 输出js对话框提示信息,跳转页面
 *
 * @param string $msg
 * @param string $url
 * @param string $target
 *
 * @example 输出javascript标签内容
 */
function ShowMsg($msg = "", $url = "", $target = "") {
	$script = "<html>\r\n";
	$script .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
	$script .= "<script type=\"text/javascript\">\r\n";
	$script .= "if('" . $msg . "' != ''){\r\n";
	$script .= "alert(\"" . $msg . "\");\r\n";
	$script .= "}\r\n";
	$script .= "if('" . $url . "' < 0){\r\n";
	$script .= "window.history.go('". $url ."');\r\n";
	$script .= "}else{\r\n";
	$script .= "if('" . $target . "' == 'parent'){\r\n";
	$script .= "window.top.location.href='" . $url . "';\r\n";
	$script .= "}else{\r\n";
	$script .= "location.href='" . $url . "';\r\n";
	$script .= "}\r\n";
	$script .= "}\r\n";
	$script .= "</script>\r\n";
	$script .= "</html>";
	exit ($script);
}

function alertMsg($msg,$url=-1){
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest'){
		exit('{"msg":"'.$msg.'","url":"'.$url.'"}');
	}else{
		$url = is_numeric($url)? "window.history.go({$url});" : 'window.location.href="'.$url.'";';
		exit('<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script language="javascript">if("'.$msg.'"!=""){alert("'.$msg.'");}'.$url.'</script></html>');
	}
}

/**
* 获取页面权限id ,url
* @param int $bigcaseid
* @param int $caseid
* @param int $parentid
* @param int $childid
*
*/
function getLevelLink($bigcaseid, $caseid, $parentid, $childid){
	if($bigcaseid < 10001 || $caseid < 10001 || $parentid < 10001 || $childid < 101){
		return array('Flag'=>101,'FlagString'=>'参数错误');
	}
	$param = array(
		'extparam' => array('Tag'=>'GetLevelLink'),
		'param' => array('BigCaseId'=>$bigcaseid,'CaseId'=>$caseid,'ParentId'=>$parentid,'ChildId'=>$childid)
	);
	$link_array = request($param);
	
	if($link_array['Flag']==10010){
		$link_array = $link_array['Result'];
		foreach($link_array as $link_key=>$link_value){
			$link_array[$link_value['child_id']]['url'] = get_config('menu_url',$parentid,'list_url',$link_value['child_id']);
		}
		if(empty($link_array[$childid]['url'])){
			$link_array[$childid]['url'] = '#';
		}
	}
	return $link_array;
}

/**
 * 获取星座名称
 * @param int $month
 * @param int $day
 *
 */
function xingzuo($month, $day){
	// 检查参数有效性
	if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
	// 星座名称以及开始日期
	$signs = array(
		array( "20" => "水瓶座"),
		array( "19" => "双鱼座"),
		array( "21" => "白羊座"),
		array( "20" => "金牛座"),
		array( "21" => "双子座"),
		array( "22" => "巨蟹座"),
		array( "23" => "狮子座"),
		array( "23" => "处女座"),
		array( "23" => "天秤座"),
		array( "24" => "天蝎座"),
		array( "22" => "射手座"),
		array( "22" => "摩羯座")
	);
	list($sign_start, $sign_name) = each($signs[(int)$month-1]);
	if ($day < $sign_start){
		list($sign_start, $sign_name) = each($signs[($month -2 < 0) ? $month = 11 : $month -= 2]);
	}
	return $sign_name;
}

/**
 * 验证用户登陆
 *
 */
function checkLogin($go=''){
	if(empty($go)) $go = $_SERVER['REQUEST_URI'];
	$login_info = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>intval($_GET['GroupId']),'SessionKey'=>$_GET['SessionKey']),'extparam'=>array('Tag'=>'GetLogin','Reset'=>1)));
	if( $login_info['Flag'] != '100' ){
		alertMsg("{$login_info['FlagString']}", 'http://'.$_SERVER['HTTP_HOST'].'/passport/?account&index&url='.base64_encode($go));
	}
	return $login_info;
}

function checkDpLogin($showMsg=true){
	if(empty($_COOKIE['DP_LOGIN_TOKEN'])){
		$login_info = array('Flag'=>101,'FlagString'=>'未登录');
	}else{
		$login_info = httpPOST(SSO_API_PATH,array('param'=>array('SessionKey'=>$_COOKIE['DP_LOGIN_TOKEN']),'extparam'=>array('Tag'=>'GetLogin','Reset'=>0)));
		// print_r($login_info);exit;
	}
	if($showMsg){
		if($login_info['Flag'] != 100){
			exit('<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script language="javascript">alert("您还未登录");top.location.href="http://'.$_SERVER['HTTP_HOST'].'/group/index.php";</script></html>');
		}
	}
	return $login_info;
}

function br2nl($string) {
	if(!empty($string)) {
		$string = str_replace("<br />","",$string);
		$string = str_replace("<br>","",$string);
		$string = str_replace("<br />","\n",$string);
		$string = str_replace("<br>","\n",$string);
	}
	return $string;
}
