<?php

class GoodsCommon {

    protected $db;

    public function __construct() {
        $this->db = domain::main()->GroupDBConn();
        $this->platform_db = db::connect(config('database','default'));
    }

    public function goodsDisplay($category, $group_id, $categoryId){
        $categoryId = intval($categoryId);
        $subCategories = $this->subCategories($group_id, $categoryId);
        if($subCategories['Flag'] != 100){
            return $subCategories;
        }

        $subCategories = $subCategories['SubCategories'];
        $table = $category == -1 ? 'goods_package' : 'goods';
        foreach ($subCategories as $key => $subCategory) {
            $sql = "SELECT * FROM ".DB_NAME_SHOP.".{$table} WHERE group_id={$group_id} AND cate_id={$categoryId} AND sub_cate_id={$subCategory['id']} AND state=1 ORDER BY `order` ASC";
            $goods = $this->db->get_results($sql, ASSOC);
            $subCategories[$key]['goods'] = (array)$goods;
        }
        return array('Flag'=>100,'FlagString'=>'商品','Goods'=>$subCategories);
    }

    private function subCategories($group_id,$categoryId){
        $sql = "SELECT id,name,`order` FROM ".DB_NAME_SHOP.".goods_sub_cat WHERE group_id={$group_id} AND cate_id={$categoryId} AND `status`=1 ORDER BY `order` ASC";
        $res = $this->db->get_results($sql, ASSOC);
        if(!$res){
            return array('Flag'=>101,'FlagString'=>'无子分类');
        }
        return array('Flag'=>100,'FlagString'=>'OK', 'SubCategories'=>$res);
    }

    //站商城分类
    public function categories($group_id,$category=0,$state=1){
        $group_id = intval($group_id);
        $category = intval($category);
        if($group_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数有误');
        }
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".goods_cate WHERE group_id={$group_id}";
        if($category > 0){
            $sql .= " AND cate_id={$category}";
        }
        if($state == 1){
            $sql .= " AND state=1";
        }
        $sql .= " ORDER BY `order`";
        $categories = $this->db->get_results($sql,ASSOC);
        if(!$categories){
            return array('Flag'=>102,'FlagString'=>'无分类');
        }
        return array('Flag'=>100,'FlagString'=>'商城分类','Category'=>$categories);
    }

    public function getCategoryByCaseId($case_id){
        $case_id = intval($case_id);
        $sql = "SELECT category FROM ".DB_NAME_TPL.".commodity WHERE case_id={$case_id} LIMIT 1";
        $category = $this->platform_db->get_var($sql);
        if($category < 1){
            return array('Flag'=>101,'FlagString'=>'获取失败');
        }
        return array('Flag'=>100,'FlagString'=>'获取成功','Category'=>$category);
    }

    public function setDefault($uin,$parent_id){
        $uin = intval($uin);
        $parent_id = intval($parent_id);
        if($uin < 1 || $parent_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        //是否过期
        $sql = "SELECT id,quality FROM ".DB_NAME_SHOP.".commodity_stock WHERE uin={$uin} AND parent_id={$parent_id}";
        $row = $this->db->get_row($sql,ASSOC);
        if($row['id'] < 0){
            return array('Flag'=>102,'FlagString'=>'不存在该进场道具');
        }
        if(time() > $row['quality']){
            return array('Flag'=>103,'FlagString'=>'该进场道具已过期，不能设为默认进场道具');
        }
        $sql = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET other_id=0 WHERE uin={$uin} AND case_id=10041";
        if(!$this->db->query($sql)){
            return array('Flag'=>104,'FlagString'=>'设置为默认进场道具失败');
        }
        $sql = "UPDATE ".DB_NAME_SHOP.".commodity_stock SET other_id=1 WHERE uin={$uin} AND parent_id={$parent_id}";
        if(!$this->db->query($sql)){
            return array('Flag'=>102,'FlagString'=>'设置失败');
        }
        return array('Flag'=>100,'FlagString'=>'设置成功，请去房间体验');
    }

    public function getCommoditiesByCategory($categoryId,$state=1){
        $categoryId = intval($categoryId);
        if($categoryId < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $sql = "SELECT id,bigcase_id,case_id,parent_id,name,expire,price,category FROM ".DB_NAME_TPL.".commodity WHERE category={$categoryId}";
        if($state == 1){
            $sql .= " AND status=1";
        }
        $lists = $this->platform_db->get_results($sql, ASSOC);
        if(empty($lists)){
            return array('Flag'=>102,'FlagString'=>'无商品');
        }
        return array('Flag'=>100,'FlagString'=>'商品','Lists'=>$lists);
    }

    /*
    public function getGroupSchemeInfo($group_id){
        $group_id = intval($group_id);
        if($group_id < 1){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $sql = "SELECT scheme_id FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$group_id}";
        $scheme_id = $this->platform_db->get_var($sql);
        if($scheme_id < 1){
            return array('Flag'=>102,'FlagString'=>'站点没有配置商城方案');
        }
        $schemeInfo = $this->getCommoditySchemeInfo($scheme_id);
        if($schemeInfo['Flag'] != 100){
            return array('Flag'=>103,'FlagString'=>'商城方案不存在或没开启');
        }
        return array('Flag'=>100,'FlagString'=>'成功','SchemeInfo'=>$schemeInfo['Info']);
    }

    private function getSchemeInfo($scheme_id){
        $scheme_id = intval($scheme_id);
        if($scheme_id < 1){
            return array('Flag'=>101,'FlagString'=>'非法参数');
        }
        $sql = "SELECT * FROM ".DB_NAME_SHOP.".scheme WHERE id={$scheme_id} AND `status`=1";
        $info = $this->platform_db->get_row($sql, ASSOC);
        if(empty($info)){
            return array('Flag'=>102,'FlagString'=>'获取失败');
        }
        return array('Flag'=>100,'FlagString'=>'获取成功','Info'=>$info);
    }*/
}
