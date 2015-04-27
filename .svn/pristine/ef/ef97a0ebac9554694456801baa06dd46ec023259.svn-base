<?php

include_once 'goods.class.php';

class Aristocracy extends Goods {
	
	public function buy($data){
        //参数处理
        $data = array_map('intval', $data);
        if($data['goods_id'] < 1 || $data['uin'] < 1){
            return array('Flag'=>101,'FlagString'=>'非法参数');
        }
        $actualUin = $this->getActualUin($data['group_id'], $data['uin']);
        if($actualUin['Flag'] != 100){
            return $actualUin;
        }
        $data['uin'] = $actualUin['Uin'];
        //用户检测
        $user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$data['uin'])));
        if($user['Flag'] != 100){
            return array('Flag'=>102,'FlagString'=>'用户不存在或不是本站用户');
        }

        //商品检测，站后台
        $goodsInfo = $this->fetchGoodsInfo($data['group_id'],$data['goods_id']);
        if(empty($goodsInfo)){
            return array('Flag'=>103,'FlagString'=>'商品不存在或已下架，无法购买');
        }

        //获取后台商品配置，公司后台
        $commodityInfo = $this->fetchCommodityInfo($goodsInfo['commodity_id']);

        //根据作用域确定房间号
        $roomInfo = $this->roomId($data['group_id'],$data['roomid'],$commodityInfo['scope']);
        if($roomInfo['Flag'] != 100){
            return $roomInfo;
        }
        $data['roomid'] = $roomInfo['RoomId'];

        /*************************************************************扣除用户钱入税收 start**********************************************************************/
        $money = $goodsInfo['price'] * $data['num'];
        $desc1 = '用户：'.$data['pay_uin'].'购买“'.$goodsInfo['commodity_name'].'”支出'.$money.'';
        $desc2 = '用户：'.$data['pay_uin'].' 购买 “'.$goodsInfo['commodity_name'].'” 价格：'.$money." 全额入税收";
        $payInfo = array('money'=>$money,'desc1'=>$desc1,'desc2'=>$desc2,'group_id'=>$data['group_id'],'uin'=>$data['pay_uin'],'target_uin'=>$data['uin'],'bigcase_id'=>$commodityInfo['bigcase_id'],'case_id'=>$commodityInfo['case_id'],'parent_id'=>$commodityInfo['parent_id'],'num'=>$data['num']);
        $rst = $this->payAndTax($payInfo);
        if($rst['Flag'] != 100){
            return $rst;
        }
        $log = $rst['log'];
        $extlog = $log[0];
        $extlog['param']['ChildId'] = 912;
        $extlog['param']['Uin'] = $data['uin'];
        $log[count($log)] = $extlog;
        unset($extlog);
        /*************************************************************扣除用户钱入税收 end**********************************************************************/

        /*************************************************************记录库存 start***************************************************************************/
        $stockInfo = array(
            'uin'       => $data['uin'],
            'cate_id'   => $goodsInfo['cate_id'],
            'commodity' => $goodsInfo['id'],
            'case_id'   => $commodityInfo['case_id'],
            'parent_id' => $commodityInfo['parent_id'],
            'group_id'  => $data['group_id'],
            'roomid'    => $data['roomid'],
            'role_id'   => intval($commodityInfo['role_id']),
            'is_aging'  => intval($commodityInfo['type']),
            'num'       => ($commodityInfo['type']==1) ? $data['num']*$goodsInfo['duration'] : $data['num'],
            //'expire'    => $goodsInfo['duration'],
            'other_id'  => 0
        );
        $rst = $this->intoStock($stockInfo);
        if($rst['Flag'] != 100){
            return $rst;
        }
        /*************************************************************记录库存 end ****************************************************************************/

        /*************************************************************记录流水 start***************************************************************************/
        $runningInfo = array(
            'group_id'  => $data['group_id'],
            'uin'       => $data['uin'],
            'pay_uin'   => $data['pay_uin'],
            'commodity' => $goodsInfo['id'],
            'case_id'   => $commodityInfo['case_id'],
            'parent_id' => $commodityInfo['parent_id'],
            'name'      => $goodsInfo['commodity_name'],
            'quantity'  => ($commodityInfo['type']==1) ? $data['num']*$goodsInfo['duration'] : $data['num'],
            'money'     => $money
        );
        $this->intoRunning($runningInfo);
        /*************************************************************记录流水 end ****************************************************************************/

        /*************************************************************授予角色 start**********************************************************************/
        if($commodityInfo['role_id'] > 0){
            //添加角色
            $roleData=array(
                'extparam'=>array(
                    'Tag'=>'AddGroupRole',
                    'GroupId'=>$data['group_id'],
                    'Uin'=>$data['uin'],
                    'RoomId'=>$data['roomid'],
                    'RoleId'=>intval($commodityInfo['role_id']),
                    'Ruleid'=>intval($commodityInfo['case_id'])
                )
            );
            $rst = httpPOST(ROLE_API_PATH,$roleData);
            if($rst['Flag'] != 100){
                return array('Flag'=>108,'FlagString'=>'授予角色失败，请联系客服');
            }
        }
        /*************************************************************授予角色 end**********************************************************************/

        /*************************************************************赠品 start**********************************************************************/
        if($goodsInfo['is_gift'] == 1){
            $rst = $this->giftsToStock($goodsInfo['gift_detail'],$data);
            if($rst['Flag'] != 100){
                return $rst;
            }
        }
        /*************************************************************赠品 end**********************************************************************/
        return array('Flag'=>100, 'FlagString'=>'购买成功','LogData'=>$log,'Money'=>$money,'IsAging'=>($commodityInfo['type']==1),'Count'=>$data['num'],'Expire'=>($commodityInfo['type']==1) ? $data['num']*$goodsInfo['duration'] : $data['num']);
    }
}