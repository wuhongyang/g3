<?php
require_once('_bank.class.php');
class chinabank extends _bank {

	//发送参数
	private $gateway = 'https://pay3.chinabank.com.cn/PayGate'; //支付接口
	private $v_moneytype = 'CNY'; //支付币种

	private $v_mid; // 商户号
	private $v_url; //返回url
	private $s_url; // 服务端调用url
	private $v_amount; //支付金额
	private $v_md5info; //md5加密后的字符串
	private $remark1; //备注字段1
	private $remark2; //备注字段2
	private $v_oid; //定单号
	private $key; //密钥
	private $encoding; //字符编码格式

	//接收参数
	private $v_pmode; // 支付方式（字符串）
	private $v_pstatus; // 支付状态 ：20（支付成功）；30（支付失败）
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
		$this->v_amount = floatval($amount);
	}

	/*设置订单号*/
	public function trade_id($trade_id = '') {
		if (empty ($trade_id)) {
			$this->v_oid = date('Ymd', time()) . "-" . $this->v_mid . "-" . date('His', time());
		} else {
			$this->v_oid = $trade_id;
		}
	}

	/*设置备注*/
	public function remark($remark1 = '', $remark2 = '') {
		$this->remark1 = $remark1;
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
			<p>正在为您跳转到网银在线页面，请稍候...</p>
			<form name="BANK_FORM" method="post" action="' . $this->gateway . '?encoding=' . $this->encoding . '">
			<input type="hidden" name="v_md5info" value="' . $this->v_md5info . '">
			<input type="hidden" name="v_mid" value="' . $this->v_mid . '">
			<input type="hidden" name="v_oid" value="' . $this->v_oid . '">
			<input type="hidden" name="v_amount" value="' . $this->v_amount . '">
			<input type="hidden" name="v_moneytype"  value="' . $this->v_moneytype . '">
			<input type="hidden" name="v_url" value="' . $this->v_url . '">
			<input type="hidden" name="remark1" value="' . $this->remark1 . $this->remark2 . '">
			<input type="hidden" name="remark2" value="[url:=' . $this->s_url . ']">
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
		$this->v_oid = trim($_POST['v_oid']);
		$this->v_pmode = trim($_POST['v_pmode']);
		$this->v_pstatus = trim($_POST['v_pstatus']);
		$this->v_pstring = trim($_POST['v_pstring']);
		$this->v_amount = trim($_POST['v_amount']);
		$this->v_moneytype = trim($_POST['v_moneytype']);
		$this->remark1 = trim($_POST['remark1']);
		$this->remark2 = trim($_POST['remark2']);
		$this->v_md5str = trim($_POST['v_md5str']);
	}
	
	/*判断返回是否有效信息*/
	public function check_receive() {
		if ($this->v_md5str == $this->incept_verify()) {
			return true;
		} else {
			return false;
		}
	}
	
	/*判断返回是否提交成功*/
	public function check_pay() {
		if ($this->v_pstatus == '20') {
			return true;
		} else {
			return false;
		}
	}
	
	/*返回数据*/
	public function return_data() {
		$array = array(
			'v_oid' => $this->v_oid,
			'v_amount' => $this->v_amount
		);
		return $array;
	}

	/*生成发送校验码*/
	private function send_verify() {
		$text = $this->v_amount . $this->v_moneytype . $this->v_oid . $this->v_mid . $this->v_url . $this->key;
		$this->v_md5info = strtoupper(trim(md5($text)));
	}

	/*生成接收校验码*/
	private function incept_verify() {
		$text = $this->v_oid . $this->v_pstatus . $this->v_amount . $this->v_moneytype . $this->key;
		return strtoupper(trim(md5($text)));
	}
}
