<?php

include_once 'goods.class.php';

class FunctionCard extends Goods {

    public function getStock($data){
        $data = array_map('intval', $data);
        if($data['uin'] < 1 || $data['case_id'] < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $uinStocks = $this->fetchStock($data);
        if(!$uinStocks){
            return array('Flag'=>102,'FlagString'=>'没有库存');
        }
        return array('Flag'=>100,'FlagString'=>'库存信息','Stocks'=>$uinStocks);
    }

    public function useProps($uin,$parent_id){
        $uin = intval($uin);
        $parent_id = intval($parent_id);
        if($uin < 1 || $parent_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $sql = "SELECT commodity,quality,group_id,case_id,parent_id FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$uin} AND parent_id={$parent_id}";
        $row = $this->db->get_row($sql,ASSOC);
        if($row['quality'] < 1){
            return array('Flag'=>102,'FlagString'=>'该功能牌已使用完');
        }
        $count = $row['quality'] - 1;
        $sql = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET quality={$count} WHERE uin={$uin} AND parent_id={$parent_id}";
        if(!$this->db->query($sql)){
            return array('Flag'=>103,'FlagString'=>'功能牌使用失败');
        }

        //记录日志
        $goodsInfo = $this->fetchGoodsInfo($row['group_id'],$row['commodity']);
        $runningInfo = array(
            'group_id'  => $row['group_id'],
            'uin'       => $uin,
            'pay_uin'   => $uin,
            'commodity' => $row['commodity'],
            'case_id'   => $row['case_id'],
            'parent_id' => $row['parent_id'],
            'name'      => $goodsInfo['commodity_name'],
            'quantity'  => -1,
            'money'     => $goodsInfo['price']
        );
        $this->intoRunning($runningInfo);
        return array('Flag'=>100,'FlagString'=>'功能牌使用成功', 'Num'=>$count);
    }
}
