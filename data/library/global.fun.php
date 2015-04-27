<?php
define('__ROOT__',dirname(dirname(__FILE__))); //程序根目录
require_once __ROOT__.'/config/define.php';
require_once __ROOT__.'/config/database.php';

/* 自动加载类 */
spl_autoload_register('loadDataLibrary');

/**
* 自动加载类文件(默认只加载base目录文件)
* @param string $name 类名
*/
function loadDataLibrary($name){
	$suffix = array('.php','.class.php');
	foreach($suffix as $ext){
		$path = __ROOT__."/library/{$name}{$ext}";
		if(file_exists($path)){
			include $path;
			break;
		}
	}
}

function getSvnList($svn_path,$svn_username,$svn_password,$path='',$config_dir='/home/daemon/'){//分站模板目录名称
	$command = 'svn list '.$svn_path.$path;
	$authCommand = ' --username '.$svn_username.' --password '.$svn_password.' --no-auth-cache --non-interactive --config-dir '.$config_dir.'.subversion';
	exec($command . $authCommand, $result);
	foreach ((array)$result as $key => $value) {
		$result[$key] = rtrim($value, '/');
	}
	return $result;
}

/**
* 传入配置文件名，返回配置文件内容
* @param string $file 文件名称
* @param array
*/
function config($file,$key = ''){
	static $config_ini;
	if(empty($config_ini[$file])){
		$path = __ROOT__."/config/{$file}.php";
		if(file_exists($path)){
			include $path;
			$config_ini[$file] = $config;
		}else{
			$config_ini[$file] = NULL;
		}
	}
	if(empty($key)){
		return $config_ini[$file];
	}else{
		return $config_ini[$file][$key];
	}
}

//获取根域名
function getRootDomain(){
	$arr = explode('.',$_SERVER['HTTP_HOST']);
	$len = count($arr)-1;
	$short_cn = ($len == 3 && strpos($_SERVER['HTTP_HOST'],'cn'));
	$d_index = $short_cn ? 1 : ceil($len / 2);
	foreach((array)$arr as $key => $val){
		if($key >= $d_index){
			$domain .= '.'.$val;
		}
	}
	return substr($domain,1);
}

//获取用户名类型
function getUserType($user){
	if(preg_match('/^\w+@(\w+([._-][a-zA-Z]+))+$/',$user)) return 'email';
	if(preg_match('/^(13|15|18)\d{9}$/',$user)) return 'phone';
	return false;
}

/**
* 发送短信
* @param string $to 接收者
* @param string $content 发送内容
* @return boolen
*/
function sendSMS($to,$content,$module,$time='',$mid=''){
	$data = array('authkey'=>'G3AuThKeY2345','to'=>$to,'content'=>$content,'module'=>$module,'ip'=>get_ip());
	$url = 'http://sms.vvku.com/sms/index.php';//短信中心地址
	$result = json_decode(socket_request($url,$data),true);
	return $result;
}

function updateNext($uniqueId){
	$data = array('uniqueId'=>$uniqueId);
	$url = 'http://sms.vvku.com/sms/update_next.php';//短信中心地址
	socket_request($url,$data);
}

/**
* 发送邮件
* @param string $to 接收者
* @param string $subject 主题
* @param string $message 发送内容
* @return boolen
*/
function sendMail($to,$subject,$message,$config=''){
	if(empty($config)){
		$config = config('email','register');
	}
	if($config['sendType'] == 'mail'){
		$subject  = "=?{$config['charset']}?B?".base64_encode($subject)."?=";
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset={$config['charset']}\r\n";
		$headers .= "From: {$config['fromname']} <{$config['from']}>\r\n";
		foreach((array)$to as $t){
			if( ! mail($t, $subject, $message, $headers)) return false;
		}
		return true;
	}else{
		//static $mail;
        //if( ! is_object($mail)){
        //    include dirname(__FILE__).'/mailer.php';
        //    $mail = new mailer();
        //}
        $mail = new mailer();
        $mail->SMTPDebug= $config['debug'];
        $mail->IsSMTP();
        $mail->SMTPAuth = false;
        $mail->Port     = $config['port'];
        $mail->CharSet  = $config['charset'];
        $mail->Host     = $config['host']; //服务器
        $mail->Username = $config['username']; //用户名
        $mail->Password = $config['password']; //密码
        $mail->SetFrom($config['from'],$config['fromname']); //发送者
        $mail->IsHTML($config['is_html']);
        $mail->Subject = $subject;
        $mail->MsgHTML($message);
        foreach((array)$to as $t){
            $mail->AddAddress($t);
        }
        return $mail->Send();
    }
}

