<?php

abstract class _bank {

	abstract public function total_fee($total_fee = 0.01);

	abstract public function trade_id($trade_id = '');

	abstract public function remark($subject = '', $body = '');

	abstract public function pay_id($id = 0);

	abstract public function pay_send();

	abstract public function pay_receive();

	abstract public function check_receive();

	abstract public function check_pay();

	abstract public function return_data();

	protected function doShowTenpay($show_url) {
		$strHtml = "<html><head>\r\n" .
		"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
		"<script language=\"javascript\">\r\n" .
		"document.location.replace('" . $show_url . "');\r\n" .
		"</script>\r\n" .
		"</head><body></body></html>";
		return $strHtml;
	}
}
