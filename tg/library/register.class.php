<?php
/**
* 邮箱通行证
* @copyright (c) 杭州奥点科技有限公司
*/
class register
{

	/**
	* 激活链接有效时间
	* @parame floot $expire 秒
	*/
	private $expire = 3600;

	function __construct(){
		
	}

	//用户是否存在
	function countmail($mail=''){
		$mail = empty($mail) ? $_GET['countmail'] : $mail;
		$user_array = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$mail,'Status'=>1)));
		if(empty($mail)){
			if($user_array['Flag'] != 100) exit('0');exit('1');
		}else{
			return $user_array;
		}
	}

	//注册页面
	function index(){
		if(!empty($_POST)){
			if(empty($_POST['username'])) alertMsg('邮箱地址不能为空');
			if(empty($_POST['nick'])) alertMsg('昵称不能为空');
			if(strlen($_POST['password']) < 6) alertMsg('密码不能小于6位');
			if(empty($_POST['checkcode']) || strtolower($_POST['checkcode']) != $this->getSession('captcha')) alertMsg('验证码不正确');
			$mail_array = $this->countmail($_POST['username']);
			if($mail_array['Flag'] == 100) alertMsg('邮箱已被注册');
			$desc    = "邮箱注册";
			$childid = 102;
			$_POST['password'] = md5($_POST['password']);
			$param = array(
				'param'=>array("BigCaseId"=>10004,"CaseId"=>10013,"ParentId"=>10128,"ChildId"=>$childid,"Desc"=>$desc,"SessionKey"=>$_POST['password'],"Uin"=>$_POST['username']),
				'extparam'=>array('Tag'=>'RegPassport','User'=>$_POST['username'],'Pass'=>$_POST['password'],'Nick'=>$_POST['nick'],'Gender'=>$_POST['gender'],'Fromname'=>$_POST['fromname'],'Fromuid'=>$_POST['fromuid'],"Ip"=>get_ip(),'Referer'=>$_POST['referer'],'FileName'=>$_POST['FileName'])
			);
			$res = request($param);
			if($res['Flag'] == 100){ //保存默认头像
				$face = array(1=>'../pic/images/uin_man.jpg',2=>'../pic/images/uin_woman.jpg');
				$bytes = file_get_contents($face[$_POST['gender']]);
				if(!empty($bytes)){
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$res['Uin']);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				}
			}
			$param = array(
				'param'=>array("BigCaseId"=>10001,"CaseId"=>10001,"ParentId"=>10001,"ChildId"=>101,"Desc"=>'登陆',"SessionKey"=>$_POST['password'],"Uin"=>$_POST['username']),
				'extparam'=>array('Tag'=>'UserLogin',"Ip"=>get_ip())
			);
			request($param);
			alertMsg('','/v/1398');
		}
	}
	
	function checkcode(){
		if(empty($_POST['checkcode']) || strtolower($_POST['checkcode']) != $this->getSession('captcha')){
			echo '0';
		}else{
			echo '1';
		}
	}

//------------------------------私有方法----------------------------------

	private function sendRegMail(array $data){
		$code = $this->username->url_authencode($data);
		$url  = BASE_URL.'?user_email&rc='.$code;
		$msg  = '<h1 style="font-weight:bold;font-size:16px;">亲爱的'.$data['nick'].', 离成功注册VV酷通行证就差一步了！</h1>';
		$msg .= '<p>请点击 <a href="'.$url.'">确认注册</a> 链接完成注册</p>';
		$msg .= '<p>如果“确认注册”链接无法打开，请复制以下地址到您的浏览器打开</p><p>'.$url.'</p>';
		$msg .= '<p>请尽快确认注册，'.($this->expire/3600).'小时内有效。</p>';
		$msg .= '<p>该邮件由系统发出，请勿回复，感谢您注册VV酷通行证！</p>';
		return sendMail($data['username'],'VV酷通行证-注册确认邮件',$msg);
	}
	
	public function detailList($data){
		$mongodb = domain::main()->GroupDBConn('mongo');
		if(!empty($data['stime']) && !empty($data['etime'])){
            $query_condition['RegisterTime'] = array('$gte'=>intval(strtotime($data['stime'].' 00:00:00')),'$lte'=>intval(strtotime($data['etime'].' 23:59:59')));
        }
		if($data['Fromname']){
			$query_condition['Fromname'] = $data['Fromname'];
		}
		$query_condition['Fromuid'] = array('$gt'=>0);
		$query_condition['RoomLogout'] = array('$gt'=>0);
        $table_name = 'Advertise.details';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('RegisterTime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
        $page_arr = $this->showPage(10,20);
        $list['list'] = $result;
        $list['page'] = $page_arr['page'];
        return array('Flag'=>100,'FlagString'=>'成功','Result'=>$list);
    }
	
	private function getSession($key){
		if(empty($_SESSION)){
			session_start();
		}
		return $_SESSION[$key];
	}
	
	private function showPage($total, $perpage = 10) {
        require_once (dirname(dirname(dirname(__FILE__))).'/data/library/page.class.php');
        $page = new extpage(array (
            'total' => $total,
            'perpage' => $perpage
        ));
        $pageArr['page'] = $page->simple_page($total);
        $pageArr['limit'] = $page->simple_limit();
        unset ($page);
        return $pageArr;
    }
}