//discuz加密函数
function uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
* 请求接口数据（测试）
* @param string $query api接口地址
* @param array $parameter 接口参数格式：array(param=>四级科目,extparam=>接口参数)
* @return array
*/
/*
function loadModel($query, $parameter, $to_array = true) {
	if(empty($query)) exit("请求地址错误");
	$url = parse_url($query);
	$file = __ROOT__.'/'.trim($url['path'],'/');
	if( ! file_exists($file)) exit("接口不存在 '{$url['path']}'");
	require_once $file;
	$class = explode('.',basename($url['path']));
	$class = $class[0];
	$func = $parameter['extparam']['Tag'];
	$param = $parameter['extparam'];
	unset($param['Tag']);
	$obj = new $class();
	$obj->parameter = $parameter;
	return call_user_func_array(array($obj,$func),$param);
}
$data = loadModel('core/regions/library/regions.php',array('extparam'=>array('Tag'=>'getRoomsCase','regionid'=>330100)));
print_r($data);
exit;
*/


/**
* 请求接口数据
* @param string $query api接口地址
* @param array $parameter 接口参数格式：array(param=>四级科目,extparam=>接口参数)
* @return array
*/
function httpPOST($query, $parameter, $to_array = true) {
	if(empty($query)) exit("请求地址错误");
	$url = parse_url($query);
	$file = __ROOT__.'/'.trim($url['path'],'/');
	if( ! file_exists($file)) exit("接口不存在 '{$url['path']}'");
	$_POST = array_merge($_POST,$parameter);
	ob_start();
	require $file;
	unset($_POST['extparam'],$_POST['param'],$_POST['parameter'],$_POST['BusinessConfig']);
	return $to_array? json_decode(ob_get_clean(),true) : ob_get_clean();
}

/**
* socket模拟POST和GET请求
* @param string url 请求地址
* @param array $data 请求参数
* @return string
*/
function socket_request($query_url, $data = '', $is_url = true, $timeout = 10) {
	$url = @parse_url($query_url);
	if(empty($url)) {
		return 'Request URL can not be empty!';
	}
	$url['path'] = empty($url['path'])? '/' : $url['path'];
	$url['port'] = empty($url['port'])? 80  : $url['port'];
	$scheme = $url['scheme'];
	$host = $is_url == true ? $url['host'] : gethostbyname($url['host']);
	$port = $url['port'];
	$path = $url['path'] . (isset($url['query'])? '?' . $url['query'] : '') . (isset($url['fragment'])? '#' . $url['fragment'] : '');
	
	if(!empty($data) || is_array($data)) {
		$content = is_array($data) ? http_build_query($data,'','&') : $data;
		$method = 'POST';
	}
	$query_url = $scheme.'://'.$host.$path;
	
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$query_url);
	curl_setopt($ch,CURLOPT_PORT,$port);
	curl_setopt($ch,CURLOPT_HEADER,false);
	curl_setopt($ch,CURLOPT_COOKIE,$_SERVER['HTTP_COOKIE']);
	curl_setopt($ch,CURLOPT_AUTOREFERER,true);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
	curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);
	if($method == 'POST') {
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$content);
	}
	//execute post
	$response = curl_exec($ch);
	//get response code
	$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	//close connection 
	curl_close($ch);
	//return result
	if($response_code == '200') {
		return $response;
	} else {
		return false;
	}
}

/**
* 获取kmoney余额
* @param string $uin 用户ID
* @return string
*/
function get_money($uin,$group_id=0){
	if($uin > 0){
		$param = array('extparam'=>array('Tag'=>'GetKmoneyBalance','Uin'=>$uin,'GroupId'=>$group_id));
		$money = httpPOST(KMONEY_API_PATH,$param);
		return intval($money['LastBalance']);
	}
}

