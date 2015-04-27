<?php
require_once 'library/vdmanage.class.php';
$vdmanage = new vdmanage();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']) {
	case 'ShowVdList':	//V豆明细查询
		echo json_encode($vdmanage->showVdList($json['Uin'] ,$json));
		break;
	case 'ShowVDiList':	//V点账户查询
		echo json_encode($vdmanage->ShowVDiList($json['Uin']));
		break;
	case 'VdDonate':	//V豆捐赠
		echo json_encode($vdmanage->vdDonate($param));
		break;
	case 'VdianList':	//V点明细
		echo json_encode($vdmanage->VdianList($json));
		break;
	case 'VChange':	//V豆捐赠
		echo json_encode($vdmanage->VChange( $json));
		break;
	case 'GroupInfo':	//V豆捐赠
		echo json_encode($vdmanage->GroupInfo( $json));
		break;
}
