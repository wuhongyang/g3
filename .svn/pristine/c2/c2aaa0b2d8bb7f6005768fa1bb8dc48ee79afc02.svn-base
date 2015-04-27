function user_create(){
	var passid,name,cluster_id,group_id,user_status,_url,_param,_resultInfo,_msg;
	passid   		= $$('passid').item(0).value;
	passname   		= $$('passname').item(0).value;
	cluster_id   	= $$('cluster_id').item(0).value;
	user_status	= getRadio('user_status');
	group_id	= $$('group_id').item(0).value;
	pid	= $$('pid').item(0).value;
	if(!isInteger(passid)){
		alert('请输入合法的ID！');
		$$('passid').item(0).focus();
		$$('passid').item(0).select();
		return false;
	}
	else if(!isInteger(cluster_id) ||　cluster_id <= 0){
		alert('请选择权限组分类！');
		return false;
	}else if(!isName(passname)){
		alert('请输入姓名！');
		return false;
	}
	else if(!isInteger(group_id) ||　group_id <= 0){
		alert('请选择权限组！');
		return false;
	}
	else{
		action = 'post';
		module = 'user_create';
		if(pid > 0){
			action = 'editor';
			module = 'user_editor';
		}
		_url 	= 'level.php?module='+module+'&action='+action+'&pid='+pid;
		_param 	= 'passid='+passid+'&passname='+passname+'&cluster_id='+cluster_id+'&group_id='+group_id+'&status='+user_status;
		_resultInfo	= '';
		$$('btnSubmit').item(0).disabled = false;
		Ajax2('',_url,function callback(result){showResult(result.responseText,_resultInfo);},_param);
	}
	return false;
}

function sysuser_modify(){
	var group_name,CId,levels,_url,_param,_resultInfo,_msg,id;
	group_name   		= $$('user').item(0).value;
	CId   		= $$('CId').item(0).value;
	id   		= $$('id').item(0).value;
  	levels		= getCheckBox('levels');
	if (levels == '') levels = 0;
	if(!isName(group_name)){
		alert('请输入合法的名称！');
		$$('user').item(0).focus();
		$$('user').item(0).select();
		return false;
	}else if(!isInteger(CId)){
		alert('请选择权限组分类！');
		return false;
	}
	else{
		module = 'group_create';
		if(id > 0){
			module = 'group_editor';
		}
		_url 	= 'admin.php?module='+module+'&action=post';
		_param 	= 'group_name='+group_name+'&levels='+levels+'&CId='+CId+'&GId='+id;
		_resultInfo	= '';
		$$('btnSubmit').item(0).disabled = false;
		Ajax2('',_url,function callback(result){showResult(result.responseText,_resultInfo);},_param);
	}
	return false;
}

function user_modify(){
	var userID,user,name,level,sublevel,listlevel,_url,_param,_resultInfo,_msg;
	userID		= $$('userID').item(0).value;
	user   		= $$('user').item(0).value;
	name   		= $$('name').item(0).value;
  	level		= checkCheckBox('level');
	sublevel	= checkCheckBox('sublevel');
	listlevel	= checkCheckBox('listlevel');
	if (level == '') level = 0;
	if (sublevel == '') sublevel = 0;
	if (listlevel == '') listlevel = 0;
	if(!isName(user)){
		alert('请输入合法的用户名！');
		$$('user').item(0).focus();
		$$('user').item(0).select();
		return false;
	}
	else if(!isName(name)){
		alert('请输入真实姓名！');
		$$('name').item(0).focus();
		$$('name').item(0).select();
		return false;
	}
	else{
		_url 	= 'admin.php?module=user_modify&action=modify';
		_param 	= 'userID='+userID+'&user='+user+'&name='+name+'&level='+level+'&sublevel='+sublevel+'&listlevel='+listlevel;
		_resultInfo	= [{"id":100,"url":"admin.php?module=user_list","msg":"[提示]：修改权限，操作成功！"},{"id":101,"url":"admin.php?module=user_list","msg":"[提示]：修改权限，操作失败！"}];
		$$('btnSubmit').item(0).disabled = false;
		Ajax2('',_url,function callback(result){showResult(result.responseText,_resultInfo);},_param);
	}
	return false;
}