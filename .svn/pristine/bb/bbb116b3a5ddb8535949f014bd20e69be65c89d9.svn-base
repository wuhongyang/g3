<?php
class Custom extends dlhelper
{
	function getPhiz($uin){
		$category = $this->db->get_results("SELECT cid,uin,name FROM phiz_category WHERE uin=0 OR uin={$uin} AND flag=1 ORDER BY uin ASC",'ASSOC');
		foreach($category as $k=>$v){
			$phizs = $this->db->get_results("SELECT id,img,descr FROM phiz_images WHERE cid={$v['cid']} AND flag=1",'ASSOC');
			foreach($phizs as $phiz){
				$category[$k]['images'][$phiz['id']] = cdn_url(PIC_API_PATH.'/p/'.$phiz['img'].'/500/0.jpg');
			}
		}
		return empty($category)?  array('Flag'=>101) : array('Flag'=>100,'phiz'=>$category);
	}

	function addPhiz($uin,$cid,$descr){
		if($uin <= 0) return array('Flag'=>102,'FlagString'=>'UIN不正确');
		$data = json_decode(httpPOST(PIC_SAVE_API_PATH,array()),TRUE);
		if($data['Flag']==100){
			$rst = $this->insert('phiz_images',array('uin'=>$uin,'cid'=>$cid,'img'=>$data['File'],'descr'=>$descr));
			$data = $rst? array('Flag'=>100) : array('Flag'=>101,'FlagString'=>'写入失败');
		}
		return $data;
	}

	function delPhiz($ids,$uin){
		$ids = implode(',',(array)$ids);
		$rst = $this->update('phiz_images',array('flag'=>0),"id IN({$ids}) AND uin={$uin}");
		return $rst? array('Flag'=>100) : array('Flag'=>101);
	}

	function addPhizCategory($name,$uin=0){
		$cid = $this->insert('phiz_category',array('uin'=>$uin,'name'=>$name));
		return ($cid > 1)? array('Flag'=>100,'cid'=>$cid) : array('Flag'=>101,'FlagString'=>'分类创建失败');
	}

	function delPhizCategory($cid,$uin){
		$rst = $this->update('phiz_category',array('flag'=>0),"cid={$cid} AND uin={$uin}");
		return $rst? array('Flag'=>100) : array('Flag'=>101);
	}
}