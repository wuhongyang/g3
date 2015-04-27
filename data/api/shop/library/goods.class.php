<?php

class Goods {

    protected $db;
    const ENABLE = 1;
    const ROOM_SCOPE = 2;
    const IS_AGING = 1;
    const DISABLE = 0;

    public function __construct() {
        $this->db = domain::main()->GroupDBConn();
    }

    //商城分类下的商品
    public function goodsOnCategory($group_id,$category_id,$state){
        $group_id = intval($group_id);
        $category_id = intval($category_id);
        if($group_id < 1 || $category_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数有误');
        }
        $goods = $this->fetchGoodsOnCategory($group_id,$category_id,$state);
        if(!$goods){
            return array('Flag'=>102,'FlagString'=>'无商品');
        }
        return array('Flag'=>100,'FlagString'=>'商城分类','Goods'=>$goods);
    }

    //通过靓号获取真实UIN
    public function getActualUin($group_id, $liang_id){
        $liang_len = strlen($liang_id);
        $liang_id = intval($liang_id);
        if($liang_len < 4){
            return array('Flag'=>101,'FlagString'=>'用户不存在');
        }elseif($liang_len > 7){
            $uin = $liang_id;
        }else{
            $sql = "SELECT uin,quality AS uptime FROM ".DB_NAME_SHOP.".commodity_stock WHERE group_id={$group_id} AND parent_id=10428 AND liang_id={$liang_id}";
            $row = $this->db->get_row($sql);
            if(empty($row)){
                if($liang_len == 7){
                    $uin = $liang_id;
                }else{
                    return array('Flag'=>101,'FlagString'=>'用户不存在');
                }
            }else{
                $uin = $row['uin'];
            }
        }
        return array('Flag'=>100,'FlagString'=>'OK', 'Uin'=>$uin);
    }

    //商品详情
    public function getInfo($group_id,$id,$state=1){
        $group_id = intval($group_id);
        $id = intval($id);
        if($group_id < 1 || $id < 1){
            return array('Flag'=>101,'FlagString'=>'参数有误');
        }
        $info = $this->fetchInfo($group_id,$id,$state);
        if(!$info){
            return array('Flag'=>102,'FlagString'=>'无商品详情');
        }
        return array('Flag'=>100,'FlagString'=>'商品详情','Info'=>$info);
    }

