<?php

include_once 'goods.class.php';

class Car extends Goods {

    protected function fetchStock($data){
        $where = '';
        if($data['case_id'] > 0){
            $where .= " AND case_id={$data['case_id']}";
        }
        if($data['parent_id'] > 0){
            $where .= " AND parent_id={$data['parent_id']}";
        }
        if($data['role_id'] > 0){
            $where .= " AND role_id={$data['role_id']}";
        }

        $sql = "SELECT * FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$data['uin']} {$where} AND other_id=".self::ENABLE;
        return $this->db->get_results($sql, ASSOC);
    }
}