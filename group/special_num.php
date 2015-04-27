<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'cate':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$permission=(array)$permisssions['permission'];
$group_id=(int)$permisssions['groupId'];

if(!checkGroupPermission(10429,$permission)){
	alertMsg('无权访问','/group/mgr.html');
}

$title = "靓号管理-靓号分类管理";

switch($module){
	default:
	case 'cate':
		$param = array(
				'extparam' => array('Tag'=>'GetBannerImg', 'GroupId'=>$group_id),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>101,'Desc'=>'商品宣传图片读取')
		);
		$result = request($param);
		$img_data = $result['Data'];
		$param = array(
			'extparam' => array('Tag'=>'CateList', 'GroupId'=>$group_id),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>101,'Desc'=>'靓号分类读取')
		);
		$result = request($param);
		$list = $result['List'];
		$page = $result['Page'];
		break;
	case 'num_list':
		$param = array(
				'extparam' => array('Tag'=>'NumList', 'GroupId'=>$group_id, 'CateId'=>$_GET['cate_id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>101,'Desc'=>'靓号分类读取')
		);
		$result = request($param);
		$list = (array)$result['List'];
		$page = $result['Page'];
		$cate_name = $result['CateName'];
		break;
	case 'add_num':
		$param = array(
				'extparam' => array('Tag'=>'GetCateName', 'GroupId'=>$group_id, 'CateId'=>$_GET['cate_id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>101,'Desc'=>'靓号分类读取')
		);
		$result = request($param);
		if($result['Flag'] != 100){
			ShowMsg($result['FlagString'], -1);
		}
		$cate_name = $result['CateName'];
		break;
	case 'cate_add':
		$cate_name = addslashes(htmlspecialchars(trim($_POST['cate_name'])));
		$words = addslashes(htmlspecialchars(trim($_POST['words'])));
		$param = array(
			'extparam' => array('Tag'=>'AddCate', 'GroupId'=>$group_id, 'GroupName'=>$cate_name,'ShopStaus'=>intval($_POST['shop_status']),"Words"=>$words),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>102,'Desc'=>'靓号分类保存')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'cate_update':
		$cate_name = addslashes(htmlspecialchars(trim($_POST['cate_name'])));
		$words = addslashes(htmlspecialchars(trim($_POST['words'])));
		$param = array(
				'extparam' => array('Tag'=>'UpdateCate', 'GroupId'=>$group_id, 'GroupName'=>$cate_name, "CateId"=>$_POST['cate_id'],'ShopStaus'=>intval($_POST['shop_status']),"Words"=>$words),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>102,'Desc'=>'靓号分类保存')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'cate_move':
		$param = array(
				'extparam' => array('Tag'=>'UpdateOrder', 'GroupId'=>$group_id, 'CateId'=>$_POST['cate_id'], 'Option'=>$_POST['option']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>103,'Desc'=>'靓号分类排序')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'cate_delete':
		$param = array(
				'extparam' => array('Tag'=>'DeleteCate', 'GroupId'=>$group_id, 'CateId'=>$_POST['cate_id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>104,'Desc'=>'靓号分类删除')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'num_add':
		$param = array(
				'extparam' => array('Tag'=>'AddNum', 'GroupId'=>$group_id, 'Category'=>$_POST['cate_id'], 'Price'=>$_POST['price'], 'Options'=>array(), 'Name'=>$_POST['name']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>105,'Desc'=>'靓号添加')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'num_delete':
		$param = array(
				'extparam' => array('Tag'=>'DeleteNum', 'GroupId'=>$group_id, 'Id'=>$_POST['id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>106,'Desc'=>'靓号删除')
		);
		$result = request($param);
		echo json_encode($result);
		exit;
	case 'gift':
		if(isset($_POST) && !empty($_POST)){
			$_POST['group_id'] = $group_id;
			$_POST['pay_uin'] = $user['Uin'];
			$param = array(
				'extparam' => array('Tag'=>'GiftNum', 'Data'=>$_POST),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>109,'Desc'=>$user['Uin'].'赠送'.$_POST['uin'].'靓号'.$_POST['special_num'])
			);
			$result = request($param);
			echo json_encode($result);exit;
		}else{

		}
		break;
	case 'num_record':
		$param = array(
			'extparam' => array('Tag'=>'NumRecord', 'GroupId'=>$group_id,'Data'=>$_GET),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>108,'Desc'=>'靓号库存记录')
		);
		$list = request($param);
		$page = $list['Page'];
		$list = (array)$list['List'];
		foreach ($list as $key => $val) {
			$result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['uin'])));
			$list[$key]['nick'] = empty($result['Nick']) ? $val['uin'] : $result['Nick'];
		}
		break;
	case 'recycle':
		$param = array(
			'extparam' => array('Tag'=>'NumRecord', 'GroupId'=>$group_id,'Data'=>array('stock_id'=>$_POST['stock_id'])),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>108,'Desc'=>'靓号库存记录')
		);
		$info = request($param);
		$uin = $info['List'][0]['uin'];
		$other_id = $info['List'][0]['other_id'];
		$param = array(
			'extparam' => array('Tag'=>'RecycleNum', 'GroupId'=>$group_id,'StockId'=>$_POST['stock_id']),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>107,'Desc'=>$user['Uin'].'收回'.$uin.'靓号'.$other_id)
		);
		$rst = request($param);
		echo json_encode($rst);exit;
		break;
	case 'add_img':
		if($_POST){
			$index = "";
			if($_POST['del_img']){
				$param = array(
						'extparam' => array('Tag'=>'BannerImg', 'GroupId'=>$group_id, 'ImgPath'=>$index, "Src"=>htmlspecialchars(addslashes(trim($_POST['src']))), 'DelImg'=>1),
						'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>110,'Desc'=>'靓号宣传图片')
				);
			}else{
				if($_FILES['img']['tmp_name']){
					$bytes = file_get_contents($_FILES['img']['tmp_name']);
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				}
				$param = array(
						'extparam' => array('Tag'=>'BannerImg', 'GroupId'=>$group_id, 'ImgPath'=>$index, "Src"=>htmlspecialchars(addslashes(trim($_POST['src'])))),
						'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>110,'Desc'=>'靓号宣传图片')
				);
			}
			$info = request($param);
			ShowMsg($info['FlagString'], "?module=cate");
		}
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('special_num/'.$module.".html",$tpl);