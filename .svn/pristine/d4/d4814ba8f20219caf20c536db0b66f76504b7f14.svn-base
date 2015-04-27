<?php
require_once('_bank.class.php');
class tenpay extends _bank {

	//发送
	private $gateUrl = 'https://gw.tenpay.com/gateway/pay.htm'; //网关url地址
	private $cmdno = '1'; //任务代码
	private $fee_type = '1'; //货币类型
	private $bank_type = 'DEFAULT'; //银行编码
	
	private $bargainor_id; //商户号
	private $transaction_id; //财付通交易单号
	private $sp_billno; //商家订单号
	private $total_fee; //商品价格，以分为单位
	private $return_url; //返回url
	private $attach; //自定义参数
	private $spbill_create_ip; //用户ip
	private $desc; //商品名称
	private $sign; //摘要,MD5
	private $cs; //字符集编码
	private $key; //密钥

	//接收
	private $pay_result; //支付结果
	
	/*初始化配置*/
	public function __construct($tenpay_config) {
		foreach ((array) $tenpay_config as $key => $val) {
			$this-> $key = $val;
		}
	}

	/*设置支付金额*/
	public function total_fee($total_fee = 0.01) {
		$this->total_fee = floatval($total_fee) * 100;
	}

	/*设置订单号*/
	public function trade_id($sp_billno = '') {
		if (empty ($sp_billno)) {
			$this->sp_billno = date('Ymd', time()) . "-" . $this->bargainor_id . "-" . date('His', time());
		} else {
			$this->sp_billno = $sp_billno;
		}
	}

	/*设置备注*/
	public function remark($subject = '商品名称', $body = '商品描述') {
		$this->desc = $subject . $body;
	}
	
	public function pay_id($id=0){
		if($id > 0){
			$this->_PayID = $id;
		}
	}

	/*提交支付请求*/
	public function pay_send() {
		$parameters = array (
			'partner' => $this->bargainor_id,
			'out_trade_no' => $this->sp_billno,
			'total_fee' => $this->total_fee,
			'return_url' => $this->return_url,
			'notify_url' => $this->return_url,
			'body' => $this->desc,
			'bank_type' => $this->bank_type,
			'spbill_create_ip' => $this->spbill_create_ip,
			'fee_type' => $this->fee_type,
			'subject' => 'test',
			'sign_type' => "MD5",
			'service_version' => "1.0",
			'input_charset' => "utf-8",
			'sign_key_index' => "1",
			'trans_type' => "1",
		);
		$this->parameters = $parameters;
		$this->createSign();
		$reqPar = '';
		$parameters['sign'] = $this->sign;
		ksort($parameters);
		foreach ($parameters as $k => $v) {
			$reqPar .= $k . '=' . urlencode($v) . '&';
		}
		//去掉最后一个&
		$reqPar = substr($reqPar, 0, strlen($reqPar) - 1);
		$requestURL = $this->gateUrl . '?' . $reqPar;
		$result = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>正在跳转,请稍候...</title>
			<style type="text/css">
				body {margin:0;padding:0;}
				p {position:absolute;left:50%;top:50%;width:300px;height:30px;margin:-35px 0 0 -160px;padding:20px;font:bold 14px/30px "宋体", Arial;background:#f9fafc url(images/loading.gif) no-repeat 20px 26px;text-indent:22px;border:1px solid #c5d0dc;}
			</style>
			</head>
			<body>
			<p>正在为您跳转到财付通页面，请稍候...</p>
			<script type="text/javascript">document.location.replace("'.$requestURL.'");</script>
			</body>
			</html>
		';
		$result = trim($result);
		return $result;
	}

	/*提交返回处理*/
	public function pay_receive() {
		/* GET */
		foreach ($_GET as $k => $v){
			$this->pay_result[$k] = $v;
		}
		/* POST */
		foreach($_POST as $k => $v){
			$this->pay_result[$k] = $v;
		}
	}

	/*判断返回是否有效信息*/
	public function check_receive() {
		if ($this->isTenpaySign()) {
			return true;
		} else {
			return false;
		}
	}

	/*判断返回是否提交成功*/
	public function check_pay() {
		if ($this->pay_result['trade_state'] == '0') {
			return true;
		} else {
			return false;
		}
	}
	
	/*返回数据*/
	public function return_data() {
		/*$array = array (
			'sp_billno' => $this->sp_billno,
			'total_fee' => $this->total_fee
		);
		*/
		$array = array(
			'sp_billno' => $this->pay_result['out_trade_no'],
			'total_fee' => $this->pay_result['total_fee'],
		);
		return $array;
	}

	/**
	*显示处理结果。
	*@param $show_url 显示处理结果的url地址,绝对url地址的形式(http://www.xxx.com/xxx.php)。
	*/
	public function doShow($show_url) {
		return parent::doShowTenpay($show_url);
	}

	/**
	*@Override
	*创建签名
	*/
	private function createSign() {
		$signPars = "";
		ksort($this->parameters);
		foreach($this->parameters as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->key;
		$sign = strtolower(md5($signPars));		

		$this->sign = $sign;
	}

	/**
	*@Override
	*/
	private function isTenpaySign() {
		$arr = $this->pay_result;
		//组织签名
		foreach($arr as $k => $v) {
			if("sign" != $k && "type" != $k && "notOpenAgent" != $k && "GroupId" != $k && "extparam" !=$k && "param" !=$k && "" !== $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=".$this->key;
		$sign = strtolower(md5($signPars));
		$tenpaySign = strtolower($arr['sign']);
		return $sign == $tenpaySign;
	}
}
