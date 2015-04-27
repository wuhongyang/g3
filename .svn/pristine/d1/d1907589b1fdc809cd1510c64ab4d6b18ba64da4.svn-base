<?php
$type = $_REQUEST['type'];
$base_path = dirname(__FILE__);

$path = $base_path."/phiz/{$type}/";
if(empty($type) || !is_dir($path)){
	exit('{"Flag":101,"FlagString":"参数不正确"}');
}

include $base_path.'/phiz/config.php';
foreach($defualt_category as $key=>$val){
	$fpath = $path."{$key}/";
	foreach(glob($fpath."*.{$type}") as $p){
		$p = str_replace($base_path,'',$p);
		$defualt_category[$key]['images'][] = "http://{$_SERVER['HTTP_HOST']}/pic{$p}";
	}
	$defualt_category[$key]['tooltip'] = $default_tooltip[$key];
}
$result = array('Flag'=>100,'FlagString'=>'获取表情成功','phiz'=>array_values($defualt_category));
echo json_encode($result);