/**
* 获取kwealth余额
* @param string $uin 用户ID
* @return string
*/
function get_wealth($uin){
	if($uin > 0){
		$param = array('extparam'=>array('Tag'=>'GetKwealthBalance','Uin'=>$uin));
		// $money = httpPOST(KWEALTH_API_PATH,$param);
		return $money['LastBalance']?$money['LastBalance']:0;
	}
}

/**
 * 获取三级科目kmoney余额
 * @param string $uin 用户ID
 * @return string
 */
function get_parent_money($bigcaseId, $caseId, $parentId,$group_id=0){
	$param = array('extparam'=>array('Tag'=>'GetBusinessBalance','GroupId'=>$group_id),'param'=>array('BigCaseId'=>$bigcaseId,'CaseId'=>$caseId,'ParentId'=>$parentId));
	$money = httpPOST(KMONEY_API_PATH,$param);	
	return $money['LastBalance']?$money['LastBalance']:0;
}

/**
 * 获取三级科目kwealth余额
 * @param string $uin 用户ID
 * @return string
 */
function get_parent_wealth($bigcaseId, $caseId, $parentId){
	$param = array('extparam'=>array('Tag'=>'GetBusinessBalance'),'param'=>array('BigCaseId'=>$bigcaseId,'CaseId'=>$caseId,'ParentId'=>$parentId));
	// $money = httpPOST(KWEALTH_API_PATH,$param);
	return $money['LastBalance']?$money['LastBalance']:0;
}

/**
* 获得客户端ip
* @return string
*/
function get_ip() {
	if($_SERVER["HTTP_X_REAL_IP"] && strcasecmp($_SERVER["HTTP_X_REAL_IP"], 'unknown')){
		$realip = $_SERVER["HTTP_X_REAL_IP"];
	}
	elseif($_SERVER["HTTP_X_FORWARDED_FOR"] && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], 'unknown')){
		$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif($_SERVER["HTTP_CLIENT_IP"] && strcasecmp($_SERVER["HTTP_CLIENT_IP"], 'unknown')){
		$realip = $_SERVER["HTTP_CLIENT_IP"];
	}else{
		$realip = $_SERVER["REMOTE_ADDR"];
	}
	return $realip;
}

/**
* 得到用户渠道类型
* @param integer $uin 用户ID
* @param integer $roomid 房间ID
* @return integer
*/
function getChannelType($uin,$roomid=0,$type=15){
	$uin = intval($uin);
	if($uin <= 0) return;
	$mysql = domain::main()->GroupDBConn();
	if($roomid > 0 && $type!=15) {
		$where = ' AND room_id = '.$roomid;
	}
	$sql = "SELECT `uid` FROM ".DB_NAME_PARTNER.".channel_user WHERE uid={$uin} {$where} AND `type`={$type} AND `flag`=1";
	return intval($mysql->get_var($sql));
}

function getChannelInfo($uin,$roomid=0,$type=15){
	$uin = intval($uin);
	if($uin <= 0) return;
	$mysql = domain::main()->GroupDBConn();
	if($roomid > 0) {
		$where = ' AND room_id = '.$roomid;
	}
	$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE uid={$uin} {$where} AND `type`={$type} AND `flag`=1";
	$result = $mysql->get_row($sql);
	if(!empty($result)){
		$result['Flag'] = 100;
	}
	return $result;
}

function getChannelRoleInfo($type,$roomid=0,$regionid=0){
	$type = intval($type);
	if ($type <= 0) {
		return;
	}
	$where = '';
	if($roomid > 0){
		$where .= " AND room_id={$roomid}";
	}
	if($regionid > 0){
		$where .= " AND region_id={$regionid}";
	}
	$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE `type`={$type} {$where} AND `flag`=1";
	$mysql = domain::main()->GroupDBConn();
	$result = $mysql->get_results($sql,ASSOC);
	return $result;
}

