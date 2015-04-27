<?php
require_once 'library/link.class.php';
$l = new Link();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch($json['Tag']){
	case 'LinkCateList'://友情链接分类列表
		echo json_encode($l->linkCateList($json['GroupId']));
		break;
	case 'LinkCateSave'://友情链接分类保存
		echo json_encode($l->linkCateSave($json['data']));
		break;
	case 'LinkCateShow'://友情链接分类是否显示
		echo json_encode($l->linkCateShow($json['data']));
		break;
	case 'LinkList'://友情链接列表读取
		echo json_encode($l->linkList($json['data']));
		break;
	case 'LinkSave'://友情链接保存
		echo json_encode($l->linkSave($json['data']));
		break;
	case 'LinkInfo'://
		echo json_encode($l->linkInfo($json['id']));
		break;
	case 'LinkDel':
		echo json_encode($l->linkDel($json['id']));
		break;
}