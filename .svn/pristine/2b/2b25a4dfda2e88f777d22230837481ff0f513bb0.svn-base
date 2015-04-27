<?php
require_once '../library/global.fun.php';

$link_array = getLevellink(10002,10069,10648,101);
$module = empty($_GET['module']) ? 'media_config' : trim($_GET['module']);

switch ($module) {
	case 'media_config':
        if($_POST){
            $param = array(
    			'param'=>array(
                    'BigCaseId' => 10002,
                    'CaseId'    => 10069,
                    'ParentId'  => 10648,
                    "ChildId"   => 106,
                    'Desc'      => "模板流媒体配置保存"),
    			'extparam'=>array(
                    "Tag"               => "SaveMediaConfig", 
                    "TplId"             => intval($_GET['tpl_id']),
                    "PlayMediaConf"     => $_POST['play_media_conf'],
                    "AdminMediaConf"    => $_POST['admin_media_conf'],
                    "P2pMediaConf"      => $_POST['p2p_media_conf']
                    )
    		);
            $res = request($param);
            ShowMsg($res['FlagString'], "media_config.php?tpl_id=".$_GET['tpl_id']);
        }
        $videocode = array('h264');
        $videosize = array('640*480','320*240','160*120');
        $audiocode = array('NellyMoser','Speex');
        $audiohz = array('96000','44100','32000','22050','16000','11025','8000');
        $highquality_audiocode = array('HE-AAC');
    
		$param = array(
			'param'=>array(
                'BigCaseId' => 10002,
                'CaseId'    => 10069,
                'ParentId'  => 10648,
                "ChildId"   => 105,
                'Desc'      => "模板流媒体配置读取"),
			'extparam'=>array(
                "Tag"       =>"MediaConfig", 
                "TplId"     =>intval($_GET['tpl_id'])
                )
		);
        $res = request($param);
        $edit = $res['Data'];
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('media_config/'.$module.".html",$tpl);