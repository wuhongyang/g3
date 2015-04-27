<?php
require_once dirname(__FILE__).'/library/menu.class.php';

$m = new Menu();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetMenuList': //一级菜单列表获取
		echo json_encode($m->getMenuList($json['GroupId']));
		break;
	case 'MenuInfo': //菜单详情
		echo json_encode($m->menuInfo($json['Id'],$json['GroupId']));
		break;
	case 'MenuAdd': //一级菜单添加
		echo json_encode($m->menuAdd($json['Data']));
		break;
	case 'MenuEdit'://一级菜单编辑
		echo json_encode($m->menuEdit($json['Data']));
		break;
	case 'MenuVisible':
		echo json_encode($m->menuVisible($json['Id'],$json['GroupId']));
		break;
	case 'SubMenuVisible':
		echo json_encode($m->subMenuVisible($json['Id'],$json['GroupId']));
		break;
	case 'Up'://上移
		echo json_encode($m->up($json['Id'],$json['GroupId']));
		break;
	case 'Down'://下移
		echo json_encode($m->down($json['Id'],$json['GroupId']));
		break;
	case 'SubMenuList'://二级菜单列表获取
		echo json_encode($m->subMenuList($json['GroupId'],$json['SuperId']));
		break;
	case 'SubMenuAdd'://二级菜单添加
		echo json_encode($m->subMenuAdd($json['Data']));
		break;
	case 'SubMenuEdit'://二级菜单编辑
		echo json_encode($m->subMenuEdit($json['Data']));
		break;
}