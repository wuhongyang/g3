<?php
require_once '../library/global.fun.php';
$module = $_GET['module']?$_GET['module']:"cate_list";
$type = $_GET['type']?$_GET['type']:1;
$link_array = getLevellink(10002,10069,10648,101);

switch($module){
	case "cate_list":
        if($_POST){
            if($type == 1){
                $desc     = "模板表情分类保存";
                $child_id = 104;
            }elseif($type == 2){
                $desc     = "模板印章分类保存";
                $child_id = 108;
            }
            $cate_id = array_map(intval, $_POST['cate_id']);
            $param = array(
                'extparam'  => array(
                    'Tag'   => 'CateSave',
                    'Type'  => $type,
                    'TplId' => intval($_GET['tpl_id']),
                    'CateId'=> $cate_id,
                    'Id'    => intval($_POST['id'])
                ),
                'param'     => array(
                    'BigCaseId' => 10002,
                    'CaseId'    => 10069,
                    'ParentId'  => 10648,
                    'ChildId'   => $child_id,
                    'Desc'      => $desc
                )
            );
            $res = request($param);
            ShowMsg($res['FlagString'], "/admin/tpl_config_cate.php?module=cate_list&type=".$type."&tpl_id=".$_GET['tpl_id']);
        }
        if($type == 1){
            $desc     = "模板表情分类读取";
            $child_id = 103;
        }elseif($type == 2){
            $desc     = "模板印章分类读取";
            $child_id = 107;
        }
		$param = array(
            'extparam'  => array(
                'Tag'   => 'CateList',
                'Type'  => intval($type),
                'TplId' => intval($_GET['tpl_id'])
            ),
            'param'     => array(
                'BigCaseId' => 10002,
                'CaseId'    => 10069,
                'ParentId'  => 10648,
                'ChildId'   => $child_id,
                'Desc'      => $desc
            )
        );
        $res = request($param);
		break;
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));

include template('tbl_config_cate/'.$module.".html", $template);