<?php

include_once 'goods.class.php';

class Daemon extends Goods {

    public function getStock($data){
        $data = array_map('intval', $data);
        if($data['uin'] < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $uinStocks = $this->fetchStock($data);
        if(!$uinStocks){
            return array('Flag'=>102,'FlagString'=>'没有库存');
        }
        $stocks = array();
        foreach ($uinStocks as $key => $val) {
            $commodityInfo = $this->fetchCommodityInfo($val['commodity']);
            if($commodityInfo['type'] == self::IS_AGING){
                if($val['quality'] < time()){
                    continue;
                }
            }
            if($commodityInfo['scope'] == self::ROOM_SCOPE){
                if($val['roomid'] == $data['room_id']){
                    $stocks[] = $commodityInfo;
                }else{
                    continue;
                }
            }else{
                $stocks[] = $commodityInfo;
            }
        }
        if(empty($stocks)){
            return array('Flag'=>103,'FlagString'=>'没有库存');
        }
        return array_merge(array('Flag'=>100,'FlagString'=>'库存信息'),$stocks);
    }

	public function buy($data){
		//参数处理
        $data = array_map('intval', $data);
        if($data['commodity_id'] < 1 || $data['uin'] < 1 || $data['roomid'] < 1){
            return array('Flag'=>101,'FlagString'=>'非法参数');
        }
        $actualUin = $this->getActualUin($data['group_id'], $data['uin']);
        if($actualUin['Flag'] != 100){
            return $actualUin;
        }
        $data['uin'] = $actualUin['Uin'];
        //用户检测
        $user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$data['uin'],'GroupId'=>$data['group_id'])));
        if($user['Flag'] != 100){
            return array('Flag'=>102,'FlagString'=>'用户不存在或不是本站用户');
        }

        //艺人检测
        if(getChannelType($data['artist'],$data['roomid']) < 1){
            return array('Flag'=>103,'FlagString'=>'不是该房间艺人');
        }

        //商品检测，站后台
        $goodsId = $data['commodity_id'];
        $goodsInfo = $this->fetchGoodsInfo($data['group_id'],$goodsId,self::DISABLE);
        if(empty($goodsInfo)){
            return array('Flag'=>103,'FlagString'=>'商品不存在或已下架，无法购买');
        }
        //获取后台商品配置，公司后台
        $commodityInfo = $this->fetchCommodityInfo($goodsInfo['commodity_id']);
        
        //在同一个房间是否守护了艺人
		$sql = "SELECT other_id,quality FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$data['uin']} AND case_id={$commodityInfo['case_id']} AND roomid={$data['roomid']} ORDER BY quality DESC";
		$daemon = $this->db->get_row($sql, ASSOC);
        if(!empty($daemon)){
			if($daemon['other_id'] != $data['artist']){
				if($daemon['quality'] > time()){
					return array('Flag'=>104,'FlagString'=>"您目前正在守护{$daemon['other_id']}，不能在同一房间内同时守护2位主播");
				}
			}
        }
        

        //根据作用域确定房间号
        $roomInfo = $this->roomId($data['group_id'],$data['roomid'],$commodityInfo['scope']);
        if($roomInfo['Flag'] != 100){
            return $roomInfo;
        }
        $data['roomid'] = $roomInfo['RoomId'];

        /*************************************************************扣除用户钱入税收 start**********************************************************************/
        $money = $goodsInfo['price'] * $data['num'];
        $desc1 = '用户：'.$data['uin'].'购买 “'.$goodsInfo['commodity_name'].'” 支出'.$money.'';
        $desc2 = '用户：'.$data['uin'].' 购买 “'.$goodsInfo['commodity_name'].'” 价格：'.$money." 全额入税收";
        $payInfo = array('money'=>$money,'desc1'=>$desc1,'desc2'=>$desc2,'group_id'=>$data['group_id'],'pay_uin'=>$data['uin'],'bigcase_id'=>$commodityInfo['bigcase_id'],'case_id'=>$commodityInfo['case_id'],'parent_id'=>$commodityInfo['parent_id'],'num'=>$data['num'],'uin'=>$data['uin'],'target_uin'=>$data['artist'],'roomid'=>$data['roomid'],'actor_uin'=>$data['artist']);
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
            'other_id'  => $data['artist']
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
            'pay_uin'   => $data['uin'],
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
        /*************************************************************送车 start************************************************************************/
        if($goodsInfo['is_gift'] == 1){
            $rst = $this->giftsToStock($goodsInfo['gift_detail'],$data);
            if($rst['Flag'] != 100){
                return $rst;
            }
        }
        /*************************************************************送车 end**************************************************************************/
        return array('Flag'=>100, 'FlagString'=>'购买成功','Balance'=>get_money($data['uin'],$data['group_id']),'LogData'=>$log);
	}
	
	//入库
    protected function intoStock($info){
        $time = time();
        $where = '';
        if($info['roomid'] > 0){
            $where .= " AND roomid={$info['roomid']}";
        }
		if($info['other_id'] > 0){
			$where .= " AND other_id={$info['other_id']}";
		}
        //在库存中是否存在，存在就更新
        $sql = "SELECT id,quality FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$info['uin']} AND parent_id={$info['parent_id']} {$where}";
        if($row = $this->db->get_row($sql)){
            $num = $info['num'];
            $is_aging = $info['is_aging'];
            if($is_aging == 1){
                //$num *= $info['expire'];
                //是否查到的值已过期
                if($time > $row['quality']){  //已过期,从今天开始算
                    $quality = strtotime('+'.$num.' day');
                }else{  //没过期就在此基础上加
                    $quality = $info['num'] * 86400 + $row['quality'];
                }
            }else{
                $quality = $info['num'] + $row['quality'];
            }
            $sql = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET quality={$quality} WHERE id={$row['id']}";
        }else{
            $quality = ($info['is_aging'] == 1) ? strtotime('+'.$info['num'].' day') : $info['num'];
            $sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_stock(`uin`,`cate_id`,`commodity`,`case_id`,`parent_id`,`group_id`,`roomid`,`role_id`,`other_id`,`quality`) VALUES({$info['uin']},{$info['cate_id']},{$info['commodity']},{$info['case_id']},{$info['parent_id']},{$info['group_id']},{$info['roomid']},{$info['role_id']},{$info['other_id']},{$quality})";
        }
        if(!$this->db->query($sql)){
            return array('Flag'=>104,'FlagString'=>'入库失败，请联系客服');
        }
        return array('Flag'=>100,'FlagString'=>'入库成功');
    }

    protected function fetchStock($data){
        $where = "uin={$data['uin']}";
        if($data['case_id'] > 0){
            $where .= " AND case_id={$data['case_id']}";
        }
        if($data['group_id'] > 0){
            $where .= " AND group_id={$data['group_id']}";
        }
        if($data['room_id'] > 0){
            $where .= " AND roomid={$data['room_id']}";
        }
        if($data['role_id'] > 0){
            $where .= " AND role_id={$data['role_id']}";
        }
        if($data['artist'] > 0){
            $where .= " AND other_id={$data['artist']}";
        }
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".commodity_stock WHERE {$where}";
        return $this->db->get_results($sql, ASSOC);
    }
}