    public function getCommodityInfo($commodity_id){
        $commodity_id = intval($commodity_id);
        if($commodity_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数有误');
        }
        $info = $this->fetchCommodityInfo($commodity_id);
        if(empty($info)){
            return array('Flag'=>102,'FlagString'=>'没有商品详情');
        }
        return array('Flag'=>100,'FlagString'=>'成功','CommodityInfo'=>$info);
    }

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
            $goodsInfo = $this->fetchGoodsInfo($val['group_id'],$val['commodity'],self::DISABLE);
            $commodityInfo = $this->fetchCommodityInfo($goodsInfo['commodity_id']);
            if($commodityInfo['scope'] == self::ROOM_SCOPE){
                if($val['roomid'] == $data['room_id']){
                    $stocks = $commodityInfo;
                }else{
                    continue;
                }
            }else{
                $stocks = $commodityInfo;
            }
            if($commodityInfo['type'] == self::IS_AGING){
                if($val['quality'] > time()){
                    $stocks = $commodityInfo;
                }else{
                    $stocks = array();
                }
            }
            if($stocks == $commodityInfo){
                break;
            }
        }
        if(empty($stocks)){
            return array('Flag'=>103,'FlagString'=>'没有库存');
        }
        return array_merge(array('Flag'=>100,'FlagString'=>'库存信息'),$stocks);
    }

    public function getUserStocksOnCategory($uin,$group_id,$category){
        //由公司后台分类行到站后台分类
        $sql = "SELECT id FROM ".DB_NAME_SHOP.".goods_cate WHERE group_id={$group_id} AND cate_id={$category} LIMIT 1";
        $category_id = $this->db->get_var($sql);
        if($category_id < 1){
            return array('Flag'=>101,'FlagString'=>'无该分类');
        }
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$uin} AND cate_id={$category_id}";
        if(!($res = $this->db->get_results($sql, ASSOC))){
            return array('Flag'=>101,'FlagString'=>'该分类无库存');
        }
        foreach ($res as $key => $val) {
            $goodsInfo = $this->fetchGoodsInfo($val['group_id'],$val['commodity'],self::DISABLE);
            $commodityInfo = $this->fetchCommodityInfo($goodsInfo['commodity_id']);
            $commodityInfo['goods_id'] = $goodsInfo['id'];
            $res[$key] = array_merge($commodityInfo,$val);
        }
        return array('Flag'=>100,'FlagString'=>'库存信息','StockInfo'=>$res);
    }

    public function getGoodsOnCat($group_id,$categoryId){
        $categoryId = intval($categoryId);
        if($categoryId < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $platform_db = db::connect(config('database','default'));
        $sql = "SELECT id,bigcase_id,case_id,parent_id,category FROM ".DB_NAME_TPL.".commodity WHERE category={$categoryId}";
        $lists = $platform_db->get_results($sql, ASSOC);
        if(empty($lists)){
            return array('Flag'=>102,'FlagString'=>'无商品');
        }
        $r = array();
        foreach ((array)$lists as $key => $val) {
            $sql = "SELECT id,commodity_name AS `label`,duration AS expire,price FROM ".DB_NAME_SHOP.".goods WHERE group_id={$group_id} AND commodity_id={$val['id']}";
            $res = $this->db->get_results($sql, ASSOC);
            if(!empty($res) && is_array($res)){
                foreach ($res as $k => $v) {
                    $res[$k]['bigcase_id'] = $val['bigcase_id'];
                    $res[$k]['case_id'] = $val['case_id'];
                    $res[$k]['parent_id'] = $val['parent_id'];
                    $res[$k]['category'] = $val['category'];
                }
                $r = array_merge($r,$res);
            }
        }
        if(empty($r)){
            return array('Flag'=>103,'FlagString'=>'无商品');
        }
        return array('Flag'=>100,'FlagString'=>'商品','Lists'=>$r);
    }

    //购买
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

    protected function giftsToStock($json,$data){
        $json = json_decode($json,true);
        //商品检测，站后台
        $goodsInfo = $this->fetchGoodsInfo($data['group_id'],$json['gift_goods_id'],self::DISABLE);
        if(empty($goodsInfo)){
            return array('Flag'=>103,'FlagString'=>'赠送商品不存在，请联系客服');
        }
        //获取后台商品配置，公司后台
        $commodityInfo = $this->fetchCommodityInfo($goodsInfo['commodity_id']);

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
            return array('Flag'=>104,'FlagString'=>'赠品入库失败，请联系客服');
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
        return array('Flag'=>100,'FlagString'=>'赠品入库成功');
    }

    protected function fetchInfo($group_id,$goods_id,$state=1){
        return $this->fetchGoodsInfo($group_id,$goods_id,$state);
    }

    protected function fetchGoodsOnCategory($group_id,$category_id,$state=1){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".goods WHERE group_id={$group_id} AND cate_id={$category_id}";
        if($state == self::ENABLE){
            $sql .= " AND state=".self::ENABLE;
        }
        $sql .= " ORDER BY `order`";
        return $this->db->get_results($sql,ASSOC);
    }

    //商品是否存在
    protected function fetchGoodsInfo($group_id,$goods_id,$state=1){
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".goods WHERE group_id={$group_id} AND id={$goods_id}";
        if($state == 1){
            $sql .= " AND state={$state}";
        }
        return $this->db->get_row($sql,ASSOC);
    }

    protected function fetchStock($data){
        $where = '';
        if($data['group_id'] > 0){
            $where .= " AND group_id={$data['group_id']}";
        }
        if($data['case_id'] > 0){
            $where .= " AND case_id={$data['case_id']}";
        }
        if($data['parent_id'] > 0){
            $where .= " AND parent_id={$data['parent_id']}";
        }
        if($data['role_id'] > 0){
            $where .= " AND role_id={$data['role_id']}";
        }

        $sql = "SELECT * FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$data['uin']} {$where}";
        return $this->db->get_results($sql, ASSOC);
    }

    //获取商品配置
    protected function fetchCommodityInfo($commodity_id){
        $platform_db = db::connect(config('database','default'));
        $sql = "SELECT * FROM ".DB_NAME_TPL.".commodity WHERE id={$commodity_id} AND `status`=1";
        $row = $platform_db->get_row($sql,ASSOC);
        if(strlen($row['image_md5']) == 32){
            $row['image_md5'] = cdn_url(PIC_API_PATH.'/p/'.$row['image_md5'].'/0/0.jpg');
        }
        if(strlen($row['flash_md5']) == 32){
            $row['flash_md5'] = cdn_url(PIC_API_PATH.'/p/'.$row['flash_md5'].'/0/0.swf');
        }
        if(strlen($row['room_image_md5']) == 32){
            $row['room_image_md5'] = cdn_url(PIC_API_PATH.'/p/'.$row['room_image_md5'].'/0/0.jpg');
        }
        return $row;
    }

    protected function roomId($group_id,$roomid,$scope){
        if($scope == self::ROOM_SCOPE){
            if($roomid < 1){
                return array('Flag'=>104,'FlagString'=>'请选择房间号');
            }
            $rst = httpPOST('api/group/group_manage_api.php',array('extparam'=>array('Tag'=>'GetGroupRoomsList','GroupId'=>$group_id,'ChannelId'=>$roomid)));
            if($rst['total'] < 1){
                return array('Flag'=>105,'FlagString'=>'房间不存在');
            }
        }else{
            $roomid = 0;
        }
        return array('Flag'=>100,'FlagString'=>'OK','RoomId'=>$roomid);
    }

    protected function payAndTax($info){
        //扣钱
        $param = array(
            'extparam'=>array('Tag'=>'Kmoney', 'Operator'=>'574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$info['group_id']),
            'param'=>array('BigCaseId'=>$info['bigcase_id'],'CaseId'=>$info['case_id'],'ParentId'=>$info['parent_id'],'ChildId'=>101,'Desc'=>$info['desc1'],'MoneyWeight'=>$info['money'],'Uin'=>$info['uin'],'DoingWeight'=>$info['num'],'TargetUin'=>$info['target_uin'],'GroupId'=>$info['group_id'],'ChannelId'=>$info['roomid'])
        );
        if($info['actor_uin'] > 0){
            $param['param']['ActorUin'] = $info['actor_uin'];
        }
        $rst = httpPOST(KMONEY_API_PATH,$param);
        if($rst['Flag'] != 100){
            return $rst;
        }
        $log[] = getLogData($param['param'],$param['extparam']);
        //税收
        $param = array(
            'extparam' => array('Tag'=>'Kmoney', 'Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$info['group_id']),
            'param'=>array('BigCaseId'=>$info['bigcase_id'],'CaseId'=>$info['case_id'],'ParentId'=>$info['parent_id'],'ChildId'=>901,'Desc'=>$info['desc2'],'MoneyWeight'=>$info['money'],'Uin'=>$info['uin'],'DoingWeight'=>$info['num'],'TargetUin'=>$info['target_uin'],'GroupId'=>$info['group_id'],'ChannelId'=>$info['roomid']),
        );
        if($info['actor_uin'] > 0){
            $param['param']['ActorUin'] = $info['actor_uin'];
        }
        $rst = httpPOST(KMONEY_API_PATH,$param);
        if($rst['Flag'] != 100){
            return $rst;
        }
        $log[] = getLogData($param['param'],$param['extparam']);
        $rst['log'] = $log;
        return $rst;
    }

    //入库
    protected function intoStock($info){
        $time = time();
        $where = '';
        if($info['roomid'] > 0){
            $where = " AND roomid={$info['roomid']}";
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

    //记录流水
    protected function intoRunning($info){
        $time = time();
        $sql = "INSERT INTO ".DB_NAME_SHOP.".commodity_running(`group_id`,`uin`,`pay_uin`,`commodity`,`case_id`,`parent_id`,`name`,`quantity`,`money`,`uptime`) VALUES({$info['group_id']},{$info['uin']},{$info['pay_uin']},{$info['commodity']},{$info['case_id']},{$info['parent_id']},'{$info['name']}',{$info['quantity']},{$info['money']},{$time})";
        $this->db->query($sql);
    }
}
