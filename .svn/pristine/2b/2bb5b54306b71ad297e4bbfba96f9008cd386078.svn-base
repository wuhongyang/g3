<?php
require_once 'common.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'index';

switch($module){
	default:
	case 'index':
		$info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));
		$info['email'] = $info['Email'];
		$info['phone'] = $info['Phone'];
		$info['idcard'] = $info['IdCard'];
		$info['username'] = $info['UserName'];
		$temp = 'index';
		break;
	/*
	case 'info':
		if(isset($_POST) && !empty($_POST)){
			$data = $_POST;
			$cityInfo = '';
			if(isset($data['city']) && $data['city']>0){
				$cityInfo = array('provinceId'=>intval($data['province']),'cityId'=>intval($data['city']));
				$cityInfo = json_encode($cityInfo);
			}
			unset($data['province'],$data['city']);
			$data['permanent_city'] = $cityInfo;
			$param = array(
				'extparam' => array('Tag'=>'SavePassInfo','Data'=>$data,'Uid'=>$user['Uid']),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>102)
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=info');
			}else{
				alertMsg($rst['FlagString'],-1);
			}
		}else{
			$param = array(
				'extparam' => array('Tag'=>'Info','Uid'=>$user['Uid']),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>101)
			);
			$info = request($param);
			$info = (array)$info['Info'];
			$addr = json_decode($info['permanent_city'],true);
			$provinceId = intval($addr['provinceId']);
			$cityId = intval($addr['cityId']);
			if($cityId <= 0){
				$provinces = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
				$provinces = (array)$provinces['Result'];
				unset($provinces[0]);
			}else{
				$pName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$provinceId)));
				$pName = $pName['provinceName'];
				$cName = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$cityId)));
				$cName = $cName['cityName'];
			}
			$temp = 'info';
		}
		break;
	case 'specialty':
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam' => array('Tag'=>'SaveSpecialty','Uid'=>$user['Uid'],'Data'=>$_POST),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10249,'ChildId'=>102)
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=specialty');
			}else{
				alertMsg($rst['FlagString'],-1);
			}
		}else{
			$param = array(
				'extparam' => array('Tag'=>'GetSpecialtyInfo','Uid'=>$user['Uid']),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10249,'ChildId'=>101)
			);
			$info = request($param);
			$info = (array)$info['Info'];
			$info['specialty'] = (array)json_decode($info['specialty'],true);
			$info['experience'] = (array)json_decode($info['experience'],true);
			$info['imgs'] = (array)json_decode($info['imgs'],true);
			$info['other_specialty'] = stripslashes($info['other_specialty']);
			$info['other_experience'] = stripslashes($info['other_experience']);

			$temp = 'specialty';
		}
		break;
	case 'upload':
		if(!empty($_FILES['fileField']) && $_FILES['fileField']['error']=='0'){
			if(strpos($_FILES['fileField']['type'], 'image') === false){
				alertMsg('上传图片格式必须为jpg，png，gif格式');
			}
			$size = $_FILES['fileField']['size']/(pow(1024, 2));
			if($size > 2){
				alertMsg('上传靓照不能大于2M，请重新上传');
			}
			$bytes = file_get_contents($_FILES['fileField']['tmp_name']);
			$index = md5($bytes);
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			$rst = array('Flag'=>$query['rst'],'File'=>$index);
			if($rst['Flag']==100){
				$rst['FileName'] = $_POST['filename'];
				@unlink($_FILES['fileField']['tmp_name']);
			}
			$rst = json_encode($rst);
		}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
			$rst = json_encode(array('Flag'=>101,'FlagString'=>'照片大小不能大于2M！'));
		}
		$temp = 'upload';
		break;
	case 'setDefaultUin':
		$uin = intval($_POST['uin']);
		$param = array(
			'extparam' => array('Tag'=>'SetDefaultUin','Uin'=>$uin),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>103)
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;*/
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