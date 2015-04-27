<?php
require_once('_bank.class.php');
class alipay extends _bank {

	//发送参数
	private $form_gateway = 'https://www.alipay.com/cooperate/gateway.do?'; //表单网关地址
	private $http_gateway = 'http://notify.alipay.com/trade/notify_query.do?'; //http网关地址
	private $https_gateway = 'https://www.alipay.com/cooperate/gateway.do?'; //https网关地址
	private $service = 'create_direct_pay_by_user'; //交易类型
	private $payment_type = '1'; //默认为1,不需要修改
	private $parameter; //全部需要传递的参数
	private $mysign; //签名,md5加密后的字符串
	private $partner; //合作商户号，必填
	private $return_url; //同步返回，必填
	private $notify_url; //异步返回，必填
	private $subject; //商品名称，必填
	private $body; //商品描述，必填
	private $total_fee; //商品单价，必填（价格不能为0）
	private $out_trade_no; //定单号，必填
	private $seller_email; //卖家支付宝帐户，必填
	private $security_code; //安全校验码，必填
	private $_input_charset; //字符集
	private $transport; //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	private $show_url; //商品相关网站

	//接收参数
	private $trade_status; //获取支付宝反馈过来的状态,根据不同的状态来更新数据库 WAIT_BUYER_PAY(表示等待买家付款);WAIT_SELLER_SEND_GOODS(表示买家付款成功,等待卖家发货);WAIT_BUYER_CONFIRM_GOODS(卖家已经发货等待买家确认);TRADE_FINISHED(表示交易已经成功结束)

	function __construct($alipay_config) {
		foreach ((array) $alipay_config as $key => $val) {
			$this-> $key = $val;
		}
	}

	/*设置支付金额*/
	public function total_fee($total_fee = 0.01) {
		$this->total_fee = floatval($total_fee);
	}

	/*设置订单号*/
	public function trade_id($out_trade_no = '') {
		if (empty ($out_trade_no)) {
			$this->out_trade_no = date('Ymd', time()) . "-" . $this->partner . "-" . date('His', time());
		} else {
			$this->out_trade_no = $out_trade_no;
		}
	}

	/*设置备注*/
	public function remark($subject = '商品名称', $body = '商品描述') {
		$this->subject = $subject;
		$this->body = $body;
	}
	
	public function pay_id($id=0){
		
	}

	/*提交支付请求*/
	public function pay_send() {
		$this->build_mysign();
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
			<p>正在为您跳转到支付宝页面，请稍候...</p>
			<form name="BANK_FORM" method="post" action="' . $this->form_gateway . '_input_charset=' . $this->_input_charset . '">
			<input type="hidden" name="service" value="' . $this->service . '">
			<input type="hidden" name="partner" value="' . $this->partner . '">
			<input type="hidden" name="return_url" value="' . $this->return_url . '">
			<input type="hidden" name="notify_url" value="' . $this->notify_url . '">
			<input type="hidden" name="subject" value="' . $this->subject . '">
			<input type="hidden" name="body" value="' . $this->body . '">
			<input type="hidden" name="out_trade_no" value="' . $this->out_trade_no . '">
			<input type="hidden" name="total_fee" value="' . $this->total_fee . '">
			<input type="hidden" name="payment_type" value="1">
			<input type="hidden" name="show_url" value="' . $this->show_url . '">
			<input type="hidden" name="seller_email" value="' . $this->seller_email . '">
			<input type="hidden" name="sign" value="' . $this->mysign . '">
			<input type="hidden" name="sign_type" value="' . $this->sign_type . '">
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
		$this->out_trade_no = trim($_POST['out_trade_no']) ? trim($_POST['out_trade_no']) : trim($_GET['out_trade_no']);
		$this->total_fee = trim($_POST['total_fee']) ? trim($_POST['total_fee']) : trim($_GET['total_fee']);
		$this->trade_status = trim($_POST['trade_status']);
	}
	
	/*判断返回是否有效信息*/
	public function check_receive() {
		if($_POST){//如果是notice通知时
			return $this->check_notify_receive();
		}else{
			return $this->check_return_receive();
		}
	}

	/*判断返回是否提交成功*/
	public function check_pay() {
		if ($this->trade_status == 'TRADE_SUCCESS') {
			return true;
		} else {
			return false;
		}
	}
	
	/*返回数据*/
	public function return_data() {
		$array = array(
			'out_trade_no' => $this->out_trade_no,
			'total_fee' => $this->total_fee
		);
		return $array;
	}

	/*判断返回是否有效信息*/
	public function check_return_receive() {
		if ($this->return_verify()) {
			return true;
		} else {
			return false;
		}
	}
	
	/*判断返回是否有效信息*/
	public function check_notify_receive() {
		if ($this->notify_verify()) {
			return true;
		} else {
			return false;
		}
	}