/**
* 取得用户的渠道身份信息
* @param integer $uin 用户ID
* @param integer $type 只获取的用户身份ID信息
* @return array 注意：即使传了$type返回也是二维数组，只需取$result[0]即可
*/
function getChannelUserInfo($uin,$type=0){
	$where = '';
	if($type > 0){
		$where .= " AND `type`={$type}";
	}
	$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_user WHERE uid={$uin} {$where} AND `flag`=1 ORDER BY `type` ASC";
	$mysql = domain::main()->GroupDBConn();
	return $mysql->get_results($sql,'ASSOC');
}

/**
* 得到艺人是否在线
* @param integer $uin 用户ID
* @return array
*/
function getArtistInfo($uin){
	$uin = intval($uin);
	if($uin <= 0) return;
	$mysql = domain::main()->GroupDBConn();
	$sql = "SELECT `room_id`,`is_online`,`up_uid` FROM ".DB_NAME_PARTNER.".channel_user WHERE uid={$uin} AND `flag`=1";
	$info = $mysql->get_row($sql,'ASSOC');
	return $info;
}

function getChannelRelation($roomid){
	if($roomid <= 0) return ;
	$mysql = domain::main()->GroupDBConn();
	$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_relation WHERE ChannelId={$roomid}";
	$info = $mysql->get_row($sql,'ASSOC');
	return $info;
}

function getGroupVip($uin,$groupid){
	if($uin <1 || $groupid <1 )	return ;
	$mysql = domain::main()->GroupDBConn('mysql', $groupid);
	$sql = "SELECT uin,group_id,nick FROM ".DB_NAME_IM.".basic_tbl WHERE uin ={$uin} AND group_id ={$groupid} LIMIT 1";
	$row = $mysql->get_row($sql,"ASSOC");
	return $row;
}

function getGroupChannelUser($groupid,$type=15,$uin=0){
	if($groupid < 0) return;
	$where = '';
	if($uin > 0){
		$where = " AND uid={$uin}";
	}
	$mysql = domain::main()->GroupDBConn();
	$sql = "SELECT uid FROM ".DB_NAME_PARTNER.".channel_user WHERE up_uid={$groupid} AND `type`={$type} AND `flag`=1 {$where}";
	return $mysql->get_results($sql,ASSOC);
}

function getDpUserPermission($uin){
	$param = array(
		'extparam' => array('Tag'=>'OwnPermissions','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10281,'ChildId'=>101,'Desc'=>'站内会员权限')
	);
	$permisssions = request($param);
	if($permisssions['Result']['groupId'] < 1){
		alertMsg('无权访问','/');
	}
	return $permisssions['Result'];
}
//验证站管理权限
function checkGroupPermission($parentId,$permission){
	if($permission['isDz']==1){
		return true;
	}
	if(!in_array($parentId,$permission)){
		return false;
	}
	return true;
}

/*
*生成记录日志数据
*@param array 必须参数
*@extparam array 扩展参数
*@return array
*/
function getLogData($param,$extparam){
	$log_array = array(
		'param'=>array(
			'Uin'=>(int)$param['Uin'],'TargetUin'=>(int)$param['TargetUin'],'ChannelId'=>(int)$param['ChannelId'],'BigCaseId'=>(int)$param['BigCaseId'],'CaseId'=>(int)$param['CaseId'],'ParentId'=>(int)$param['ParentId'],'ChildId'=>(int)$param['ChildId'],'MoneyWeight'=>(int)$param['MoneyWeight'],'DoingWeight'=>(int)$param['DoingWeight'],'TaxType'=>(int)$param['TaxType'],'Desc'=>$param['Desc'],'GroupId'=>(int)$param['GroupId']
		),
		'extparam'=>$extparam
	);
	return $log_array;
}

/*
*根据站所使用业务规则生成记录日志数据
*@param array 必须参数
*@extparam array 扩展参数
*@return array
*/
function getExtLogData($param,$extparam){
	$param['GroupId'] = (int)$param['GroupId'];
	if($param['GroupId'] > 0){
		$business_array = domain::main()->GroupKeyVal($param['GroupId'],'business_array');
	}
	$roomid = $param['ChannelId'] > 0 ? $param['ChannelId'] : 1;
	$log_array = array();
	foreach((array)$business_array as $key=>$value){
		$uin = $value[2]==1 ? $param['TargetUin'] : $param['Uin'];
		if($value[1] > 1 && $uin >0){//需要通过角色来确定id
			$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$uin,'GroupId'=>$param['GroupId'],'ChannelId'=>$roomid,'RoleId'=>$value[1])));
			if($roles_info['Flag'] == 100){
				$log_array['param'][$key] = (int)$uin;
			}
		}
	}
	return $log_array;
}

