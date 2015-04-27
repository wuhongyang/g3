<?php

include_once 'goods.class.php';

class Liang extends Goods {
    const LIANG_CATEGORY = 4;
    const LIANG_PARENTID = 10428;

    public function getStock($data){
        $uin = intval($data['uin']);
        if($uin < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $sql = "SELECT liang_id FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$uin} AND parent_id={$data['parent_id']} AND other_id>0";
        $liang_id = $this->db->get_var($sql);
        if($liang_id < 1){
            return array('Flag'=>102,'FlagString'=>'没有靓号');
        }
        return array('Flag'=>100,'FlagString'=>'靓号','other_id'=>$liang_id);
    }

    //被卖掉的靓号
    public function numberSaled($group_id){
        $sql = "SELECT `liang_id` FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$group_id} AND parent_id=".self::LIANG_PARENTID;
        $sales = $this->db->get_results($sql,ASSOC);
        if(!$sales){
            return array('Flag'=>101,'FlagString'=>'无');
        }
        $saled = array();
        foreach ((array)$sales as $val) {
            array_push($saled, $val['liang_id']);
        }
        return array('Flag'=>100,'FlagString'=>'已出售靓号','Sale'=>$saled);
    }

    public function classifies($group_id){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".special_num_cate WHERE group_id={$group_id} AND `status`=".self::ENABLE;
        if(!($categories = $this->db->get_results($sql, ASSOC))){
            return array('Flag'=>101,'FlagString'=>'无靓号分类');
        }
        return array('Flag'=>101,'FlagString'=>'靓号分类','Categories'=>$categories);
    }

    public function searchById($group_id,$name){
        if(!is_numeric($name) || $group_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        //1：没上架，，，，，2：已出售，，，，，3：在架上未出售
        $status = 1;
        $numInfo = array();

        //是否已出售
        $sql = "SELECT commodity FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$group_id} AND `liang_id`='{$name}'";
        if(($commdityId = $this->db->get_var($sql)) > 0){
            $status = 2;
            $numInfo = $this->fetchGoodsInfo($group_id,$commdityId);
        }else{
            //是否在架上
            $numInfo = $this->fetchGoodsInfoByName($group_id,$name);
            if(!empty($numInfo)){
                $status = 3;
            }
        }

        return array('Flag'=>100,'FlagString'=>'成功','Status'=>$status,'NumInfo'=>$numInfo);
    }

    //购买
    public function buy($data){
        //参数处理
        $data = array_map('intval', $data);
        $data['num'] = 1;
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

        //靓号是否被出售
       	/*$sql = "SELECT id FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$data['uin']} AND parent_id={$goodsInfo['parent_id']}";
        if($this->db->get_var($sql) > 0){
            return array('Flag'=>104,'FlagString'=>$data['uin'].'在该站已有靓号，不能够买');
        }*/
        
        //是否已出售
        $saled = $this->numberSaled($data['group_id']);
        $saled = (array)$saled['Sale'];
       
        if(in_array($goodsInfo['name'], $saled)){
            return array('Flag'=>105,'FlagString'=>'该靓号已出售');
        }

        //获取后台商品配置，公司后台
        $commodityInfo = $this->fetchCommodityInfo();

        //根据作用域确定房间号
        $roomInfo = $this->roomId($data['group_id'],$data['roomid'],$commodityInfo['scope']);
        if($roomInfo['Flag'] != 100){
            return $roomInfo;
        }
        $data['roomid'] = $roomInfo['RoomId'];

        /*************************************************************扣除用户钱入税收 start**********************************************************************/
        $money = $goodsInfo['price'] * $data['num'];
        $desc1 = '用户：'.$data['pay_uin'].'购买靓号“'.$goodsInfo['name'].'”支出'.$money.'';
        $desc2 = '用户：'.$data['pay_uin'].' 购买靓号 “'.$goodsInfo['name'].'” 价格：'.$money." 全额入税收";
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
        //如果该用户没有靓号 则默认设置为启用
        $other_id = 1;
		$sql = "SELECT count(*) FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$data['group_id']} AND uin={$data['uin']} AND parent_id=10428";
		if($this->db->get_var($sql) > 0) $other_id = 0;
        
		$stockInfo = array(
            'uin'       => $data['uin'],
            'cate_id'   => $goodsInfo['category'],
            'commodity' => $goodsInfo['id'],
            'case_id'   => $commodityInfo['case_id'],
            'parent_id' => $commodityInfo['parent_id'],
            'group_id'  => $data['group_id'],
            'roomid'    => $data['roomid'],
            'role_id'   => intval($commodityInfo['role_id']),
            //'is_aging'  => intval($commodityInfo['type']),
            'num'       => $data['num'],
            //'expire'    => $goodsInfo['duration'],
            'liang_id'  => $goodsInfo['name'],
			'other_id'  => $other_id
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
            'name'      => $goodsInfo['name'],
            'quantity'  => ($commodityInfo['type']==1) ? $data['num']*$goodsInfo['duration'] : $data['num'],
            'money'     => $money
        );
        $this->intoRunning($runningInfo);
        /*************************************************************记录流水 end ****************************************************************************/

        return array('Flag'=>100, 'FlagString'=>'购买成功','LogData'=>$log);
    }

    //商品是否存在
    protected function fetchGoodsInfo($group_id,$goods_id){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".special_num WHERE group_id={$group_id} AND id={$goods_id} AND `status`=1";
        return $this->db->get_row($sql,ASSOC);
    }

    protected function fetchGoodsOnCategory($group_id,$category_id){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".special_num WHERE group_id={$group_id} AND category={$category_id} AND `status`=".self::ENABLE;
        return $this->db->get_results($sql,ASSOC);
    }

    //获取商品配置
    protected function fetchCommodityInfo(){
        $platform_db = db::connect(config('database','default'));
        $sql = "SELECT * FROM ".DB_NAME_TPL.".commodity WHERE category=".self::LIANG_CATEGORY." AND `status`=1";
        return $platform_db->get_row($sql,ASSOC);
    }

    //入库
    protected function intoStock($info){
        $sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_stock(`uin`,`cate_id`,`commodity`,`case_id`,`parent_id`,`group_id`,`roomid`,`role_id`,`liang_id`,`quality`,`other_id`) VALUES({$info['uin']},{$info['cate_id']},{$info['commodity']},{$info['case_id']},{$info['parent_id']},{$info['group_id']},{$info['roomid']},{$info['role_id']},{$info['liang_id']},{$info['num']},{$info['other_id']})";
        if(!$this->db->query($sql)){
            return array('Flag'=>104,'FlagString'=>'入库失败，请联系客服');
        }
        return array('Flag'=>100,'FlagString'=>'入库成功');
    }

    private function fetchGoodsInfoByName($group_id,$name){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".special_num WHERE group_id={$group_id} AND `name`={$name}";
        return $this->db->get_row($sql, ASSOC);
    }
}
