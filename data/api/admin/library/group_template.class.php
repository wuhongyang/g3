<?php
class GroupTemplate{

	protected $db = null;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
    function tpl_list($data){
        $condition = '';
        if($data['Id']){
            $condition .= " AND id = ".$data['Id'];
        }
        $data['Status'] = intval($data['Status']);
        if($data['Status'] > 0){
            $condition .= " AND status={$data['Status']}";
        }
        
        if($data['NoPage']){
            $sql  = "SELECT * FROM ".DB_NAME_TPL.".`template` WHERE 1 ".$condition." ORDER by `id`";
            $data = $this->db->get_results($sql, "ASSOC");
            return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$data);
        }
        
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`template` WHERE 1 ".$condition;
        extract($this->showPage($this->db->get_var($sql)));
        
        $sql  = "SELECT * FROM ".DB_NAME_TPL.".`template` WHERE 1 ".$condition." ORDER by `id` LIMIT ".$limit;
        $data = $this->db->get_results($sql, "ASSOC");
        
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$data, "Page"=>$page);
    }
    
    function tpl_save($id, $name, $desc, $status){
        if($id){
            return $this->tpl_update($id, $name, $desc, $status);
        }else{
            return $this->tpl_add($name, $desc, $status);
        }
    }
    
    private function tpl_update($id, $name, $desc, $status){
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`template` WHERE `name` = '".$name."' AND id != ".$id;
        if($this->db->get_var($sql)){
            return array("Flag"=>102, "FlagString"=>"存在相同的模板名称");
        }
                
        $sql = "UPDATE ".DB_NAME_TPL.".`template` SET `name` = '".$name."' ,`desc` = '".$desc."' ,`status` = '".$status."' ,`uptime` = '".time()."' WHERE `id` = '".$id."'; ";
        if($this->db->query($sql)){
            return array("Flag"=>100, "FlagString"=>"更新成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"更新失败");
        }
    }
    
    private function tpl_add($name, $desc, $status){
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`template` WHERE `name` = '".$name."'";
        if($this->db->get_var($sql)){
            return array("Flag"=>102, "FlagString"=>"存在相同的模板名称");
        }
        
        $sql = "INSERT INTO ".DB_NAME_TPL.".`template` (`name`, `desc`, `status`, `uptime`) VALUES ('".$name."', '".$desc."', '".$status."', '".time()."'); ";
        if($this->db->query($sql)){
            return array("Flag"=>100, "FlagString"=>"添加成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"添加失败");
        }
    }
    
    function media_config($tpl_id){
        $sql = "SELECT * FROM ".DB_NAME_TPL.".`media_config` WHERE tpl_id = ".$tpl_id;
        $row = $this->db->get_row($sql, "ASSOC");
        
        if(!$row){
            return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>array());
        }
        
        $row['play_media_conf']     = json_decode($row['play_media_conf'], true);
        $row['admin_media_conf']    = json_decode($row['admin_media_conf'], true);
        $row['p2p_media_conf']      = json_decode($row['p2p_media_conf'], true);
        
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
    }
    
    function save_media_config($play_media_conf, $admin_media_conf, $p2p_media_conf, $tpl_id){
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`media_config` WHERE tpl_id = ".$tpl_id;
        if($this->db->get_var($sql)){
            $sql = "UPDATE ".DB_NAME_TPL.".`media_config` SET `play_media_conf` = '".json_encode($play_media_conf)."' ,`admin_media_conf` = '".json_encode($admin_media_conf)."' ,
            `p2p_media_conf` = '".json_encode($p2p_media_conf)."' ,`uptime` = '".time()."' WHERE `tpl_id` = '".$tpl_id."'; ";
        }else{
            $sql = "INSERT INTO ".DB_NAME_TPL.".`media_config` (`play_media_conf`, `admin_media_conf`, `p2p_media_conf`, `uptime`, `tpl_id`)
             VALUES ('".json_encode($play_media_conf)."', '".json_encode($admin_media_conf)."', '".json_encode($p2p_media_conf)."', '".time()."', '".$tpl_id."');";
        }
        
        if($this->db->query($sql)){
            return array("Flag"=>100, "FlagString"=>"保存成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"保存更新失败");
        }
    }
    
    function cate_list($type, $tpl_id){
        $sql         = "SELECT id,cate_id FROM ".DB_NAME_TPL.".`tpl_config_cate` WHERE tpl_id = ".$tpl_id." AND `type` = ".$type;
        $row         = $this->db->get_row($sql, "ASSOC");
        $cate_id_arr = (array)json_decode($row['cate_id'], true);
        
        if($type == 1){
            $sql   = "SELECT cate_id, cate_name FROM ".DB_NAME_REGION.".tbl_expressiontype";
        }elseif($type == 2){
            $sql   = "SELECT parent_id AS cate_id, `name` AS cate_name FROM ".DB_NAME_REGION.".tbl_stamptype";
        }
        $all_cate   = $this->db->get_results($sql, "ASSOC");
        
        $select_cate = array();
        foreach($all_cate as $one){
            if(!in_array($one['cate_id'], $cate_id_arr)){
                continue;
            }
            $select_cate[] = $one;
        }
        
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>array("select_cate"=>$select_cate, "all_cate"=>$all_cate, "id"=>$row['id']));
    }
    
    function cate_save($id, $cate_id, $type, $tpl_id){
        if($id){
            return $this->cate_update($id, $cate_id);
        }else{
            return $this->cate_add($cate_id, $type, $tpl_id);
        }
    }
    
    private function cate_update($id, $cate_id){
        $sql = "UPDATE ".DB_NAME_TPL.".`tpl_config_cate` SET `cate_id` = '".json_encode($cate_id)."' WHERE `id` = '".$id."'; ";
        if($this->db->query($sql)){
            return array("Flag"=>100, "FlagString"=>"保存成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"保存更新失败");
        }
    }
    
    private function cate_add($cate_id, $type, $tpl_id){
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_TPL.".`tpl_config_cate` WHERE `type` = '".$type."' AND tpl_id = ".$tpl_id;
        if($this->db->get_var($sql)){
            return array("Flag"=>102, "FlagString"=>"操作错误");
        }
        
        $sql = "INSERT INTO ".DB_NAME_TPL.".`tpl_config_cate` (`type`, `cate_id`, `tpl_id`) VALUES ('".$type."', '".json_encode($cate_id)."', '".$tpl_id."'); ";
        if($this->db->query($sql)){
            return array("Flag"=>100, "FlagString"=>"保存成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"保存更新失败");
        }
    }
    
    private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
			$page = new extpage(array (
					'total' => $total,
					'perpage' => $perpage
			));
			$pageArr['page'] = $page->show();
			$pageArr['limit'] = $page->limit();
			unset ($page);
		}
		return $pageArr;
	}
 }