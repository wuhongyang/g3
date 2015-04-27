<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体 网上银行
 *文件: trade.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class trade
{
    //构造函数
    public function __construct() 
    {
        $this->db = db::connect(config('database','default'));
    }

    /*
     *   查询订单
     */
    public function getTradeInfo($trade_id,$deposit) {
        if (!empty($trade_id) && is_numeric($deposit)) {
            $sql = 'SELECT `uin`,`pay_uin`,`rebate`,`parent_type`,`child_type`,`status`,`groupid`,`pay_id`,`channel_id`,callback,element FROM '.DB_NAME_COMMON.'.tbl_recharge_order WHERE trade_id="'.$trade_id.'" AND `money`='.$deposit.' ORDER BY `uptime` DESC LIMIT 1;';
            $row = $this->db->get_row($sql);
            if(!empty($row) && is_array($row)) {
                $array = array(
                    'Flag'=>100,
                    'FlagString'=>'获取订单信息成功',
                    'Uin'=>$row['uin'],
                    'Rebate'=>$row['rebate'],
                    'ParentType'=>$row['parent_type'],
                    'ChildType'=>$row['child_type'],
                    'Status' => $row['status'],
                    'GroupId' => (int)$row['groupid'],
                    'PayId' => $row['pay_id'],
                    'ChannelId' => $row['channel_id'],
                    'Callback' => $row['callback'],
                    'Element' => $row['element'],
                    'PayUin' => $row['pay_uin']
                );
            } else {
                $array = array(
                    'Flag'=>101,
                    'FlagString'=>'获取订单信息失败'
                );
            }
        } else {
            $array = array(
                'Flag'=>102,
                'FlagString'=>'参数错误'
            );
        }
        return $array;
    }

    /*
     *   更新订单状态
     */
    public function updateTrade($trade_id,$deposit,$status) {
        if (!empty($trade_id) && is_numeric($deposit)) {
            $sql = 'UPDATE '.DB_NAME_COMMON.'.tbl_recharge_order SET `status`='.$status.',`uptime`='.time().' WHERE trade_id="'.$trade_id.'" AND `money`='.$deposit.' AND `status`=0 ORDER BY `uptime` DESC LIMIT 1;';
            if($this->db->query($sql)) {
                $array = array(
                    'Flag'=>100,
                    'FlagString'=>'更新订单状态成功',
                );
            } else {
                $array = array(
                    'Flag'=>101,
                    'FlagString'=>'更新订单状态失败',
                );
            }
        } else {
            $array = array(
                'Flag'=>102,
                'FlagString'=>'参数错误',
            );
        }
        return $array;
    }

    /*
     *   检查订单是否存在
     */
//     public function getTradeExist($trade_id,$deposit) {
//         if (!empty($trade_id) && is_numeric($deposit)) {
//             $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
//             $this->db->start_transaction(); // 开始事务
//             $sql = 'SELECT trade_id FROM '.DB_NAME_COMMON.'.tbl_recharge_order WHERE trade_id="'.$trade_id.'" AND `money`='.$deposit.' AND `status`=0 ORDER BY `uptime` DESC LIMIT 1 FOR UPDATE;';
//             if($trade_id = $this->db->get_var($sql)) {
//                 $array = array(
//                     'Flag'=>100,
//                     'FlagString'=>'订单存在',
//                 );
//             } else {
//                 $array = array(
//                     'Flag'=>101,
//                     'FlagString'=>'订单不存在',
//                 );
//             }
//             $this->db->commit();
//         } else {
//             $array = array(
//                 'Flag'=>102,
//                 'FlagString'=>'参数错误',
//             );
//         }
//         return $array;
//     }
    /*
     *   提交订单
     */
    public function submitTrade($pay_uin,$uin,$parent_type,$child_type,$expense,$rebate,$groupid,$payid,$channelid,$callback,$element) {
    	if($uin > 0 && $parent_type >= 0 && $child_type >= 0 && $expense > 0) {
            $trade_id = $parent_type.$child_type.date('YmdHis').rand(1000000000,9999999999);
            $sql = 'INSERT INTO '.DB_NAME_COMMON.'.tbl_recharge_order(trade_id,parent_type,child_type,uin,pay_uin,money,uptime,rebate,groupid,pay_id,channel_id,callback,element) VALUES("'.$trade_id.'","'.$parent_type.'","'.$child_type.'","'.$uin.'","'.$pay_uin.'","'.$expense.'","'.time().'","'.$rebate.'","'.$groupid.'","'.$payid.'","'.$channelid.'","'.$callback.'",\''.$element.'\')';
            if($this->db->query($sql)) {
                $array = array(
                    'Flag'=>100,
                    'FlagString'=>'提交订单成功',
                    'TradeId'=>$trade_id
                );
            } else {
                $array = array(
                    'Flag'=>101,
                    'FlagString'=>'提交订单失败',
                );
            }
        } else {
            $array = array(
                'Flag'=>102,
                'FlagString'=>'参数有误',
            );
        }
        return $array;
    }
}