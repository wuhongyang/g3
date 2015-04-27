<?php
require dirname(__FILE__).'/library/act.class.php';
$json = $_POST['extparam'];
$act = new act($json['DataGroupId']);

switch($json['Tag']){
	case 'Act_list':
		$array = $act->actList($json);
		echo json_encode($array);
		break;
}
