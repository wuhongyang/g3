<?php
include_once dirname(__FILE__).'/library/power.class.php';
$power = new power();

$json = $_POST['extparam'];
$param = $_POST['param'];
$business_config = $_POST['BusinessConfig'];

switch($json['Tag']){
	case 'GetAdminLeftMenu':
		$array = $power->getAdminLeftMenu($param);
		echo json_encode($array);
		break;
	case 'GetLevelLink':
		$array = $power->GetLevelLink($param);
		echo json_encode($array);
		break;
	default:
		$array = $power->checkUserPower($param);
		if($array['Flag'] == 100){
			$parameter['param']['Admin_name'] = $array['Admin_name'];
			echo httpPOST($business_config['api'],$parameter,false);
		}else{
			echo json_encode($array);
		}		
		break;
}
