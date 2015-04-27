<?php

class ArtistTax{
	private $db;
	private $mongodb;

	public function __construct(){
		//$this->db = db::connect(config('database','default'));
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}

    
    public function proxyIntegrationDetail($uin, $group_id, $search=array()){
        //$package_id = intval($data['package_id']);
        $uin = intval($uin);
        $group_id = intval($group_id);
        
        //$ruleid = $this->getRuleid($package_id);
        //if($ruleid < 1){
        //     return array('Flag'=>101,'FlagString'=>'请联系管理员，配置角色积分规则');
        //}
        $ruleid = 38;

        if(empty($search['stime'])){
            $search['stime'] = intval(strtotime(date('Y-m-01', time())));
        }else{
            $search['stime'] = intval(strtotime($search['stime']));
        }
        if(empty($search['etime'])){
            $search['etime'] = intval(time());
        }else{
            $search['etime'] = intval(strtotime($search['etime'].' 23:59:59'));
        }
        $query_condition['Uptime'] = array('$gte'=>$search['stime'], '$lte'=>$search['etime']);
        $query_condition['Ruleid'] = $ruleid;
        $query_condition['UinId'] = $uin;
        $query_condition['ExtendUin'] = $group_id;
        $list = $this->getIntegrationDetail($query_condition);

        //日汇总
        $dayCollect = $this->dayIntegrationCollect($uin, $group_id, $ruleid);

        //月汇总
        $data = array('uin'=>$uin, 'group_id'=>$group_id, 'ruleid'=>$ruleid);
        $monthCollect = $this->getCurrentMonthTax($data);

        //所有
        $totalCollect = $this->getTotalTax($data);

        return array('Flag'=>100,'FlagString'=>'ok', 'Detail'=>$list, 'DayCollect'=>$dayCollect, 'MonthCollect'=>$monthCollect, 'TotalCollect'=>$totalCollect);
    }
/*
    private function getRuleid($package_id){
        $sql = "SELECT artist_rule FROM ".DB_NAME_PERMISSION.".role_package WHERE id={$package_id}";
        $ruleid = $this->db->get_var($sql);
        $ruleid = intval($ruleid);
        return $ruleid;
    }
*/
    //日汇总
    private function dayIntegrationCollect($uin, $group_id, $ruleid){
        $query_condition = array("Ruleid"=>$ruleid,"ExtendUin"=>$group_id,"UinId"=>$uin,'Uptime'=>intval(date('Ymd')));
        $weight = $this->mongodb->get_var(
            DB_NAME_INTEGRAL.'.day_weight',
            $query_condition,
            array('Weight')
        );
        return $weight;
    }

    //获取积分流水
    private function getIntegrationDetail($query_condition){
        $table_name = DB_NAME_INTEGRAL.'.details';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('Uptime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
        $list['list'] = $result;
        $list['page'] = $page_arr['page'];
        return $list;
    }

	public function getArtistTax($data){
        $data['uin'] = intval($data['uin']);
        $data['group_id'] = intval($data['group_id']);
		//得到站角色套餐包艺人税收规则
		//根据data['package_id']取得ruleid
		//$package_id = intval($data['package_id']);
        //$data['ruleid'] = $this->getRuleid($package_id);
        
		if(!empty($data['stime']) && !empty($data['etime'])){
            $query_condition['Uptime'] = array('$gte'=>intval(strtotime($data['stime'].' 00:00:00')),'$lte'=>intval(strtotime($data['etime'].' 23:59:59')));
        }
        $query_condition['Ruleid'] = intval($ruleid);
        $query_condition['UinId'] = $data['uin'];
        $query_condition['ExtendUin'] = $data['group_id'];
        $list = $this->getIntegrationDetail($query_condition);
        $totalTax = $this->getTotalTax($data);
        $CurrentMonthTax = $this->getCurrentMonthTax($data);
        return array('Flag'=>100,'FlagString'=>'积分流水查看','Result'=>$list,'TotalTax'=>$totalTax,'CurrentMonthTax'=>$CurrentMonthTax);
	}

    private function getCurrentMonthTax($data){
        $query_condition = array("Ruleid"=>$data['ruleid'],"ExtendUin"=>$data['group_id'],"UinId"=>$data['uin'],'Uptime'=>intval(date('Ym')));
        $weight = $this->mongodb->get_var(
            DB_NAME_INTEGRAL.'.month_weight',
            $query_condition,
            array('Weight')
        );
        return $weight;
    }

    private function getTotalTax($data){
        if(!empty($data['stime']) && !empty($data['etime'])){
            $query_condition['Uptime'] = array('$gte'=>intval(date('Ymd',strtotime($data['stime']))),'$lte'=>intval(date('Ymd',strtotime($data['etime']))));
        }
        $query_condition['Ruleid'] = $data['ruleid'];
        $query_condition['ExtendUin'] = $data['group_id'];
        $query_condition['UinId'] = $data['uin'];
        $result = $this->mongodb->get_results(
            DB_NAME_INTEGRAL.'.day_weight',
            $query_condition
        );
        $total = 0;
        foreach ($result as $key => $val) {
            $total += $val['Weight'];
        }
        return $total;
    }

	private function showPage($total, $perpage = 10) {
        require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
        $page = new extpage(array (
            'total' => $total,
            'perpage' => $perpage
        ));
        $pageArr['page'] = $page->simple_page($total);
        $pageArr['limit'] = $page->simple_limit();
        unset ($page);
        return $pageArr;
    }
}