<?php
/**
 * 后台分站方言类
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
class IntegrateDetail{
    private $ruleDefine;
    protected $db = null;
    
    function __construct($ruleDefine, $dataGroupId){
        $this->db = db::connect(config('database','default'));
        $this->mongodb = domain::main()->GroupDBConn('mongo', $dataGroupId);
        $this->ruleDefine = $ruleDefine;
		/*$this->uin_array = array(1=>"发起用户ID",2=>"目标用户ID",3=>"艺人ID",4=>"室主ID");
		$this->channel_array = array(1=>"房间ID");
		$this->extend_array = array(1=>"地域ID");*/
//      $this->type_array = array(1=>"发起用户ID",2=>"目标用户ID",3=>"艺人ID",4=>"室主ID",5=>"房间ID",6=>"地域ID",7=>"群ID",8=>"站内会员ID",9=>array('option'=>"性别",'val'=>array(0=>"无",1=>"男",2=>"女")));
	//	uin_array = {1:"Uin",2:"TargetUin",3:"ActorUin",4:"OwnUin"},channel_array = {1:"ChannelId"},extend_array = {1:"RegionId",2:"GroupId"}
    }
    
    public function __destruct() {
        unset($this->db);
    }

    private function get_label_name($rule){
		$sql = "SELECT `id`,`name` FROM ".DB_NAME_BEHAVIOR.". business_key";
		$res = $this->db->get_results($sql, "ASSOC");
		$arr = array();
		foreach($res as $one){
			$arr[$one['id']] = $one['name'];
		}
		foreach($rule as $key => $val){
			$rule_arr[$val['id']] = $val['name'];
			$arr2 = json_decode($val['business_id_type'], true);
			if(is_array($arr2)){
				foreach($arr2 as $key_name=>$v){
					if($v){
						$label_name[$val['id']][$key_name] = $arr[$v];
					}
				}
			}
		}
		return $label_name;
	}
    
    //积分流水
    public function detailList($data){
        //得到业务规则
        $ruleDefineList = $this->ruleDefine->getRuleDefine(0);
        $ruleid = empty($data['rule']) ? intval($ruleDefineList['Result'][0]['id']) : intval($data['rule']);
        $label_name = $this->get_label_name($ruleDefineList['Result']);
        foreach($ruleDefineList['Result'] as $key => $val){
			$rl[$val['id']] = $val['name'];
		}
		if(!empty($data['stime']) && !empty($data['etime'])){
            $query_condition['Uptime'] = array('$gte'=>intval(strtotime($data['stime'].' 00:00:00')),'$lte'=>intval(strtotime($data['etime'].' 23:59:59')));
        }
        $query_condition['Ruleid'] = intval($ruleid);
        if($data['keys']){
        	foreach($data['keys'] as $key => $value){
        		if($value){
        			$query_condition[$key] = intval($value);
        		}
        	}
        }
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
        foreach((array)$result as $key => $val){
            $result[$key]['Uptime'] = date('Y-m-d H:i:s',$val['Uptime']);
            $param = array(
                'param'=>array(
                    "BigCaseId"=> $val['BigCaseId'],
                    "CaseId"   => $val['CaseId'],
                    "ParentId" => $val['ParentId'],
                    "ChildId"  => $val['ChildId'],
                ),
                'extparam'=>array(
                    "Tag" => "GetBusinessConfig",
                )
            );
        }
        $page_arr = $this->showPage($key+1,20);
        $list['list'] = $result;
        $list['page'] = $page_arr['page'];
        return array('Flag'=>100,'FlagString'=>'积分流水查看','Result'=>$list,'RuleDefineList'=>$rl,'LabelName'=>$label_name);
    }

    //积分汇总
    public function summaryList($data){
        //得到业务规则
        $ruleDefineList = $this->ruleDefine->getRuleDefine(0);
        $database = DB_NAME_INTEGRAL;
        $ruleid = empty($data['rule']) ? intval($ruleDefineList['Result'][0]['id']) : intval($data['rule']);
        $label_name = $this->get_label_name($ruleDefineList['Result']);
		foreach($ruleDefineList['Result'] as $key => $val){
			$rl[$val['id']] = $val['name'];
		}
        //得到要操作的表和时间条件格式化
        switch ($data['style']) {
            case '1':
                $table = 'day_weight';
                $query_condition['Uptime'] = intval(date('Ymd',time()));
                break;
            case '2':
                $table = 'week_weight';
                $query_condition['Uptime'] = intval(date('oW',time()));
                break;
            case '3':
                $table = 'month_weight';
                $query_condition['Uptime'] = intval(date('Ym',time()));
                break;
            case '4':
                $table = 'year_weight';
                $query_condition['Uptime'] = intval(date('Y',time()));
                break;
            case '5':
                $table = 'total_weight';
                break;
            default:
                $table = 'day_weight';
                $query_condition['Uptime'] = intval(date('Ymd',time()));
                break;
        }
        $query_condition['Ruleid'] = intval($ruleid);
        if($data['search']['keys']){
        	foreach($data['search']['keys'] as $key => $value){
        		if($value){
        			$query_condition[$key] = intval($value);
        		}
        	}
        }
        $table_name = $database.'.'.$table;
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
        $page_arr = $this->showPage(count($result),20);
        $list['list'] = $result;
        $list['page'] = $page_arr['page'];
        return array('Flag'=>100,'FlagString'=>'积分汇总查看','Result'=>$list,'RuleDefineList'=>$rl,'LabelName'=>$label_name);
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