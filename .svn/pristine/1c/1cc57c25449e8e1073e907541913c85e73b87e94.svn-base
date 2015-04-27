<?php
ignore_user_abort(true);
set_time_limit(0);

define('LIANG_PARENT_ID', 10428);

require_once '../library/global.fun.php';
$mysql = db::connect(config('database','default'));

$sql = "SELECT case_id,parent_id FROM ".DB_NAME_TPL.".commodity GROUP BY parent_id";
$res = $mysql->get_results($sql,ASSOC);
$cases = array();
foreach ((array)$res as $k => $v) {
	$cases[$v['parent_id']] = $v['case_id'];
}

//找出不是靓号的记录，，靓号需要特殊处理
$sql = "SELECT id,commodity,group_id,parent_id FROM ".DB_NAME_SHOP.".commodity_stock";
$res = $mysql->get_results($sql,ASSOC);

foreach ((array)$res as $key => $val) {
	//找到二级ID
	$case_id = $cases[$val['parent_id']];
	if($val['parent_id'] == LIANG_PARENT_ID){
		$sql = "SELECT category FROM ".DB_NAME_SHOP.".special_num WHERE group_id={$val['group_id']} AND id={$val['commodity']}";
		$cate_id = $mysql->get_var($sql);
		if($cate_id < 1){
			echo "ID为：{$val['id']} 没有找到对应分类id，无法更新\r\n";
			continue;
		}
		$goods_id = $val['commodity'];
	}else{
		//找到站后台配置
		$sql = "SELECT id AS goods_id,cate_id FROM ".DB_NAME_SHOP.".goods WHERE group_id={$val['group_id']} AND commodity_id={$val['commodity']}";
		$row = $mysql->get_row($sql,ASSOC);
		$cate_id = $row['cate_id'];
		$goods_id = $row['goods_id'];
		if($cate_id < 1 || $goods_id < 1){
			echo "ID为：{$val['id']} 没有找到对应分类id，无法更新\r\n";
			continue;
		}
	}

	//更新库存表
	$sql = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET cate_id={$cate_id},commodity={$goods_id},case_id={$case_id} WHERE id={$val['id']}";
	if(!$mysql->query($sql)){
		echo 'ID为：'.$val['id']." 更新失败\r\n";
	}
}
echo "\r\n完成";

//unlink("./user.lock");
/*
 * unlink("./user.php");
*/