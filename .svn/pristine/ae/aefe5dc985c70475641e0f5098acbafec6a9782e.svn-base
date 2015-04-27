<?php
/**
 * 广告统计
 * @author pgp
 * @copyright aodiansoft.com
 * @version $Id$
 */
class Ad{
    protected $mongodb = null;
    
    function __construct(){
        $this->mongodb = db::connect(config('mongodb','ktv'),'mongo');
    }
    
    public function __destruct() {
        unset($this->db);
    }

    //积分流水
    public function detailList($data){
		if(!empty($data['stime']) && !empty($data['etime'])){
            $query_condition['RegisterTime'] = array('$gte'=>intval(strtotime($data['stime'].' 00:00:00')),'$lte'=>intval(strtotime($data['etime'].' 23:59:59')));
        }
		if($data['Fromname']){
			$query_condition['Fromname'] = $data['Fromname'];
		}
        $table_name = 'Advertise.details';
        $page_arr = $this->showPage($total,20);
        list($offset,$rows) = explode(',',$page_arr['limit']);
        $result = $this->mongodb->get_results(
            $table_name,
            $query_condition,
            array(
                'sort'=>array('RegisterTime'=>-1),
                'limit'=>array('offset'=>$offset,'rows'=>$rows)
            )
        );
        $page_arr = $this->showPage(10,20);
        $list['list'] = $result;
        $list['page'] = $page_arr['page'];
        return array('Flag'=>100,'FlagString'=>'成功','Result'=>$list);
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