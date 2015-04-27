<?php
require_once '../library/global.fun.php';
$module = $_GET['module']?$_GET['module']:"tpl_list";
$link_array = getLevellink(10002,10069,10648,101);

switch($module){
	case "tpl_list":
		$param = array(
            'extparam'  => array(
                'Tag'   => 'TplList'
            ),
            'param'     => array(
                'BigCaseId' => 10002,
                'CaseId'    => 10069,
                'ParentId'  => 10648,
                'ChildId'   => 101,
                'Desc'      => '模板列表读取'
            )
        );
        $res = request($param);
		break;
    case "tpl_add":
        if($_POST){
            $tpl_name = addslashes(htmlspecialchars(trim($_POST['tpl_name'])));
            $tpl_desc = addslashes(htmlspecialchars(trim($_POST['tpl_desc'])));
            $status   = intval($_POST['status']);
            $id       = intval($_POST['id']);
            
            $param = array(
                'extparam'  => array(
                    'Tag'   => 'TplSave',
                    'Name'  => $tpl_name,
                    'Desc'  => $tpl_desc,
                    'Status'=> $status,
                    'Id'    => $id
                ),
                'param'     => array(
                    'BigCaseId' => 10002,
                    'CaseId'    => 10069,
                    'ParentId'  => 10648,
                    'ChildId'   => 102,
                    'Desc'      => '模板列表保存'
                )
            );
            $res = request($param);
            
            if($res['Flag'] == 100){
                $goto = "template.php?module=tpl_list";
            }else{
                $goto = -1;
            }
            ShowMsg($res['FlagString'], $goto);
        }
        
        $id = intval($_GET['id']);
        if($id){
            $param = array(
                'extparam'  => array(
                    'Tag'   => 'TplList',
                    'Data'  => array(
                                'Id' => $id
                                )
                ),
                'param'     => array(
                    'BigCaseId' => 10002,
                    'CaseId'    => 10069,
                    'ParentId'  => 10648,
                    'ChildId'   => 101,
                    'Desc'      => '模板列表读取'
                )
            );
            $res = request($param);
            $row = $res['Data'][0];
        }
        break;
    case 'tpl_header':
        $id = $_GET['search']['tpl_id'] ? $_GET['search']['tpl_id'] : $_GET['tpl_id'];
        $config_url = array(
            '角色体系'      => 'role.php?module=cate_list&search[tpl_id]='.$id,
            '积分规则'      => 'business_rule_define.php?module=list&search[tpl_id]='.$id,
            '业务参数'      => 'business_param_config.php?module=list&search[tpl_id]='.$id,
            '商城配置'      => 'commodity.php?module=list&tpl_id='.$id,
            '流媒体'        => 'media_config.php?tpl_id='.$id,
            '礼物配置'      => 'props_manage.php?tpl_id='.$id,
            '功能道具配置'  => 'function_props.php?tpl_id='.$id,
            '游戏道具配置'  => 'game_props.php?tpl_id='.$id,
            '表情配置'      => 'tpl_config_cate.php?module=cate_list&type=1&tpl_id='.$id,
            '印章配置'      => 'tpl_config_cate.php?module=cate_list&type=2&tpl_id='.$id,
            '整蛊道具配置'  => 'props_manage.php?module=props_list&is_tricky=1&tpl_id='.$id,
        );
        break;
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));

include template('template/'.$module.".html", $template);