function cdn_url($url,$clear = ''){
	$storage = cache::connect(config('cache','memcache'));
	if(is_array($url)){
		foreach($url as $key=>$value){
		  $new_url[] = get_cdn_url($value,$storage,$clear);
		}
	}else{
		$new_url = get_cdn_url($url,$storage,$clear);
	}
	return $new_url;
}

function get_cdn_url($url,$storage,$clear = ''){
	$url_arr = parse_url($url);//只对完整url有效http://*
	$new_url = $storage->long_get($url_arr['path'].$url_arr['query']);//永久有效
	if((empty($new_url) && !empty($url_arr['scheme']) && !empty($url_arr['host']) && !empty($url_arr['path'])) || $clear == 'CDN_UPLOAD_CLEAR'){//当后台上传时才或者memcache里不存在时计算
		$path_arr = explode('/',$url_arr['path']);
		$file_ext = pathinfo($url_arr['path'],PATHINFO_EXTENSION);//css,js文件采用?参数方式
		$url_md5 = $url;
		if($_SERVER['HTTP_HOST'] != $url_arr['host']){
			if($_SERVER["HOSTNAME"] == 'test'){
				$osshost = 'oss.aliyuncs.com';
			}else{
				$osshost = 'oss-internal.aliyuncs.com';
			}
			if(in_array($file_ext,array('js','css','swf'))){
				$url_md5 = $url_arr['scheme'].'://vvku-'.current(explode('.',$url_arr['host'])).'.'.$osshost.$url_arr['path'];
			}else{
				$url_md5 = $url_arr['scheme'].'://'.$path_arr['2'].'.'.$osshost.'/'.$path_arr['3'].'/'.$path_arr['5'].'x'.str_replace('.'.$file_ext, '', $path_arr['6']).'/'.$path_arr['4'];
			}
		}
		$cdn_md5 = url_short(md5_file($url_md5));
		if(in_array($file_ext,array('js','css','swf'))){
			if(empty($url_arr['query'])){
				$new_url = $url_arr['path'].'?'.$cdn_md5;
			}else{
				$new_url = $url_arr['path'].'?'.$cdn_md5.'&'.$url_arr['query'];
			}
		}else{
			$path_len = count($path_arr);
			$path_arr[$path_len - 3] .= '_'.$cdn_md5;
			$new_url = implode('/',$path_arr);
		}
		$storage->set($url_arr['path'].$url_arr['query'],$new_url);
	}else{
		if(filter_var($new_url,FILTER_VALIDATE_URL)){
			$tmp_url = parse_url($new_url);
			if(empty($tmp_url['query'])){
				$new_url = $tmp_url['path'];
			}else{
				$new_url = $tmp_url['path'].'?'.$tmp_url['query'];
			}
		}
	}
	return $url_arr['scheme'].'://'.$url_arr['host'].$new_url;
}

function url_short($input){
	$base32 = array (
	'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
	'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
	'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
	'y', 'z', '0', '1', '2', '3', '4', '5'
	);

	$hex = md5($input);
	$hexLen = strlen($hex);
	$subHex = substr ($hex, 0 * 8, 8);
	$int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
	$out = '';

	for ($j = 0; $j < 6; $j++) {
		$val = 0x0000001F & $int;
		$out .= $base32[$val];
		$int = $int >> 5;
	}
	return $out;
}

/*
自定义session
$session = new SessionSaveHandle();
ini_set('session.save_handler', 'user');
session_set_save_handler(array($session, "open"), array($session, "close"), 
array($session, "read"), array($session, "write"), array($session, "destroy"), array($session, "gc"));
*/