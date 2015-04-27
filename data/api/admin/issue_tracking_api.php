<?php
include_once dirname(__FILE__).'/library/issue_tracking.class.php';
$issue = new issue();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'InitiateTypeList':
		$array = $issue->InitiateTypeList($json['Data']['no_page']);
		echo json_encode($array);
		break;
	case "InitiateTypeInfo":
		$array = $issue->InitiateTypeInfo($json['Data']['Id']);
		echo json_encode($array);
		break;
	case "InitiateTypeSave":
		$array = $issue->InitiateTypeSave($json['Data']);
		echo json_encode($array);
		break;
	case "InitiateTypeDel":
		$array = $issue->InitiateTypeDel($json['Data']['Id']);
		echo json_encode($array);
		break;
	case 'LevelList':
		$array = $issue->LevelList($json['Data']);
		echo json_encode($array);
		break;
	case "LevelInfo":
		$array = $issue->LevelInfo($json['Data']['Id']);
		echo json_encode($array);
		break;
	case "LevelSave":
		$array = $issue->LevelSave($json['Data']);
		echo json_encode($array);
		break;
	case "LevelDel":
		$array = $issue->LevelDel($json['Data']['Id']);
		echo json_encode($array);
		break;
	case "Collection":
		$array = $issue->Collection($json['Data']);
		echo json_encode($array);
		break;
	case "IssueList":
		$array = $issue->IssueList($json['Data']);
		echo json_encode($array);
		break;
	case "IssueAdd":
		$array = $issue->IssueAdd($json['Data']);
		echo json_encode($array);
		break;
	case "IssueEdit":
		$array = $issue->IssueEdit($json['Data']);
		echo json_encode($array);
		break;
	case "IssueInfo":
		$array = $issue->IssueInfo($json['Data']['Id']);
		echo json_encode($array);
		break;
	case "get_after_level":
		$array = $issue->get_after_level($json['Data']['p_id']);
		echo json_encode($array);
		break;
	case "get_before_level":
		$array = $issue->get_before_level($json['Data']['id']);
		echo json_encode($array);
		break;
	
}
