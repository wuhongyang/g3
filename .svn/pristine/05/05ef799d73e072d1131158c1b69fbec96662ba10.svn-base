<?php
ignore_user_abort(true);
set_time_limit(0);
header("Content-type: text/html; charset=utf-8"); 
require_once '../library/global.fun.php';

$mysql = db::connect(config('database','default'));

$sql = 'SELECT group_id,cate_id FROM '.DB_NAME_SHOP.'.goods GROUP BY cate_id';
$res = $mysql->get_results($sql, ASSOC);

foreach((array)$res as $val){
	$sql = "INSERT INTO ".DB_NAME_SHOP.".goods_sub_cat(`name`,`group_id`,`cate_id`,`status`) VALUES('默认分类',{$val['group_id']},{$val['cate_id']},1)";
	if(!$mysql->query($sql)){
		echo "goods,,,,,insert table 'goods_sub_cat' fail, rows as group_id:{$val['group_id']}, cate_id:{$val['cate_id']} <br>";
		continue;
	}
	$insert_id = $mysql->insert_id();
	$sql = "UPDATE ".DB_NAME_SHOP.".goods SET sub_cate_id={$insert_id} WHERE group_id={$val['group_id']} AND cate_id={$val['cate_id']}";
	$mysql->query($sql);
	$sql = "UPDATE ".DB_NAME_SHOP.".goods_sub_cat SET `order`={$insert_id} WHERE id={$insert_id}";
	$mysql->query($sql);
}

$sql = 'SELECT group_id,cate_id FROM '.DB_NAME_SHOP.'.goods_package GROUP BY cate_id';
$res = $mysql->get_results($sql, ASSOC);

foreach((array)$res as $val){
	$sql = "INSERT INTO ".DB_NAME_SHOP.".goods_sub_cat(`name`,`group_id`,`cate_id`,`status`) VALUES('默认分类',{$val['group_id']},{$val['cate_id']},1)";
	if(!$mysql->query($sql)){
		echo "goods_package,,,,,insert table 'goods_sub_cat' fail, rows as group_id:{$val['group_id']}, cate_id:{$val['cate_id']} <br>";
		continue;
	}
	$insert_id = $mysql->insert_id();
	$sql = "UPDATE ".DB_NAME_SHOP.".goods_package SET sub_cate_id={$insert_id} WHERE group_id={$val['group_id']} AND cate_id={$val['cate_id']}";
	$mysql->query($sql);
	$sql = "UPDATE ".DB_NAME_SHOP.".goods_sub_cat SET `order`={$insert_id} WHERE id={$insert_id}";
	$mysql->query($sql);
}
echo 'done';