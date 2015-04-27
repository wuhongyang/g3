<?php
$config_array = array(
	'kkyoo_voucher'=>array(
		'name'=>'V点',
		'field'=>array(
			'voucher_tot_margin'=>array('name'=>'总平衡','desc'=>'差额3'),
			'voucher_user_last_margin'=>array('name'=>'用户余额平衡','desc'=>'差额4'),
			'voucher_parent_last_margin'=>array('name'=>'科目余额平衡','desc'=>'差额5'),
			'voucher_user_ledger_margin'=>array('name'=>'用户总账流水平衡','desc'=>'差额6'),
			'voucher_user_tot_margin'=>array('name'=>'用户汇总流水平衡','desc'=>'差额7'),
			'voucher_parent_running_margin'=>array('name'=>'科目流水平衡','desc'=>'差额8'),
		)
	),
	'total_balance' => array(
		'name' => '总平衡',
		'field'=> array(
			'MarginEX' => array('name'=>'总资金平衡')
		)
	),
);
$all_field_num=count($config_array);
foreach($config_array as $key=>$val){
	$num=count($val['field']);
	$config_array[$key]['field_num']=$num;
	$all_field_num+=$num;;
}