	//构造build_mysign
	private function build_mysign() {
		$parameter = array (
			'service' => $this->service, //交易类型
			'partner' => $this->partner, //合作商户号
			'return_url' => $this->return_url, //同步返回
			'notify_url' => $this->notify_url, //异步返回
			'_input_charset' => $this->_input_charset, //字符集，默认为GBK
			'subject' => $this->subject, //商品名称，必填
			'body' => $this->body, //商品描述，必填
			'out_trade_no' => $this->out_trade_no, //商品外部交易号，必填（保证唯一性）
			'total_fee' => $this->total_fee, //商品单价，必填（价格不能为0）
			'payment_type' => $this->payment_type, //默认为1,不需要修改
			'show_url' => $this->show_url, //商品相关网站
			'seller_email' => $this->seller_email //卖家邮箱，必填
		);
		$this->parameter = $this->para_filter($parameter);
		$sort_array = array ();
		$arg = '';
		$sort_array = $this->arg_sort($this->parameter);
		foreach ((array) $sort_array as $key => $val) {
			$arg .= $key . '=' . $val . '&';
		}
		$prestr = substr($arg, 0, strlen($arg) - 1); //去掉最后一个&号
		$this->mysign = $this->sign($prestr . $this->security_code);
	}

	//排序参数
	private function arg_sort($array) {
		ksort($array);
		reset($array);
		return $array;
	}

	//生成sign
	private function sign($prestr) {
		$mysign = "";
		if ($this->sign_type == 'MD5') {
			$mysign = md5($prestr);
		}
		elseif ($this->sign_type == 'DSA') {
			//DSA 签名方法待后续开发
			die('DSA 签名方法待后续开发，请先使用MD5签名方式');
		} else {
			die('支付宝暂不支持' . $this->sign_type . '类型的签名方式');
		}
		return $mysign;
	}

	//过滤参数
	private function para_filter($parameter) { //除去数组中的空值和签名模式
		$para = array ();
		foreach ((array) $parameter as $key => $val) {
			if ($key == 'sign' || $key == 'sign_type' || $val == '') {
				continue;
			} else {
				$para[$key] = $parameter[$key];
			}
		}
		return $para;
	}

	private function notify_verify() {
		if ($this->transport == 'https') {
			$veryfy_url = $this->https_gateway . "service=notify_verify" . "&partner=" . $this->partner . "&notify_id=" . $_POST["notify_id"];
		} else {
			$veryfy_url = $this->http_gateway . "partner=" . $this->partner . "&notify_id=" . $_POST["notify_id"];
		}
		$veryfy_result = $this->get_verify($veryfy_url);
		$post = $this->para_filter($_POST);
		$sort_post = $this->arg_sort($post);
		foreach ((array) $sort_post as $key => $val) {
			if ($key != "sign" && $key != "sign_type" && $key != "GroupId" && $key != "notOpenAgent" && $key != "extparam" && $key != "param") {
				$arg .= $key . "=" . $val . "&";
			}
		}
		$prestr = substr($arg, 0, strlen($arg) - 1); //去掉最后一个&号
		$this->mysign = $this->sign($prestr . $this->security_code);
		if (preg_match("/true$/i", $veryfy_result) && $this->mysign == $_POST["sign"]) {
			return true;
		} else {
			return false;
		}
	}

	private function return_verify() {
		$sort_get = $this->arg_sort($_GET);
		foreach ((array) $sort_get as $key => $val) {
			if ($key != "sign" && $key != "sign_type" && $key != "GroupId" && $key != "notOpenAgent") {
				$arg .= $key . "=" . $val . "&";
			}
		}
		$prestr = substr($arg, 0, strlen($arg) - 1); //去掉最后一个&号
		$this->mysign = $this->sign($prestr . $this->security_code);
		if ($this->mysign == $_GET["sign"]) {
			return true;
		} else {
			return false;
		}
	}

	private function get_verify($url, $time_out = "60") {
		$urlarr = parse_url($url);
		$errno = "";
		$errstr = "";
		$transports = "";
		if ($urlarr["scheme"] == "https") {
			$transports = "ssl://";
			$urlarr["port"] = "443";
		} else {
			$transports = "tcp://";
			$urlarr["port"] = "80";
		}
		$fp = @ fsockopen($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
		if (!$fp) {
			die("ERROR: $errno - $errstr<br />\n");
		} else {
			fwrite($fp, "POST " . $urlarr["path"] . " HTTP/1.1\r\n");
			fwrite($fp, "Host: " . $urlarr["host"] . "\r\n");
			fwrite($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fwrite($fp, "Content-length: " . strlen($urlarr["query"]) . "\r\n");
			fwrite($fp, "Connection: close\r\n\r\n");
			fwrite($fp, $urlarr["query"] . "\r\n\r\n");
			while (!feof($fp)) {
				$info[] = @ fgets($fp, 1024);
			}
			fclose($fp);
			$info = implode(",", $info);
			while (list ($key, $val) = each($_POST)) {
				$arg .= $key . "=" . $val . "&";
			}
			return $info;
		}
	}
}
