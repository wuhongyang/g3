<?php
require_once 'library/help.class.php';

$help = new help();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ClassifyList':
		echo json_encode($help->classify_list($json['Data']));
		break;
	case 'SaveClassify':
		echo json_encode($help->save_classify($json['Data']));
		break;
	case 'DelClassify':
		echo json_encode($help->del_classify($json['Id']));
		break;
	case 'SubstanceList':
		echo json_encode($help->substance_list($json['Data']));
		break;
	case 'SaveSubstance':
		echo json_encode($help->save_substance($json['Data']));
		break;
	case 'DelSubstance':
		echo json_encode($help->del_substance($json['Id']));
		break;
	case 'GetLinkList':
		//{"Tag":"GetLinkList"}
		echo json_encode($help->get_link_list($json['Data']));
		break;
	case 'SaveLink':
		//{"Tag":"SaveLink","Data":{"Id":2,"SiteName":"啊啊","Url":"http://www.baidu.com","Order":"1","ImgCatId":"1","ImgPicId":1}}
		echo json_encode($help->save_link($json['Data']));
		break;
	case 'DelLink':
 		//{"Tag":"DelLink","Data":{"Id":2}}
		echo json_encode($help->del_link($json['Data']));
		break;
	case 'AddEmail':
		echo json_encode($help->add_email($json['Data']));
		break;
	case 'EmailList':
		echo json_encode($help->email_list($json['StartTime'], $json['EndTime']));
		break;
	case 'EmailDetail':
		echo json_encode($help->email_detail($json['Id']));
		break;
}