<?php
/*
session_start();
if(empty($_SESSION['adminLogin'])){
	header('Location:login.php');exit;
}
*/

$config = array(
/*
  10003 => array( 
    'url'=>'props_manage.php',
    'list_url'=>array(
		101 =>'props_manage.php?module=props_list',
		102 =>'props_info.php',
		103 =>'props_info.php',
		104 =>'props_manage.php?module=props_del',
		105 =>'props_manage.php?module=props_order',
    	106 =>'props_manage.php?module=cate_list',
    	107 =>'props_manage.php?module=add_cate',
	)
  ),
  */
  10004 => array( 
    'url'=>'interact_manage.php',
    'list_url'=>array(
		101=>'interact_manage.php',
		102=>'interact_info.php',
		103=>'interact_info.php',
		104=>'interact_manage.php?module=interact_del',
		105=>'interact_manage.php?module=interact_order',
		106=>'interact_manage.php?module=interact_config',
		107=>'interact_manage.php?module=rate_config',
		108=>'interact_manage.php?module=room_auth_config',
		109=>'interact_manage.php?module=room_auth_info',
		110=>'interact_manage.php?module=room_auth_info',
		111=>'interact_manage.php?module=room_auth_del',
	)
  ),
  10002 => array( 
    'url'=>'group.php?module=GroupList',
    'list_url'=>array(
		101=>'group.php?module=GroupList',
		102=>'group.php?module=info',
		103=>'group.php?module=info',
		104=>'group.php?module=addGroup',
		105=>'group.php?module=groupMemberList',
		106=>'group.php?module=addGroupMember',
		107=>'group.php?module=groupMemberList',
		108=>'group.php?module=groupMemberList',
		109=>'group.php?module=addGroupMember',
		110=>'group.php?module=game_manager',
		111=>'group.php?module=game_interface_setting',
    	113=>'group.php?module=practice_account_list',
    	114=>'group.php?module=practice_account_edit',
    	115=>'group.php?module=practice_account_del',
    	116=>'group.php?module=sync_info'
	)
  ),
  10005 => array( 
    'url'=>'passport.php',
    'list_url'=>array(
		101=>'passport.php?module=listPass',
		102=>'passport.php?module=state',
		103=>'passport.php?module=reset',
	)
  ),
  10006 => array( 
    'url'=>'passport.php',
    'list_url'=>array(
		101=>'passport.php?module=listPass',
		102=>'passport.php?module=state',
		103=>'passport.php?module=reset',
    	104=>'passport.php?module=email',
    	105=>'passport.php?module=phone',
    	106=>'passport.php?module=bind',
	)
  ),
  10010 => array( 
    'url'=>'admin.php?module=cluster_list',
    'list_url'=>array(
		101=>'admin.php?module=cluster_list',
		102=>'admin.php?module=group_create',
		103=>'admin.php?module=group_editor',
		106=>'admin.php?module=cluster_create',
		107=>'admin.php?module=group_list',
		108=>'admin.php?module=cluster_editor',
	)
  ),
  10012 => array( 
    'url'=>'level.php?module=user_list',
    'list_url'=>array(
		101=>'level.php?module=user_create',
		102=>'level.php?module=user_list',
		103=>'level.php?module=user_editor',
	)
  ),
  10011 => array( 
    'url'=>'kmoney.php?module=user_balance',
    'list_url'=>array(
		101=>'kmoney.php?module=user_balance',
		103=>'kmoney.php?module=user_detail',
		104=>'kmoney.php?module=tax_detail',
		105=>'kmoney.php?module=day_total',
		106=>'kmoney.php?module=deposit_pay',
		107=>'kmoney.php?module=business_day_total',
		109=>'kmoney.php?module=parent_total',
		110=>'kmoney.php?module=account_balance',
	)
  ),
  10008 => array( 
    'url'=>'act_detail.php?module=act_list',
  ),
  10013 => array( 
    'url'=>'ccs.php?module=bigCaseList',
    'list_url'=>array(
		101=>'ccs.php?module=bigCaseList',
		102=>'ccs.php?module=bigCaseInfo',
		103=>'ccs.php?module=bigCaseInfo',
		107=>'ccs.php?module=caseList',
		108=>'ccs.php?module=caseInfo',
		109=>'ccs.php?module=caseInfo',
		112=>'ccs.php?module=parentList',
		113=>'ccs.php?module=parentInfo',
		114=>'ccs.php?module=parentInfo',
		117=>'ccs.php?module=childList',
		118=>'ccs.php?module=childInfo',
		119=>'ccs.php?module=childInfo',
		122=>'ccs.php?module=childSync',
		123=>'ccs.php?module=caseUp',
		124=>'ccs.php?module=caseDown',
		123=>'ccs.php?module=caseOrder',
	)
  ),
  10014 => array( 
    'url'=>'common_child.php?module=list',
    'list_url'=>array(
		101=>'common_child.php?module=list',
		102=>'common_child.php?module=info',
		103=>'common_child.php?module=info',
		104=>'common_child.php?module=sync',
	)
  ),
  10015 => array( 
    'url'=>'business.php?module=parentList',
    'list_url'=>array(
		101=>'interact_manage.php?module=add',
		102=>'interact_manage.php?module=del',
	)
  ),
  10016 => array( 
    'url'=>'business.php?module=childList',
    'list_url'=>array(
		101=>'interact_manage.php?module=add',
		102=>'interact_manage.php?module=del',
	)
  ),
  10020 => array( 
    'url'=>'rooms.php?module=roomList',
    'list_url'=>array(
		101=>'rooms.php?module=roomList',
		102=>'rooms.php?module=freeze',
		103=>'room_info.php',
		104=>'room_info.php',
		105=>'rooms.php?module=admin_recmd',
	)
  ),
  10019 => array(
    'url'=>'picture_config.php',
    'list_url'=>array(
		101=>'picture_config.php?module=catList',
		102=>'picture_config.php?module=addPicCat',
		103=>'picture_config.php?module=addPicCat',
		104=>'picture_config.php?module=picList',
		105=>'picture_config.php?module=addPic',
		106=>'picture_config.php?module=addPic',
		107=>'picture_config.php?module=delPicCat',
	)
  ),
  10022 => array(
	'url' => 'props_config.php',
	'list_url' => array(
		101 => 'props_config.php',
		102 => 'props_config_info.php',
		103 => 'props_config_info.php',
	)
  ),
  10023 => array(
	  'url'=> 'systemindex.php',
	  'list_url' => array(
		  101=>'systemindex.php',
	   )
   ),
   10028=> array(
	   'url'=> 'usermanage.php?module=usermanage',
	   'list_url' => array(
		   101 => 'usermanage.php?module=usermanage',
	   	   102 => 'usermanage.php?module=detail',
	)
   ),
  10030 => array( 
    'url'=>'kwealth.php?module=user_balance',
    'list_url'=>array(
		101=>'kwealth.php?module=user_balance',
		102=>'kwealth.php?module=user_detail',
		103=>'kwealth.php?module=day_total',
		104=>'kwealth.php?module=parent_total',
		105=>'kwealth.php?module=deposit_pay',
		106=>'kwealth.php?module=account_balance',
		107=>'kwealth.php?module=business_day_total',
	)
  ),
  10027 => array(
		'url' => 'partner_channel.php?module=partnerList',
		'list_url' => array(
			101 => 'partner_channel.php?module=partnerList',
			103 => 'partner_info.php?module=infoList',
			104 => 'partner_channel.php?module=channelList',
			106 => 'partner_channel.php?module=zzAdd',
			107 => 'partner_channel.php?module=showChannel',
			109 => 'partner_channel.php?module=setSalaryAndReward',
			110 => 'partner_info.php?module=infoList',
			111 => 'channel_info.php?module=infoList',
			112 => 'partner_channel.php?module=agentAdd',
			115 => 'partner_channel.php?module=channelSync',
		)
   ),
   /*10033 => array(
		'url' => 'channel_income.php?module=channelincomePool',
		'list_url' => array(
			101 => 'channel_income.php?module=channelincomePool',
			102 => 'channel_income.php?module=setMoneyInfo',
			103 => 'channel_income.php?module=channelTaxList',
			104 => 'channel_income.php?module=channelDetailList',
			105 => 'channel_income.php?module=save'
		)
   ),
   10162 => array(
		'url' => 'channel_income.php?module=ownincomePool',
		'list_url' => array(
			101 => 'channel_income.php?module=ownincomePool',
			102 => 'channel_income.php?module=ownincomeDetail',
		)
   ),
   10163 => array(
		'url' => 'channel_income.php?module=actincomePool',
		'list_url' => array(
			101 => 'channel_income.php?module=actincomePool',
			102 => 'channel_income.php?module=actincomeDetail',
		)
   ),
   10164 => array(
		'url' => 'business_param_config.php?module=param_group_list',
		'list_url' => array(
			101 => 'business_param_config.php?module=param_group_list',
			102 => 'business_param_config.php?module=param_group_edit',
			103 => 'business_param_config.php?module=param_group_edit',
			104 => 'business_param_config.php?module=param_group_del',
		)
	),*/
    10337 => array(
		'url' => 'rooms.php?module=rooms_ui_list',
		'list_url' => array(
            101 => 'rooms.php?module=rooms_ui_list',
			102 => 'rooms.php?module=update_rooms_ui',
			103 => 'rooms.php?module=del_rooms_ui',
		)
	),
    10399 => array(
		'url' => 'rooms.php?module=ui_package_list',
		'list_url' => array(
            101 => 'rooms.php?module=ui_package_list',
            102 => 'rooms.php?module=update_ui_package',
            103 => 'rooms.php?module=del_ui_package',
			104 => 'rooms.php?module=copy_ui_package',
		)
	),
	/*
   10166 => array(
		'url' => 'channel_income.php?module=channelaccess',
		'list_url' => array(
			101 => 'channel_income.php?module=channelaccess',
			102 => 'channel_income.php?module=channelroomrank',
			103 => 'channel_income.php?module=channelactrank',
		)
   ),*/
   10188 => array(
		'url' => 'channel_income.php?module=channelownrmb',
		'list_url' => array(
			101 => 'channel_income.php?module=channelownrmb',
			102 => 'channel_income.php?module=channelactrmb',
		)
   ),
   /*
   10189 => array(
		'url' => 'roomrecommend.php?module=list&type=2',
		'list_url' => array(
			101 => 'roomrecommend.php?module=list&type=2',
			102 => 'roomrecommend.php?module=info&type=2',
			103 => 'roomrecommend.php?module=update&type=2',
			104 => 'roomrecommend.php?module=list&type=1',
			105 => 'roomrecommend.php?module=info&type=1',
			106 => 'roomrecommend.php?module=update&type=1',
			107 => 'roomrecommend.php?module=roomnum&type=2',
			108 => 'roomrecommend.php?module=roomnum&type=1',
		)
   ),*/
   10034 =>array(
		'url' => 'channel_category.php?module=list',
		'list_url' => array(
			101 => 'channel_category.php?module=list',
			103 => 'channel_category.php?module=info',
			104 => 'channel_category.php?module=info',
		)
   ),
   /*
   10066 => array(
		'url' => 'region_site.php?module=list',
		'list_url' => array(
			101 => 'region_site.php?module=list',
			103 => 'region_site.php?module=info',
			104 => 'region_site.php?module=hot',
			105 => 'region_site.php?module=info',
		)
   ),
   10067 => array(
		'url' => 'site_category.php?module=caseList',
		'list_url' => array(
			101 => 'site_category.php?module=caseList',
			103 => 'site_category.php?module=caseInfo',
			104 => 'site_category.php?module=caseInfo',
			105 => 'site_category.php?module=showSubSiteCategory',
			106 => 'site_category.php?module=caseOrder',
			109 => 'site_category.php?module=subCategoryInfo',			
			110 => 'site_category.php?module=subCategoryInfo',
			111 => 'site_category.php?module=subCategoryInfo',
			112 => 'site_category.php?module=subCategoryOrder',
		)
   ),
   10068 => array(
		'url' => 'dialect.php?module=dialectList',
		'list_url' => array(
			101 => 'dialect.php?module=dialectList',
			103 => 'dialect.php?module=dialectInfo',
			104 => 'dialect.php?module=dialectInfo',
		)
   ),*/
   10038 => array(
		'url' => 'integrate_detail.php?module=detailList',
		'list_url' => array(
			101 => 'integrate_detail.php?module=detailList',
			102 => 'integrate_detail.php?module=summaryList',
		)
   ),
   /*
   10069 => array(
		'url' => 'business_rule_define.php?module=list',
		'list_url' => array(
			101 => 'business_rule_define.php?module=list',
			103 => 'business_rule_define.php?module=info',
			104 => 'business_rule_define.php?module=info',
			105 => 'business_rule_define.php?module=sync',
		)
   ),
   10070 => array(
		'url' => 'business_param_config.php?module=list',
		'list_url' => array(
			101 => 'business_param_config.php?module=list',
			103 => 'business_param_config.php?module=info',
			104 => 'business_param_config.php?module=info',
		)
   ),*/
   10091 => array(
   		'url' => 'kcost.php?module=cash',
   		'list_url' => array(
			101 => 'kcost.php?module=cash',
			102 => 'kcost.php?module=balance',
			103 => 'kcost.php?module=adjust',
			104 => 'kcost.php?module=account',
			109 => 'kcost.php?module=RecoverMoney',
			113 => 'kcost.php?module=toUser',
   		)
   ),
   /*
   10093 => array(
		'url' => 'outstation_ad.php?module=ad_list',
		'list_url' => array(
			101 => 'outstation_ad.php?module=ad_list',
			102 => 'outstation_ad.php?module=ad_add',
			103 => 'outstation_ad.php?module=ad_edit',
			104 => 'outstation_ad.php?module=ad_del',
		)
	),*/
	10097 => array(
		'url' => 'role.php?module=list',
		'list_url' => array(
			101 => 'role.php?module=list',
			102 => 'role.php?module=add',
			103 => 'role.php?module=update',
			104 => 'role.php?module=config',
		)
	),
	/*
	10098 => array(
		'url' => 'user_permission.php?module=list',
		'list_url' => array(
			101 => 'user_permission.php?module=list',
		)
	),
	10111 => array(
		'url'=>'game_props.php',
		'list_url'=>array(
			101 => 'game_props.php',
			102 => 'game_props_info.php',
			103 => 'game_props_info.php',
			104 => 'game_props.php?module=props_del',
			105 => 'game_props.php?module=props_order',
			106 => 'game_props.php?module=props_config',
			107 => 'game_props.php?module=money_bind_config',
		)
	),
	10112 => array(
		'url'=>'function_props.php',
		'list_url'=>array(
			101 => 'function_props.php',
			102 => 'function_props_info.php',
			103 => 'function_props_info.php',
			104 => 'function_props.php?module=props_del',
			105 => 'function_props.php?module=props_order',
			106 => 'function_props.php?module=function_config',
		)
	),
    */
	10009 => array(
		'url'=>'commodity_category.php',
		'list_url'=>array(
			101 => 'commodity_category.php?module=list',
			102 => 'commodity_category.php?module=info'
		)
	),
    /*
    10017 => array(
		'url'=>'commodity.php',
		'list_url'=>array(
			101 => 'commodity.php?module=list',
			102 => 'commodity.php?module=info'
		)
	),*/
    10190 => array(
		'url'=>'commodity_scheme.php',
		'list_url'=>array(
			101 => 'commodity_scheme.php?module=list',
			102 => 'commodity_scheme.php?module=info'
		)
	),
	10134 => array(
		'url'=>'join.php?module=list',
		'list_url'=>array(
			101 => 'join.php?module=list',
			102 => 'join.php?module=join_info',
			103 => 'join.php?module=join_update',
			106 => 'join.php?module=add',
		)
	),
	10138 => array(
		'url'=>'interact_info.php?module=getfrozenlist',
		'list_url'=>array(
			101 => 'interact_info.php?module=getfrozenlist',
			102 => 'interact_info.php?module=freefrozen',
		)
	),
	10144 => array(
		'url'=>'create_account.php',
		'list_url'=>array(
			101 => 'create_account.php?module=single_create',
			102 => 'create_account.php?module=range_create',
		)
	),
	10146 => array(
		'url' => 'openvip.php',
		'list_url' => array(
			101 => 'openvip.php?module=open',
		)
	),
	10147 => array(
		'url' => 'config_amount.php?module=list',
		'list_url' => array(
			101 => 'config_amount.php?module=info',
			102 => 'config_amount.php?module=list',
			103 => 'config_amount.php?module=info',
			104 => 'config_amount.php?module=delete',
		)
	),
	/*
	10148 => array(
		'url' => 'open_super_admin.php?module=list',
		'list_url' => array(
			101 => 'open_super_admin.php?module=info',
			102 => 'open_super_admin.php?module=list',
			103 => 'open_super_admin.php?module=cancel',
		)
	),*/
	10152 => array(
		'url' => 'count.php?module=all_user_total',
		'list_url' => array(
			101 => 'count.php?module=all_user_total',
			102 => 'count.php?module=all_user_history',
			103 => 'count.php?module=rooms_user_total',
			104 => 'count.php?module=rooms_user_history',
			105 => 'count.php?module=rooms_count_day',
			106 => 'count.php?module=rooms_user_info',
		)
	),
	10165 => array(
		'url' => 'feedback.php?module=list',
		'list_url' => array(
			101 => 'feedback.php?module=list',
			102 => 'feedback.php?module=view',
			103 => 'feedback.php?module=dispose',
		)
	),
	10216 => array(
		'url' => 'channel_package.php',
		'list_url' => array(
			101 => 'channel_package.php?module=list',
			102 => 'channel_package.php?module=info',
			103 => 'channel_package.php?module=info',
			104 => 'channel_package.php?module=stockList',
		)
	),
	10217 => array(
		'url' => 'agent_adjust_account.php',
		'list_url' => array(
			101 => 'agent_adjust_account.php?module=safeguard',
			102 => 'agent_adjust_account.php?module=in',
			103 => 'agent_adjust_account.php?module=out',
		)
	),
	/*
	10218 => array(
		'url' => 'channel_agent_account.php',
		'list_url' => array(
			101 => 'channel_agent_account.php?module=accountTradeDetail',
			102 => 'channel_agent_account.php?module=packageTradeCollect',
			103 => 'channel_agent_account.php?module=packageHistorySellDetail',
		)
	),*/
	10224 => array(
		'url' => 'help.php?module=category',
		'list_url' => array(
			101 => 'help.php?module=category',
			102 => 'help.php?module=category_edit',
			103 => 'help.php?module=category_edit',
			104 => 'help.php?module=category_del',
			105 => 'help.php?module=article',
			106 => 'help.php?module=article_edit',
			107 => 'help.php?module=article_edit',
			108 => 'help.php?module=article_del',
		)
	),
	/*
	10227 => array(
		'url' => 'channel_income.php?module=channel_tax_detail',
		'list_url' => array(
			101 => 'channel_income.php?module=channel_tax_detail',
			102 => 'channel_income.php?module=channel_typetax_count&Type=1',
			103 => 'channel_income.php?module=channel_usertax_count&Type=1',
			104 => 'channel_income.php?module=channel_usertax_balance',
			105 => 'channel_income.php?module=channel_user_exchange',
		)
	),
	10188 => array(
		'url' => 'channel_income.php?module=rmb_details',
		'list_url' => array(
			101 => 'channel_income.php?module=rmb_details',
			102 => 'channel_income.php?module=rmb_balance',
			103 => 'channel_income.php?module=depositCheck',
			104 => 'channel_income.php?module=depositApply',
			105 => 'channel_income.php?module=depositVerify',
			106 => 'channel_income.php?module=depositVerify',
			107 => 'channel_income.php?module=depositVerify',
		)
	),*/
	10231 => array(
		'url' => 'friend_link.php',
		'list_url' => array(
			101 => 'friend_link.php?module=list',
			102 => 'friend_link.php?module=add',
			103 => 'friend_link.php?module=update',
			104 => 'friend_link.php?module=delete',
		)
	),
	10235 => array(
		'url' => 'kwealth.php?module=rmb_adjust',
		'list_url' => array(
			101 => 'kwealth.php?module=rmb_adjust',
			102 => 'kwealth.php?module=rmb_adjust',
		)
	),
	10236 => array(
		'url' => 'interact_info.php?module=ipCount',
		'list_url' => array(
			101 => 'interact_info.php?module=ipCount',
		)
	),
	/*
	10253 => array(
		'url' => 'service.php?module=list',
		'list_url' => array(
				101 => 'service.php?module=list',
				102 => 'service.php?module=add',
				103 => 'service.php?module=edit'
		)
	),*/
	10254 => array(
		'url' => 'ad_detail.php?module=list',
		'list_url' => array(
			101 => 'ad_detail.php?module=list',
		)
	),
	10256 => array( 
		'url'=>'voucher.php?module=user_balance',
		'list_url'=>array(
			101=>'voucher.php?module=user_balance',
			102=>'voucher.php?module=account_balance',
			103=>'voucher.php?module=day_total',
			104=>'voucher.php?module=user_detail',
			105=>'voucher.php?module=business_day_total',
			106=>'voucher.php?module=parent_total',
			107=>'voucher.php?module=deposit_pay',
		)
	),
	10274 => array(
		'url' => 'help_email.php',
		'list_url' => array(
			101 => 'help_email.php?module=list',
			102 => 'help_email.php?module=add',
		)
	),
	10276 => array(
		'url' => 'medal.php?module=medaltype',
		'list_url' => array(
			101 => 'medal.php?module=medaltype',
			102 => 'medal.php?module=medaltype_edit',
			103 => 'medal.php?module=medallist',
			104 => 'medal.php?module=medallist_edit',
		)
	),
	10313 => array(
		'url' => 'start_config.php'
	),
	10275 => array(
		'url' => 'vdiancharge.php',
		'list_url' => array(
			105 => 'vdiancharge.php?module=charge',
			103 => 'vdiancharge.php?module=pcharge',
			101 => 'vdiancharge.php?module=ucharge',
			102 => 'vdiancharge.php?module=mcharge',
		)
	),
	10333 => array(
		'url' => 'group_ranking_setting.php',
		'list_url' => array(
			101 => 'group_ranking_setting.php?module=list',
			102 => 'group_ranking_setting.php?module=info',
			103 => 'group_ranking_setting.php?module=del'
		)
	),
	10334 => array(
		'url' => 'fund_system.php?database=kkyoo_kmoney',
		'list_url' => array(
			101 => 'fund_system.php?database=kkyoo_kmoney',
		)
	),
	10335 => array(
		'url' => 'fund_system.php?database=kkyoo_voucher',
		'list_url' => array(
			101 => 'fund_system.php?database=kkyoo_voucher',
		)
	),
	10731 => array(
		'url' => 'fund_system.php?database=kkyoo_voucher_plat',
		'list_url' => array(
			101 => 'fund_system.php?database=kkyoo_voucher_plat',
		)
	),
	10336 => array(
		'url' => 'fund_system.php?database=kkyoo_tax',
		'list_url' => array(
			101 => 'fund_system.php?database=kkyoo_tax',
		)
	),
	10732 => array(
		'url' => 'fund_system.php?database=kkyoo_tax_plat',
		'list_url' => array(
			101 => 'fund_system.php?database=kkyoo_tax_plat',
		)
	),
	10339 => array(
		'url' => 'group_foot.php',
		'list_url' => array(
			101 => 'group_foot.php?module=list',
			102 => 'group_foot.php?module=info',
			103 => 'group_foot.php?module=sync_info',
		)
	),
	10005 => array(
		'url' => 'group_style.php?module=list',
		'list_url' => array(
			101 => 'group_style.php?module=list',
			102 => 'group_style.php?module=info',
			103 => 'group_style_setting.php?module=list',
			104 => 'group_style_setting.php?module=info',
		)
	),
	/*
	10340 => array(
		'url' => 'role.php?module=cate_list',
		'list_url' => array(
			101 => 'role.php?module=list',
			102 => 'role.php?module=info',
			103 => 'role.php?module=del',
			104 => 'role.php?module=cate_list',
			105 => 'role.php?module=cate_update',
			106 => 'user_permission.php',
			107 => 'user_permission.php',
		)
	),
	10341 => array(
		'url' => 'user_permission.php',
		'list_url' => array(
			101 => 'user_permission.php',
			102 => 'user_permission.php'
		)
	),*/
	10342 => array(
		'url' => 'role_package.php?module=package_list',
		'list_url' => array(
			101 => 'role_package.php?module=package_list',
			102 => 'role_package.php?module=package_info',
			103 => 'role_package.php?module=package_del',
		)
	),
	10426 => array(
		'url' => 'finance_margin.php',
		'list_url' => array(
			101 => 'finance_margin.php?module=finance_margin',
			102 => 'finance_margin.php?module=finance_group_margin',
			103 => 'finance_margin.php?module=finance_abnormal_running',
			104 => 'finance_margin.php?module=finance_manage'
		)
	),
	10443 => array(
		'url' => 'stamp.php',
		'list_url' => array(
			101 => 'stamp.php?module=list',
			102 => 'stamp.php?module=info',
		)
	),
	10444 => array(
		'url' => 'expression.php',
		'list_url' => array(
			101 => 'expression.php?module=list',
			102 => 'expression.php?module=info',
		)
	),
	10470 => array(
		'url' => 'data_export.php',
		'list_url' => array(
			101 => 'data_export.php'
		)		
	),
	10575 => array(
		'url' => 'key.php',
		'list_url' => array(
			101 => 'key.php?module=key_list',
			102 => 'key.php?module=key_update',
			103 => 'key.php?module=compose_list',
			104 => 'key.php?module=compose_update'
		)
	),
	10641 => array(
		'url' => 'intentional_customer.php',
		'list_url' => array(
			101 => 'intentional_customer.php?module=list'
 		)
	),
	/*10731 => array(
		'url' => 'issue_tracking.php?module=issue_list',
		'list_url' => array(
			101 => 'issue_tracking.php?module=issue_list',
			102 => 'issue_tracking.php?module=issue_save',
 		)
	),
	10732 => array(
		'url' => 'issue_tracking.php?module=collection',
		'list_url' => array(
			101 => 'issue_tracking.php?module=collection',
 		)
	),
	10733 => array(
		'url' => 'issue_tracking.php?module=initiate_type_list',
		'list_url' => array(
			101 => 'issue_tracking.php?module=initiate_type_save',
			102 => 'issue_tracking.php?module=initiate_type_list',
			103 => 'issue_tracking.php?module=initiate_type_del',
 		)
	),
	10734 => array(
		'url' => 'issue_tracking.php?module=level_one_list',
		'list_url' => array(
			101 => 'issue_tracking.php?module=level_one_list',
			102 => 'issue_tracking.php?module=level_one_save',
			103 => 'issue_tracking.php?module=level_one_del',
 		)
	),
	*/10648 => array(
        'url' => 'template.php',
        'list_url' => array(
            101 => 'template.php?module=tpl_list',
            
            138 => 'role.php?module=list',
			142 => 'role.php?module=info',
			145 => 'role.php?module=del',
			150 => 'role.php?module=cate_list',
			147 => 'role.php?module=cate_update',
			152 => 'user_permission.php',
			153 => 'user_permission.php',
            
			141 => 'business_rule_define.php?module=list',
			146 => 'business_rule_define.php?module=info',
			149 => 'business_rule_define.php?module=info',
			151 => 'business_rule_define.php?module=sync',
            
			139 => 'business_param_config.php?module=list',
			144 => 'business_param_config.php?module=info',
			148 => 'business_param_config.php?module=info',
            
            112 =>'props_manage.php?module=props_list',
    		114 =>'props_info.php',
    		117 =>'props_info.php',
    		124 =>'props_manage.php?module=props_del',
    		125 =>'props_manage.php?module=props_order',
        	131 =>'props_manage.php?module=cate_list',
        	133 =>'props_manage.php?module=add_cate',
            
            140 => 'commodity.php?module=list',
			143 => 'commodity.php?module=info',
            
            111 => 'game_props.php',
			115 => 'game_props_info.php',
			120 => 'game_props_info.php',
			121 => 'game_props.php?module=props_del',
			127 => 'game_props.php?module=props_order',
			132 => 'game_props.php?module=props_config',
            
            110 => 'function_props.php',
			116 => 'function_props_info.php',
			119 => 'function_props_info.php',
			123 => 'function_props.php?module=props_del',
			126 => 'function_props.php?module=props_order',
			129 => 'function_props.php?module=function_config',
        )
    ),
);
