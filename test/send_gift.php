<?php
ignore_user_abort(true);
set_time_limit(0);
header("Content-type: text/html; charset=utf-8"); 
require_once '../library/global.fun.php';
$group_id = (int)$GroupData['groupid'];

$mysql = db::connect(config('database','default'));

$password = md5('123456');

function showErrorLog($log){
	if(is_array($log)){
		foreach ($log as $val) {
			echo "<p style='color:red;'>{$val}</p>";
		}
	}else{
		echo "<p style='color:red;'>{$log}</p>";
	}
}

switch ($_GET['module']) {
	default:
	case 'openUser':
		if(isset($_POST) && !empty($_POST)){
			$userCount = intval($_POST['user_count']);
			if($userCount < 1){
				alertMsg('用户数必须是大于0的整数');
			}
			$srange = intval($_POST['srange']);
			$erange = intval($_POST['erange']);
			if($userCount - 1 != $erange - $srange){
				alertMsg('范围差值不等于用户数，请重新设置');
			}

			$error = array();
			$parameter = array("extparam"=>array("Tag"=>"RegPassport","Pass"=>$password,"Platform"=>2));
			$successCount = 0;
			for($i=$srange;$i<=$erange;$i++){
				$parameter['extparam']['User'] = $_POST['prefix'].$i;
				$res = httpPOST("core/sso/sso_api.php", $parameter);
				if($res['Flag'] != 100){
					array_push($error, "进行到".$_POST['prefix'].$i."被终止,错误原因:".$res['FlagString']);
				}
			}
			echo '共开设用户：'.$userCount.'个，失败'.count($errors).'个';
			if(!empty($errors)){
				showErrorLog($errors);
			}
		}else{
			echo <<<HTML
			<form method="post">
				用户数：<input type="text" name="user_count"><br>
				用户前缀：<input type="text" name="prefix"><br>
				范围：<input type="text" name="srange"> -- <input type="text" name="erange"><br>
				<input type="submit" name="modify" value="开设用户">
			</form>
HTML;
		}
		break;

	case 'recharge':
		if(isset($_POST) && !empty($_POST)){
			$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$group_id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101)
			);
			$groupinfo=request($param);
			$Ginfo=$groupinfo['Result'];

			$post = $_POST;
			$post['prefix'] = htmlspecialchars(addslashes($post['prefix']));
			$range1 = intval($post['srange']);
			$range2 = intval($post['erange']);
			$post['weight'] = intval($post['weight']);
			if($range1 > $range2){
				ShowMsg("填写范围不正确", -1);
			}
			if(!$post['prefix']){
				ShowMsg("前缀不能为空", -1);
			}
			if($post['weight'] < 1){
				ShowMsg("金额必须为大于0的整数", -1);
			}
			
			$errors = 0;
			for($i=$range1;$i<=$range2;$i++){
				$parameter = array(
						"extparam"=>array(
								"Tag"=>"GetUser",
								"UserName"=>$post['prefix'].$i
						)
				);
				$res = httpPOST("core/sso/sso_api.php", $parameter);
				if($res['Flag'] != 100){
					showErrorLog("进行到".$post['prefix'].$i."被终止,错误原因:".$res['FlagString']);
					++$errors;
				}else{
					$param=array(
						'extparam'=>array('Tag'=>'VipRecharge','Uin'=>$Ginfo['uin'],'TargetUin'=>$res['Uid'],'GroupId'=>$group_id,'Weight'=>$post['weight']),
						'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>103,'GroupId'=>$group_id)
					);
					$result = request($param);
					if($result['Flag'] != 100){
						showErrorLog("进行到".$post['prefix'].$i."被终止,错误原因:".$res['FlagString']);
						++$errors;
					}
				}	
			}
			$count = $range2 - $range1 + 1;
			echo '<p>共充值'.$count.'笔，失败：'.$errors.'笔</p>';
		}else{
			echo <<<HTML
			<form method="post">
				用户前缀：<input type="text" name="prefix"><br>
				范围：<input type="text" name="srange"> -- <input type="text" name="erange"><br>
				充值金额：<input type="text" name="weight"><br>
				<input type="submit" name="modify" value="充值">
			</form>
HTML;
		}
		break;

	case 'sendGift':
		if(isset($_POST) && !empty($_POST)){
			$srange = intval($_POST['srange']);
			$erange = intval($_POST['erange']);
			if(empty($_POST['prefix'])){
				alertMsg('前缀不能为空');
			}
			if($srange > $erange){
				alertMsg("填写范围不正确");
			}
			$_POST['num'] = intval($_POST['num']);
			if($_POST['num'] < 1){
				alertMsg("礼物数量必须大于0");
			}
			$_POST['roomid'] = intval($_POST['roomid']);
			if($_POST['roomid'] < 1){
				alertMsg("房间号必须大于0");
			}

			$errors = 0;
			for($i=$srange;$i<=$erange;$i++){
				$loginInfo = httpPOST(SSO_API_PATH,array('param'=>array("SessionKey"=>$password,"Uin"=>$_POST['prefix'].$i),'extparam'=>array('Tag'=>'UserLogin')));
				if($loginInfo['Flag'] != 100){
					//处理错误
					showErrorLog("用户：{$loginInfo['Login']}，登录失败，失败原因：{$rst['FlagString']}");
					++$errors;
				}else{
					$param = array(
						'param' => array('BigCaseId'=>10001,'CaseId'=>10022,'ParentId'=>intval($_POST['gift']),'ChildId'=>101,'Uin'=>$loginInfo['Uid'],'SessionKey'=>$loginInfo['SessionKey'],'ChannelId'=>intval($_POST['roomid']),'TargetUin'=>$loginInfo['Uid'],'Client'=>'Web Client','DoingWeight'=>intval($_POST['num'])),
						'extparam' => array('Tag'=>'SendGift')
					);
					$rst = request($param);
					if($rst['Flag'] != 100){
						++$errors;
						showErrorLog("用户：{$loginInfo['Login']}，送礼失败，失败原因：{$rst['FlagString']}");
					}
				}
				usleep(1);
			}
			$count = $erange - $srange + 1;
			echo '<p>共送礼'.$count.'笔，失败：'.$errors.'笔</p>';
		}else{
			//查到所有礼物
			$giftList = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetUseParent','CaseId'=>10022)));
?>
			<form method="post">
				用户前缀：<input type="text" name="prefix"><br>
				范围：<input type="text" name="srange"> -- <input type="text" name="erange"><br>
				礼物：<select name="gift">
						<?php foreach($giftList as $k => $v): ?>
						<option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
						<?php endforeach; ?>
					  </select><br>
				礼物个数：<input type="text" name="num" ><br>
				房间号：<input type="text" name="roomid"><br>
				<input type="submit" name="modify" value="送礼">
			</form>
<?php
			
		}
		break;
}
?>
<ul>
	<li><a href="?module=openUser">开设用户</a></li>
	<li><a href="?module=recharge">充值</a></li>
	<li><a href="?module=sendGift">送礼</a></li>
</ul>