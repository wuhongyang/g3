<?php
require_once 'library/group_template.class.php';
require_once 'library/commodity.class.php';
require_once 'library/param_config.class.php';
require_once 'library/rule_define.class.php';
require_once 'library/role.class.php';
$role = new Roles();
$ruleDefine = new RuleDefine();
$paramConfig = new ParamConfig();
$c = new Commodity();
$GroupTemplate = new GroupTemplate();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'TplList':
		echo json_encode($GroupTemplate->tpl_list($json['Data']));
		break;
    case 'TplSave':
        echo json_encode($GroupTemplate->tpl_save($json['Id'], $json['Name'], $json['Desc'], $json['Status']));
		break;
    case 'MediaConfig':
        echo json_encode($GroupTemplate->media_config($json['TplId']));
		break;
    case 'SaveMediaConfig':
        echo json_encode($GroupTemplate->save_media_config($json['PlayMediaConf'], $json['AdminMediaConf'], $json['P2pMediaConf'], $json['TplId']));
        break;
    case 'CateList':
        echo json_encode($GroupTemplate->cate_list($json['Type'], $json['TplId']));
        break;
    case 'CateSave':
        echo json_encode($GroupTemplate->cate_save($json['Id'], $json['CateId'], $json['Type'], $json['TplId']));
        break;
    
    case 'CommodityList':
		echo json_encode($c->lists($json['Search']));
		break;
	case 'CommodityInfo':
		echo json_encode($c->info($json['Id']));
		break;
	case 'CommodityGetByCategory':
		echo json_encode($c->getByCategory($json['Category']));
		break;
	case 'CommodityAdd':
		echo json_encode($c->add($json['Data']));
		break;
	case 'CommodityEdit':
		echo json_encode($c->edit($json['Id'],$json['Data']));
		break;
    
    case 'ParamConfigList':
		echo json_encode($paramConfig->lists($json['SearchData']));
		break;
	case 'ParamConfigInfo':
		echo json_encode($paramConfig->getInfo($json['Id'],$json['TplId']));
		break;
	case 'ParamConfigAdd':
		echo json_encode($paramConfig->add($json['Info']));
		break;
	case 'ParamConfigUpdate':
		echo json_encode($paramConfig->update($json['Info'],$json['Id']));
		break;
    
    case 'RuleDefineList':
		echo json_encode($ruleDefine->lists($json['SearchData']));
		break;
	case 'RuleDefineListPage':
		echo json_encode($ruleDefine->lists($json['SearchData'], true));
		break;
	case 'RuleDefineInfo':
		echo json_encode($ruleDefine->getInfo($json['Id']));
		break;
	case 'RuleDefineAdd':
		echo json_encode($ruleDefine->add($json['Info']));
		break;
	case 'RuleDefineUpdate':
		echo json_encode($ruleDefine->update($json['Info'],$json['Id']));
		break;
	case 'RuleDefineSync':
		echo json_encode($ruleDefine->sync());
		break;	
	case 'GetRuleDefine':
		echo json_encode($ruleDefine->getRuleDefine($json['TplId']));
		break;

	case 'RoleCate':
		echo json_encode($role->cateList($json['SearchData'],$json['IsNotPage']));
		break;
	case 'SaveCate':
		echo json_encode($role->saveCate($json['Data']));
		break;
	case 'CateInfo':
		echo json_encode($role->cateInfo($json['CateId']));
		break;
	case 'DelCate':
		echo json_encode($role->cateDel($json['CateId']));
		break;
	case 'RoleList':
		echo json_encode($role->roleList($json['SearchData'],$json['IsNotPage']));
		break;
	case 'RoleInfo':
		echo json_encode($role->roleInfo($json['Id']));
		break;
	case 'RoleUpdate':
		echo json_encode($role->updateRole($json['Data'],$json['Id']));
		break;
	case 'RoleAdd':
		echo json_encode($role->addRole($json['Data']));
		break;
	case 'DelRole':
		echo json_encode($role->delRole($json['RoleId']));
		break;
	case 'PermissionList':
		echo json_encode($role->permissionList($json['RoleId']));
		break;
	case 'SaveRolePermission':
		echo json_encode($role->saveRolePermission($json['RoleId'],$json['Data']));
		break;
	case 'UnbindRole':
		echo json_encode($role->unbindRole($param['TargetUin'],$json['RoleId']));
		break;
	case 'SetRole2User':
		echo json_encode($role->setRole2User($param['TargetUin'],$json['Role']));
		break;
	case 'CopyRole':
		echo json_encode($role->copyRole($json['RoleId']));
		break;

    default :
		echo httpPOST("api/admin/interact_api.php",$_POST,false);
		break;
}