<?php

class shop {

    private $db = null;
    private $trade_id = null;
    private $log = null;
    private $kmoney_rate = 10000;

    public function __construct() {
        $this->db = domain::main()->GroupDBConn();
    }

    public function __destruct() {
        unset ($this->db);
    }

    /*
     *   提交订单
     */
    public function submitTrade($json, $param)
    {
        $param = array(
            'extparam' => array('Tag' => 'SubmitTrade', 'Rebate' => $json['Rebate'], 'GroupId' => $json['GroupId'],'PayId'=>$json['PayId'],'ChannelId'=>$json['ChannelId'],'Callback'=>$json['Callback'],'Element'=>$json['Element']),
            'param'	   => array('MoneyWeight' => $param['MoneyWeight'], 'Uin' => $param['Uin'],  'TargetUin' => $param['TargetUin'],'ParentId' => $param['ParentId'], 'ChildId' => $param['ChildId']),
        );
        return httpPOST(TRADE_API_PATH, $param);
    }

    /*
     *   支付宝异步请求
     */
    public function alipayNotify($param,$extparam)
    {
        $bank = bank::payment('ALIPAY');
        $bank->pay_receive();
        if ($bank->check_notify_receive()) {
            $array = $bank->return_data();
            $trade_id = $array['out_trade_no'];
            $trade_fee = $array['total_fee'];
            $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
            $uin = $pay_arr['Uin'];
            $pay_uin = $pay_arr['PayUin'];
            $groupid	= $pay_arr['GroupId'];
            $parent_type = $param['ParentId'];
            $rebate	= $pay_arr['Rebate'];
            $payid	= $pay_arr['PayId'];
            $channelid	= $pay_arr['ChannelId'];
            $content =  "支付宝充值";
            $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;
            if($bank->check_pay()) {
				$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);
                if ($pay_result == true) {
                    $result = array(
                        'Flag' 		=> '100',
                        'FlagString'=> '成功',
                        'Result' 	=> 'success',
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
                        'Callback'=>$pay_arr['Callback'],
						'LogData'=> $this->log
                    );
                }else{
					$result = array(
						'Flag' 		=> '103',
						'FlagString'    => '失败',
						'Result' 	=> '操作失败',
					);
				}
            }
        } else {
            $result = array(
                'Flag' 		=> '101',
                'FlagString'=> '失败',
                'Result' 	=> 'fail',
            );
        }
        return $result;
    }

    /*
     *   支付同步请求
     */
    public function alipayReturn($param,$extparam)
    {
        $bank = bank::payment('ALIPAY');
        $bank->pay_receive();

        if ($bank->check_return_receive()) {
            $array = $bank->return_data();
            $trade_id = $array['out_trade_no'];
            $trade_fee = $array['total_fee'];
            $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
            $uin = $pay_arr['Uin'];
            $pay_uin = $pay_arr['PayUin'];
            $groupid	= $pay_arr['GroupId'];
            $parent_type = $param['ParentId'];
            $rebate	= $pay_arr['Rebate'];
            $payid	= $pay_arr['PayId'];
            $channelid	= $pay_arr['ChannelId'];
            $content = "支付宝充值";
            $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;
			
			$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);
            if ($pay_result == true) {
                $result = array(
                    'Flag' 		=> '100',
                    'FlagString'=> '成功',
                    'Result' 	=> 'success',
                    'Money'	=> $trade_fee,
					'Uin'=>$uin,
					'PayId'=>$payid,
					'ChannelId'=>$channelid,
                    'Callback'=>$pay_arr['Callback'],
					'LogData'=> $this->log
                );
            }else{
				$result = array(
					'Flag' 		=> '103',
					'FlagString'    => '失败',
					'Result' 	=> '操作失败',
					'Money'	=> $trade_fee,
					'Uin' => $uin,
					'PayId'=>$payid,
					'ChannelId'=>$channelid,
					'TradeId' => $trade_id,
				);
			}
        } else {
            $result = array(
                'Flag' 		=> '101',
                'FlagString'=> '失败',
                'Result' 	=> 'fail',
            );
        }
        return $result;
    }

    /*
     *  网银在线auto自动接收请求
     */
    public function chinabankAuto($param,$extparam)
    {
        $bank = bank::payment('CHINABANK');
        $bank->pay_receive();
        if ($bank->check_receive()) {
            if($bank->check_pay()) {
                $array = $bank->return_data();
                $trade_id = $array['v_oid'];
                $trade_fee = $array['v_amount'];
                $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
                $uin = $pay_arr['Uin'];
				$pay_uin = $pay_arr['PayUin'];
                $groupid	= $pay_arr['GroupId'];
                $parent_type = $param['ParentId'];
                $rebate	= $pay_arr['Rebate'];
				$payid	= $pay_arr['PayId'];
				$channelid	= $pay_arr['ChannelId'];
                $content = "网银充值";
                $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;
    			
				$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);

                if ($pay_result == true) {
                    $result = array(
                        'Flag' 		=> '100',
                        'FlagString'=> '成功',
                        'Result' 	=> 'ok',
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
                        'Callback'=>$pay_arr['Callback'],
						'LogData'=> $this->log
                    );
                }else{
					$result = array(
						'Flag' 		=> '103',
						'FlagString'    => '失败',
						'Result' 	=> '操作失败',
						'Money'	=> $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
						'TradeId' => $trade_id,
					);
				}
            }
        } else {
            $result = array(
                'Flag' 		=> '101',
                'FlagString'=> '失败',
                'Result' 	=> 'error',
            );
        }
        return $result;
    }

    /*
     *   网银在线receive请求
     */
    public function chinabankReceive($param,$extparam)
    {
        $bank = bank::payment('CHINABANK');
        $bank->pay_receive();
        if ($bank->check_receive()) {
            if($bank->check_pay()) {
                $array = $bank->return_data();
                $trade_id = $array['v_oid'];
                $trade_fee = $array['v_amount'];
                $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
                $uin = $pay_arr['Uin'];
				$pay_uin = $pay_arr['PayUin'];
                $groupid	= $pay_arr['GroupId'];
                $parent_type = $param['ParentId'];
                $rebate	= $pay_arr['Rebate'];
				$payid	= $pay_arr['PayId'];
				$channelid	= $pay_arr['ChannelId'];
                $content ="网银充值";
                $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;

				$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);
                if ($pay_result == true) {
                    $result = array(
                        'Flag' 		=> '100',
                        'FlagString'    => '成功',
                        'Result' 	=> 'ok',
                        'Money'	=> $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
                        'Callback'=>$pay_arr['Callback'],
						'LogData'=> $this->log
                    );
                }else{
					$result = array(
						'Flag' 		=> '103',
						'FlagString'    => '失败',
						'Result' 	=> '操作失败',
						'Money'	=> $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
						'TradeId' => $trade_id,
					);
				}
            } else{
                $result = array(
                    'Flag' 		=> '101',
                    'FlagString'    => '失败',
                    'Result' 	=> '支付失败',
                );
            }
        } else {
            $result = array(
                'Flag' 		=> '102',
                'FlagString'    => '失败',
                'Result' 	=> '校验失败,数据可疑',
            );
        }
        return $result;
    }

    /*
     *   财付通回调请求
     */
    public function tenpay($param,$extparam)
    {
        $bank = bank::payment('TENPAY');
        $bank->pay_receive();
        if ($bank->check_receive()) {
            if($bank->check_pay()) {
                $array = $bank->return_data();
                $trade_id = $array['sp_billno'];
                $trade_fee = floatval($array['total_fee'] / 100);
                $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
                $uin = $pay_arr['Uin'];
				$pay_uin = $pay_arr['PayUin'];
                $groupid	= $pay_arr['GroupId'];
                $parent_type = $param['ParentId'];
                $rebate	= $pay_arr['Rebate'];
				$payid	= $pay_arr['PayId'];
				$channelid	= $pay_arr['ChannelId'];
                $content = "财付通充值";
                $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;
                
				$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);
                if ($pay_result == true) {
                    $result = array(
                        'Flag' 		=> '100',
                        'FlagString'    => '成功',
                        'Result' 	=> '充值成功',
						'Money' => $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
                        'Callback'=>$pay_arr['Callback'],
						'LogData'=> $this->log
                    );
                }else{
					$result = array(
                        'Flag' 		=> '103',
                        'FlagString'    => '失败',
                        'Result' 	=> '支付失败，请联系客服',
						'Money'	=> $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
						'TradeId' => $trade_id,
                    );
				}
            } else {
                $result = array(
                    'Flag' 		=> '101',
                    'FlagString'    => '失败',
                    'Result' 	=> '支付失败，请联系客服',
                );
            }
        } else {
            $result = array(
                'Flag' 		=> '102',
                'FlagString'    => '失败',
                'Result' 	=> '认证签名失败',
            );
        }
        return $result;
    }

	/*
     *   宝付回调请求
     */
    public function BaofooReceive($param,$extparam)
    {
        $bank = bank::payment('BAOFOO');
        $bank->pay_receive();
        if ($bank->check_receive()) {
            if($bank->check_pay()) {
                $array = $bank->return_data();
                $trade_id = $array['v_oid'];
                $trade_fee = floatval($array['v_amount'] / 100);
                $pay_arr = $this->getTradeInfo($trade_fee, $trade_id);
                $uin = $pay_arr['Uin'];
				$pay_uin = $pay_arr['PayUin'];
                $groupid	= $pay_arr['GroupId'];
                $parent_type = $param['ParentId'];
                $rebate	= $pay_arr['Rebate'];
				$payid	= $pay_arr['PayId'];
				$channelid	= $pay_arr['ChannelId'];
                $content = "宝付充值";
                $deposit = (float)$trade_fee * (float)$rebate * $this->kmoney_rate;
                
				$pay_result = $this->submitPay($trade_id, $uin, $pay_uin, $parent_type, $trade_fee, $deposit, $groupid, $content,$channelid);
                if ($pay_result == true) {
                    $result = array(
                        'Flag' 		=> '100',
                        'FlagString'    => '成功',
                        'Result' 	=> '充值成功',
						'Money' => $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
						'LogData'=> $this->log
                    );
                }else{
					$result = array(
                        'Flag' 		=> '103',
                        'FlagString'    => '失败',
                        'Result' 	=> '支付失败,请联系客服',
						'Money'	=> $trade_fee,
						'Uin' => $uin,
						'PayId'=>$payid,
						'ChannelId'=>$channelid,
						'TradeId' => $trade_id,
                    );
				}
            } else {
                $result = array(
                    'Flag' 		=> '101',
                    'FlagString'    => '失败',
                    'Result' 	=> '支付失败，请联系客服',
                );
            }
        } else {
            $result = array(
                'Flag' 		=> '102',
                'FlagString'    => '失败',
                'Result' 	=> '认证签名失败',
            );
        }
        return $result;
    }
    /*
     *   跳转到网银
     *   @param
     *   @return
     */
    public function webPay($post, $param)
    {
        $pay_type = $post['pay_type'];
        $pay_id = $post['pay_id'];
        $pay_uin = $param['Uin'];
        $pay_expense = $param['MoneyWeight'];
        $trade_id = $post['trade_id'];
		$type = empty($post['type'])? 'vbao' : $post['type'];
		$notOpenAgent = isset($post['notOpenAgent']) ? $post['notOpenAgent'] : 0;
        $bank = bank::payment($pay_type,$type,$notOpenAgent,$post['GroupId']);
		$bank->total_fee($pay_expense);
        $bank->trade_id($trade_id);
        $bank->pay_id($pay_id);

        $bank->remark($param['Desc'],'商品订单号:'.$trade_id);
        $result['Flag'] = '100';
        $result['FlagString'] = '成功';
        $result['Result'] =  $bank->pay_send();
        return $result;
    }

    private function submitPay($trade_id, $uin, $pay_uin, $parent_id, $rmb, $deposit,$groupid, $content,$channel_id){
        if (!empty($trade_id) && $uin > 0 && $parent_id > 0 && $rmb > 0 && $deposit > 0 && !empty($content)){
            $trade_total = $this->getTradeInfo($rmb, $trade_id);
            if($trade_total['Flag'] == 100 && $trade_total['Status'] == 0){ //订单存在并有效
                
            	//订单状态修改
            	$balance = get_parent_money(10006, 10049, 10269, $groupid);
            	if($deposit > $balance){
            		httpPOST(TRADE_API_PATH, array('param'=>array('MoneyWeight'=>$rmb),'extparam'=>array('Tag'=>'UpdateTrade','TradeId'=>$trade_id,'Status'=>2)));
            		return false;
            	}else{
            		httpPOST(TRADE_API_PATH, array('param'=>array('MoneyWeight'=>$rmb),'extparam'=>array('Tag'=>'UpdateTrade','TradeId'=>$trade_id,'Status'=>1)));
            	}
           	
                $trade_desc3	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币.从站预存账户扣除'.$deposit.'金币';
                $param = array(
                		'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
                		'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => 10269, 'ChildId' => 108, 'Desc' => $trade_desc3,'ChannelId'=>$channel_id,'BigCaseId'=>10006,'CaseId'=>10049,'GroupId'=>$groupid),
                );
                $pay_result3 = httpPOST(KMONEY_API_PATH, $param);
                $log[] = $param;
                $trade_desc1	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币[三级余额库]';
                $param = array(
                    'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
                    'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => $parent_id, 'ChildId' => 101, 'Desc' => $trade_desc1,'ChannelId'=>$channel_id,'BigCaseId'=>10005,'CaseId'=>10024,'GroupId'=>$groupid),
                );
                $pay_result1 = httpPOST(KMONEY_API_PATH, $param);
                $log[] = $param;
                $trade_desc2	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币[用户余额库]';
                $param = array(
                    'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
                    'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => $parent_id, 'ChildId' => 102, 'Desc' => $trade_desc2,'ChannelId'=>$channel_id,'BigCaseId'=>10005,'CaseId'=>10024,'GroupId'=>$groupid),
                );
                $pay_result2 = httpPOST(KMONEY_API_PATH, $param);
                $log[] = $param;
				$this->log = $log;
                if($pay_result1['Flag'] == 100 && $pay_result2['Flag'] == 100 && $pay_result3['Flag'] == 100){
					if(!empty($trade_total['Callback']) && !empty($trade_total['Element'])){
						$element = unserialize($trade_total['Element']);
						$pay_result4 = httpPOST($trade_total['Callback'], $element);
						if($pay_result4['Flag'] == 100){
							return true;
						}else{
							return false;
						}
					}else{
						return true;
					}
                } else {
                    return false; //支付存入失败
                }
            } else {
                return $trade_total['Status'] == 1 ? true : false; //该定单已执行
            }
        }
    }

    private function getTradeInfo($rmb,$trade_id){
        $param = array(
            'param'	   => array('MoneyWeight' => $rmb),
            'extparam' => array('Tag' => 'GetTradeInfo', 'TradeId' => $trade_id),
        );
        return httpPOST(TRADE_API_PATH, $param);
    }
}
