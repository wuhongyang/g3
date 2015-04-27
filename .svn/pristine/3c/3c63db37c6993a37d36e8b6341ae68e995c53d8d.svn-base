<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

$view = (int)$_GET['view'];
switch($_GET['module']){
	default: //帮助中心
		$param = array(
			'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助内容列表'),
			'extparam' => array('Tag'=>'ClassifyList','Data'=>array('Type'=>1,'Status'=>1))
		);
		$typelist = request($param);
		$typelist = $typelist['Data'];
		
		foreach($typelist as $key=>$val){
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助内容列表'),
				'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>$val['id']))
			);
			$rst = request($param);
			$typelist[$key]['list'] = $rst['Data'];
			if($val['id'] == 1){
				$commonlist = $typelist[$key];
				$param = array(
					'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助内容列表'),
					'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>1,'All'=>1))
				);
				$rst = request($param);
				$commonlist['list'] = $rst['Data'];
				unset($typelist[$key]);
			}
			
		}


		if($view > 0){ //查看帮助内容
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>103,'Desc'=>'获取帮助内容'),
				'extparam' => array('Tag'=>'SubstanceDetail','Data'=>array('Id'=>$view))
			);
			$result = request($param);
			$viewdata = $result['Data'];
			/*foreach($category as $val){
				if($val['id'] == $viewdata['classify_id']){
					$curcname = $val['name'];
					break;
				}
			}*/
			$description = str_replace('&nbsp;','',preg_replace("/\s+/",'',strip_tags($viewdata['content'])));
			$descTail = strlen($description)>70 ? '……' : '';
			$template = 'view.html';
		}else{ //常见问题解答
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'常见问题解答列表'),
				'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>1))
			);
			$result = request($param);
			$firstlist = $result['Data'];
			$page = $result['Page'];
			$template = 'index.html';
		}
		break;
	
	case 'notice': //官方通告
		if($view > 0){ //查看通告内容
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>103,'Desc'=>'获取通告内容'),
				'extparam' => array('Tag'=>'SubstanceDetail','Data'=>array('Id'=>$view))
			);
			$result = request($param);
			$viewdata = $result['Data'];
			$prev = $result['Prev'];
			$next = $result['Next'];
			$template = 'notice-view.html';
		}else{ //官方通告列表
			$type = is_numeric($_GET['type'])? $_GET['type'] : 4;
			$param = array(
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10161,'ChildId'=>102,'Desc'=>'帮助内容列表'),
				'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>$type))
			);
			$result = request($param);
			$lists = $result['Data'];
			$page = $result['Page'];
			$template = 'notice-list.html';
		}
		break;
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template('help/'.$template,$tpl);