<?php
require_once 'library/rank.class.php';
$r = new Rank();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'RankList':
		echo json_encode($r->rankList($json['GroupId']));
		break;
	case 'RankIndexSave':
		echo json_encode($r->rankIndexSave($json['GroupId'],$json['Rank']));
		break; 
	case 'RankSave':
		echo json_encode($r->rankSave($json['GroupId'],$json['Rank']));
		break; 
}