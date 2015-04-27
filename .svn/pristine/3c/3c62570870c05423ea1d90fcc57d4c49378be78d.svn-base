<?php
class bank {
	public static function payment($engine,$type='vbao',$notOpenAgent=0,$groupid=0, $partner_id=0, $partner_key='') {
		$GroupData = domain::main()->GroupData();
		$GroupData['EXT'] = json_decode($GroupData['EXT'], true);
		
		if($engine == 'ALIPAY') {
			require_once  __ROOT__.'/library/bank_pack/alipay.class.php';
			$partner = $GroupData['EXT']['alipay_id']['value'];//合作伙伴ID
			$security_code = $GroupData['EXT']['alipay_key']['value'];//安全检验码
			$seller_email = rawurldecode($GroupData['EXT']['alipay_email']['value']);//卖家支付宝帐户
			$alipay_config = array(
				'partner' => $partner,  //合作伙伴ID
				'security_code' => $security_code, //安全检验码
				'seller_email' => $seller_email, //卖家支付宝帐户
				'_input_charset' => 'utf-8', //字符编码格式 目前支持 GBK 或 utf-8
				'transport'	=> 'http', //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
				'sign_type' => 'MD5', //加密方式 系统默认(不要修改)
				'notify_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/alipay/notify_url.php?notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid, //交易过程中服务器通知的页面 要用 http://格式的完整路径
				'return_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/alipay/return_url.php?notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid,//付完款后跳转的页面 要用 http://格式的完整路径
				'show_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/index.php' //你网站商品的展示地址
			);
			$instance = new alipay($alipay_config);
		}
		elseif($engine == 'TENPAY') {
			require_once  __ROOT__.'/library/bank_pack/tenpay.class.php';
			$partner_id = $GroupData['EXT']['tenpay_id']['value'];
			$partner_key = $GroupData['EXT']['tenpay_key']['value'];
			$tenpay_config = array(
				'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //财付通风险防范参数
// 				'bargainor_id' => '1900000113',  //合作伙伴ID
//				'key' => 'e82573dc7e6136ba414f2e2affbe39fa', //安全检验码
				'bargainor_id' => $partner_id,  //合作伙伴ID
				'key' => $partner_key, //安全检验码
				'transaction_id' => '1214150101'.date('YmdHis').rand(1000,9999), //财付通交易单号
				'cs' => 'utf-8', //字符编码格式 目前支持 gbk 或 utf-8
				'sign' => 'MD5', //加密方式 系统默认(不要修改)
				'return_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/tenpay/return_url.php?type='.$type.'&notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid, //付完款后跳转的页面 要用 http://格式的完整路径
			);
			$instance = new tenpay($tenpay_config);
		}
		elseif($engine == 'CHINABANK') {
			require_once __ROOT__.'/library/bank_pack/chinabank.class.php';
			$partner_id = $GroupData['EXT']['chinabank_id']['value'];
			$partner_key = $GroupData['EXT']['chinabank_key']['value'];
			$chinabank_config = array(
// 				'v_mid' => '22509012',   //合作伙伴ID
// 				'key' => '3d9b9489311fec375d2808ffe9034382', //安全检验码
				'v_mid' => $partner_id,   //合作伙伴ID
				'key' => $partner_key, //安全检验码
				'encoding' => 'utf-8', //字符编码格式 目前支持 gbk 或 utf-8
				's_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/chinabank/AutoReceive.php?type='.$type.'&notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid, //付完款后服务端调用页面 要用 http://格式的完整路径
				'v_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/chinabank/Receive.php?type='.$type.'&notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid //付完款后跳转的页面 要用 http://格式的完整路径
			);
			$instance = new chinabank($chinabank_config);
		}
		elseif($engine == 'BAOFOO'){
			require_once __ROOT__.'/library/bank_pack/phonebank.class.php';
			$partner_id = $GroupData['EXT']['baofoo_id']['value'];
			$partner_key = $GroupData['EXT']['baofoo_key']['value'];
			$phonebank_config = array(
// 				'_MerchantID' => '116507',   //合作伙伴ID
// 				'_Md5Key' => 'hkgx28n53uyjhhrf', //安全检验码
				'_MerchantID' => $partner_id,   //合作伙伴ID
				'_Md5Key' => $partner_key, //安全检验码
				'_Merchant_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/phonebank/merchant_url.php?type='.$type.'&notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid, //付完款后服务端调用页面 要用 http://格式的完整路径
				'_Return_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shop/phonebank/return_url.php?type='.$type.'&notOpenAgent='.$notOpenAgent.'&GroupId='.$groupid //付完款后跳转的页面 要用 http://格式的完整路径
			);
			$instance = new phonebank($phonebank_config);
		}
		return $instance;
	}
}

