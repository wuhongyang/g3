<?php
require_once('_bank.class.php');
class phonebank extends _bank {

	//发送参数
	private $gateway = 'http://paygate.baofoo.com/PayReceive/payindex.aspx'; //支付接口
	private $_NoticeType = 1;
	private $_Md5Key = 'gamzkmjdrbpbct9v';
	
	private $_MerchantID; // 商户号
	private $v_url; //返回url
	private $s_url; // 服务端调用url
	private $OrderMoney; //支付金额
	private $_Md5Sign; //md5加密后的字符串
	private $_PayID; //md5加密后的字符串
	private $_AdditionalInfo; //md5加密后的字符串
	private $_TransID; //定单号
	private $key; //密钥

	//接收参数
	private $v_pmode; // 支付方式（字符串）
	private $v_pstatus; // 支付状态 ：1（支付成功）；0（支付失败）
	private $v_pstring; // 支付结果信息
	private $v_md5str; //返回后的md5
	
	/*初始化配置*/
	public function __construct($chinabank_config) {
		foreach ((array) $chinabank_config as $key => $val) {
			$this-> $key = $val;
		}
	}

	/*设置支付金额*/
	public function total_fee($amount = 0.01) {
		$this->OrderMoney = $amount*100;
	}

	/*设置订单号*/
	public function trade_id($trade_id = '') {
		if (empty ($trade_id)) {
			$this->_TransID = date('Ymd', time()) . "-" . $this->_MerchantID . "-" . date('His', time());
		} else {
			$this->_TransID = $trade_id;
		}
		$this->_TradeDate = date('Ymdhis');
	}

	/*设置备注*/
	public function remark($remark = '', $remark2 = '') {
		$this->_AdditionalInfo = $remark;
		$this->remark2 = $remark2;
	}
	
	public function pay_id($id=0){
		if($id > 0){
			$this->_PayID = $id;
		}
	}
	
	/*提交支付请求*/
	public function pay_send() {
		$this->send_verify();
		$result = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>正在跳转,请稍候...</title>
			<style type="text/css">
				body {margin:0;padding:0;}
				p {position:absolute;left:50%;top:50%;width:310px;height:30px;margin:-35px 0 0 -160px;padding:20px;font:bold 14px/30px "宋体", Arial;background:#f9fafc url(images/loading.gif) no-repeat 20px 26px;text-indent:22px;border:1px solid #c5d0dc;}
			</style>
			</head>
			<body>
			<p>正在为您跳转到宝付页面，请稍候...</p>
			<form name="BANK_FORM" method="post" action="' . $this->gateway .'">
			<input type="hidden" name="MerchantID" value="' . $this->_MerchantID .'" />
			<input type="hidden" name="PayID" value="' . $this->_PayID .'" />
			<input type="hidden" name="TradeDate" value="' . $this->_TradeDate .'" />
			<input type="hidden" name="TransID" value="' . $this->_TransID .'" />
			<input type="hidden" name="OrderMoney" value="'.$this->OrderMoney.'" />
			<input type="hidden" name="AdditionalInfo" value="' . $this->_AdditionalInfo .'" />
			<input type="hidden" name="Merchant_url" value="' . $this->_Merchant_url .'" />
			<input type="hidden" name="Return_url" value="' . $this->_Return_url .'" />
			<input type="hidden" name="Md5Sign" value="' . $this->_Md5Sign .'" />
			<input type="hidden" name="NoticeType" value="' . $this->_NoticeType .'" />
			
			<input type="hidden" name="ProductName" value="" />
			<input type="hidden" name="Amount" value="" />
			<input type="hidden" name="ProductLogo" value="" />
			<input type="hidden" name="Username" value="" />
			<input type="hidden" name="Email" value="" />
			<input type="hidden" name="Mobile" value="" />
			</form>
			<script type="text/javascript">document.BANK_FORM.submit();</script>
			</body>
			</html>
		';
		$result = trim($result);
		return $result;
	}

	/*提交返回处理*/
	public function pay_receive() {
		$this->_MerchantID = trim($_POST['MerchantID']) ? trim($_POST['MerchantID']) : trim($_GET['MerchantID']);
		$this->_TransID = trim($_POST['TransID']) ? trim($_POST['TransID']) : trim($_GET['TransID']);
		$this->v_pstatus = trim($_POST['Result']) ? trim($_POST['Result']) : trim($_GET['Result']);
		$this->_resultDesc = trim($_POST['resultDesc']) ? trim($_POST['resultDesc']) : trim($_GET['resultDesc']);
		$this->v_amount = trim($_POST['factMoney']) ? trim($_POST['factMoney']) : trim($_GET['factMoney']);
		$this->_additionalInfo = trim($_POST['additionalInfo']) ? trim($_POST['additionalInfo']) : trim($_GET['additionalInfo']);
		$this->_SuccTime = trim($_POST['SuccTime']) ? trim($_POST['SuccTime']) : trim($_GET['SuccTime']);
		$this->_Md5Sign = trim($_POST['Md5Sign']) ? trim($_POST['Md5Sign']) : trim($_GET['Md5Sign']);
	}
	
	/*判断返回是否有效信息*/
	public function check_receive() {
		if ($this->_Md5Sign == $this->incept_verify()) {
			return true;
		} else {
			return false;
		}
	}
	
	/*判断返回是否提交成功*/
	public function check_pay() {
		if ($this->v_pstatus == '1') {
			return true;
		} else {
			return false;
		}
	}
	
	/*返回数据*/
	public function return_data() {
		$array = array(
			'v_oid' => $this->_TransID,
			'v_amount' => $this->v_amount
		);
		return $array;
	}

	/*生成发送校验码*/
	private function send_verify() {
		$text = $this->_MerchantID . $this->_PayID .$this->_TradeDate .  $this->_TransID . $this->OrderMoney . $this->_Merchant_url . $this->_Return_url. $this->_NoticeType .$this->_Md5Key;
		$this->_Md5Sign = trim(md5($text));
	}

	/*生成接收校验码*/
	private function incept_verify() {
		$text = $this->_MerchantID . $this->_TransID . $this->v_pstatus . $this->_resultDesc . $this->v_amount.$this->_additionalInfo.$this->_SuccTime.$this->_Md5Key;
		return trim(md5($text));
	}
}
