<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

//友情链接
$flParam = array(
	'extparam' => array('Tag'=>'GetLinkList','Limit'=>-1),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10232,'ChildId'=>101)
);
$fl = request($flParam);
$friendLink = (array)$fl['Data'];

$flHasPic = array();
foreach ($friendLink as $key => $value) {
	if($value['img_pic_id'] > 0){
		$picInfo = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$value['img_pic_id']))));
		if($picInfo['lists'][0]['img_path']){
			$value['logo'] =  '{PIC_API_PATH}/p/'.$picInfo['lists'][0]['img_path'];
		}
		$flHasPic[] = $value;
		unset($friendLink[$key]);
	}
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template("friend_link.html",$tpl);