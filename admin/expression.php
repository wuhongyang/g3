<?php
include_once('../library/global.fun.php');

$link_array = getLevellink(10002, 10002, 10444, 101);
$module = trim($_GET['module']);
switch($module){
	default:
	case 'list':
		if(!$_GET['cate_id']){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
					'extparam' => array('Tag'=>'ExpressionCateList','NoPage'=>false)
			);
			$rst = request($param);
			$list = $rst['List'];
			$page = $rst['Page'];
			$tpl_name = "cate_list.html";
		}else{
			$parent_id = intval($_GET['cate_id']);
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
					'extparam' => array('Tag'=>'ExpressionList','CateId'=>$_GET['cate_id'])
			);
			$rst = request($param);
			$list = $rst['List'];
			$cate_name = $rst['CateName'];
			$page = $rst['Page'];
			$tpl_name = "expression_list.html";
		}
		break;
	case 'info':
		$pic_mine = ",image/bmp,image/cgm,image/cis-cod,image/example,image/fits,image/fif,image/g3fax,image/gif,image/ief,image/ifs,image/j2k,image/jp2,image/jpeg,image/jpm,image/jpx,image/ktx,image/naplps,image/nbmp,image/pict,image/pipeg,image/pjpeg,image/png,image/prs.btif,image/prs.pti,image/si6,image/svg+xml,image/svh,image/t38,image/tiff,image/tiff-fx,image/toy,image/vnd,image/vnd.adobe.photoshop,image/vnd.cns.inf2,image/vnd.dece.graphic,image/vnd.djvu,image/vnd.dvb.subtitle,image/vnd.dwg,image/vnd.dxf,image/vnd.fastbidsheet,image/vnd.fpx,image/vnd.fst,image/vnd.fujixerox.edmics-mmr,image/vnd.fujixerox.edmics-rlc,image/vnd.globalgraphics.pgb,image/vnd.lgtwap.sis,image/vnd.microsoft.icon,image/vnd.mix,image/vnd.ms-modi,image/vnd.net-fpx,image/vnd.nok-oplogo-color,image/vnd.radiance,image/vnd.rn-realflash,image/vnd.rn-realpix,image/vnd.sealed.png,image/vnd.sealedmedia.softseal.gif,image/vnd.sealedmedia.softseal.jpg,image/vnd.stiwap.sis,image/vnd.svf,image/vnd.wap.wbmp,image/vnd.xiff,image/wavelet,image/x-cals,image/x-cmu-raster,image/x-cmx,image/x-dcx,image/x-eri,image/x-fpx,image/x-freehand,image/x-icon,image/x-jps,image/x-macpaint,image/x-pcx,image/x-pda,image/x-pict,image/x-portable-anymap,image/x-portable-bitmap,image/x-portable-graymap,image/x-portable-pixmap,image/x-quicktime,image/x-rgb,image/x-up-wpng,image/x-xbitmap,image/x-xpixmap,image/x-xwindowdump,application/x-shockwave-flash,image/x-png,";
		if($_GET['cate_edit']){
			if($_GET['cate_id']){
				$param = array(
						'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
						'extparam' => array('Tag'=>'ExpressionCateGet','CateId'=>intval($_GET['cate_id']))
				);
				$rst = request($param);
				$one = $rst['Row'];
			}
			$tpl_name = "add_cate.html";
			break;
		}elseif($_GET['expression_add']){
			if($_POST){
				foreach($_POST['expression_name'] as $k=>$v){
					if(!$v || !$_FILES['img']['name'][$k]){
						ShowMsg("不能为空", -1);
					}
					if(strpos($pic_mine, ",".$_FILES['img']['type'][$k].",") === false){
						ShowMsg("文件类型错误", -1);
					}
				}
				foreach($_POST['expression_name'] as $k=>$v){
					$bytes = file_get_contents($_FILES['img']['tmp_name'][$k]);
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$_POST['expression_name'][$k] = htmlspecialchars(addslashes($v));
					$_POST['expression_img_path'][$k] = $index;
				}
				$param = array(
						'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>102),
						'extparam' => array('Tag'=>'ExpressionAdd','Data'=>$_POST,'CateId'=>$_GET['cate_id'])
				);
				$rst = request($param);
				if($rst['Flag'] == 100){
					ShowMsg("添加成功", "/admin/expression.php?module=list&cate_id=".$_GET['cate_id']);
				}else{
					ShowMsg($rst['FlagString'], -1);
				}
			}
			$tpl_name = "add_expression.html";
		}elseif($_GET['expression_edit']){
			if($_POST){
				if(!$_POST['img_name']){
					ShowMsg("名字不能为空", -1);
				}
				$index = "";
				if($_FILES['img']['type']){
					if(strpos($pic_mine, ",".$_FILES['img']['type'].",") === false){
						ShowMsg("文件类型错误", -1);
					}
					
					$bytes = file_get_contents($_FILES['img']['tmp_name']);
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$stamp_name = htmlspecialchars(addslashes($_POST['stamp_name']));
				}
				
				$param = array(
						'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
						'extparam' => array('Tag'=>'ExpressionSave','Id'=>intval($_POST['id']),'ImgName'=>$_POST['img_name'],'ImgPath'=>$index)
				);
				$rst = request($param);
				if($rst['Flag'] == 100){
					ShowMsg("修改成功", "/admin/expression.php?module=list&cate_id=".$_GET['cate_id']);
				}else{
					ShowMsg($rst['FlagString'], -1);
				}
			}
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>101),
					'extparam' => array('Tag'=>'ExpressionGet','ExpressionId'=>$_GET['expression_id'])
			);
			$rst = request($param);
			$one = $rst['Row'];
			$tpl_name = "edit_expression.html";
		}elseif($_GET['expression_del']){
			$param = array(
					'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>102),
					'extparam' => array('Tag'=>'ExpressionDel','Id'=>intval($_GET['id']))
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				ShowMsg("删除成功", "/admin/expression.php?module=list&cate_id=".$_GET['cate_id']);
			}else{
				ShowMsg($rst['FlagString'], -1);
			}
		}
		break;
	case 'cate_save':
		$post = $_POST;
		$post['status'] = intval($post['status']);
		$post['cate_id'] = intval($post['cate_id']);
		$post['bigcase_id'] = intval($post['bigcase_id']);
		$post['case_id'] = intval($post['case_id']);
		$post['parent_id'] = intval($post['parent_id']);
		$post['cate_name'] = htmlspecialchars(addslashes(trim($post['cate_name'])));
		if($post['cate_name'] == ""){
			echo json_encode(array("Flag"=>101, "FlagString"=>"名字不能为空"));
			exit;
		}
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10444,'ChildId'=>102),
				'extparam' => array('Tag'=>'ExpressionCateSave','Data'=>$post)
		);
		$rst = request($param);
		echo json_encode($rst);
		exit;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('expression/'.$tpl_name,$tpl);