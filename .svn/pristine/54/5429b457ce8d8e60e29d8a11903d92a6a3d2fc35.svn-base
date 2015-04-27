<?php

/**
 *   系统信息
 *   文件: systemindex.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class systemindex{

	public function getMessage()
	{
		//获取用户登录信息
		$param = array(
			'extparam' => array(
					'Tag'	=>  'GetLogin',
				)
		);
		$user = httpPOST(SSO_API_PATH, $param, true);

		//获取用户数量
		$param = array(
			'extparam' => array(
					'Tag'	=>  'CountUser',
				)
		);
		$count = httpPOST(SSO_API_PATH, $param, true);

		//获取ip地址
		$param = array(
			'extparam' => array(
					'Tag'	=>  'Address',
					'ip'    =>  get_ip(),
				)
		);
		$address = httpPOST(REGION_API_PATH,$param,false);
		$return = array(
			'user'   => $user,
			'count'  => $count,
			'ip'     => get_ip(),
			'address'=> json_decode($address,true),
		);
		return $return;
	}
}
