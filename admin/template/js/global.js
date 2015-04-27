function $(id){
	if(id != ''){
		return document.getElementById(id);	
	}
}

function $$(name){
	if(name != ''){
		return document.getElementsByName(name);
	}
}

//把json字串转成json对象
function str2obj(str){
	var obj;
	try{
		obj = eval('('+ str +')');
	}catch(e){
		obj = {};
	}
	return obj;
}

/**
* 加载四级科目联动菜单
* @param integer param 科目id参数(bigcase=1)
* @param string dom HTMLselect ID名
* @param integer selectid 默认选中的科目
*/
function getOptions(param,dom,selectid){
	if(typeof(selectid)=='undefined'){
		selectid = 0;
	}
	new Ajax().ajaxRequest('','/admin/ccs.php','module=ajax&'+param,'get',true,
		function (result){
			var data = eval('('+result.responseText+')');
			var opt = document.getElementById(dom);
			opt.options.length = 0;
			opt.options.add(new Option('请选择',0));
			for(i=0;i<data.length;i++){
				var newopt = new Option(data[i].name,data[i].id);
				if(selectid==data[i].id) newopt.selected = true;
				opt.options.add(newopt);
			}
		}
	);
}

function getCmdPath(dom){
	var sel = document.getElementById("parent");
	sel.onchange = function(){
		var bcObj = document.getElementById("bigcase");
		var bigCaseId = bcObj.options[bcObj.selectedIndex].value;
		var caseObj = document.getElementById("case");
		var caseId = caseObj.options[caseObj.selectedIndex].value;
		var parentObj = document.getElementById("parent");
		var parentId = parentObj.options[parentObj.selectedIndex].value;
		/*
		new Ajax().ajaxRequest('','/admin/ccs/business.php','module=ajax&'+param,'get',true,function(data){
			document.getElementById(dom).value = data;
		});
		*/
	}
}

//图片分类
function showCategory(option_id,selectedId){
	var catElm = document.getElementById(option_id);
	catElm.options.length = 0;
	catElm.options.add(new Option('请选择图片分类','-1'));
	for(var key in cat){
		catElm.options.add(new Option(cat[key].cat_name,cat[key].id));
	}
	if(selectedId){
		for(var i=0; i<catElm.options.length; i++){
			if(selectedId == catElm.options[i].value){
				catElm.options[i].selected = true;
			}
		}
	}
}

//图片
function showPic(option_id,catId,selectedId){
	var picElm = document.getElementById(option_id);
	
	picElm.options.length = 0;
	picElm.options.add(new Option('请选择图片','-1'));
	for(var key in pic){
		if(catId == pic[key].cat_id)
			picElm.options.add(new Option(pic[key].pic_name,pic[key].id));
	}
	
	if(picElm.options.length <= 1){
		picElm.options.length = 0;
		picElm.options.add(new Option('分类下没有图片','-2'));
	}
	if(selectedId){
		for(var i=0; i<picElm.options.length; i++){
			if(selectedId == picElm.options[i].value){
				picElm.options[i].selected = true;
			}
		}
	}
}

function clears(){
	var len = arguments.length;
	if(len <= 0)	return;
	for(var i=0; i<len; i++){
		var obj = document.getElementById(arguments[i]);
		if(obj==null || obj==undefined)	continue;
		obj.options.length = 0;
		obj.options.add(new Option('请选择',0));
	}
}

function checkForm(param,purl,info){
	var eparam = eval(param);
	var eparamLen = eparam.length;
	var param = '';
	var revalue = '';
	for(var i=0;i<eparamLen;i++){
		if(eparam[i][2] == 'radio'){
			try{
				revalue = getRadio(eparam[i][0]);
			} catch(e){
				alert(e.name + ": " + e.message + "\n\nDOM: " + eparam[i][0]);
			}
		}
		else if(eparam[i][2] == 'checkbox'){
			try{
				revalue = getCheckBox(eparam[i][0]);
			} catch(e){
				alert(e.name + ": " + e.message + "\n\nDOM: " + eparam[i][0]);
			}
		}
		else if(eparam[i][2] == 'textarea'){
			try{
				revalue = encodeURIComponent($(eparam[i][0]).value);
			} catch(e){
				alert(e.name + ": " + e.message + "\n\nDOM: " + eparam[i][0]);
			}
		}
		else {
			try{
				revalue = encodeURIComponent($$(eparam[i][0]).item(0).value);
			} catch(e){
				alert(e.name + ": " + e.message + "\n\nDOM: " + eparam[i][0]);
			}
		}
		if(!checkValue(revalue,eparam[i][2])){
			alert(eparam[i][1]);
			$$(eparam[i][0]).item(0).focus();
			return false;
		}
		param += '&'+eparam[i][0]+'='+revalue;
	}
	new Ajax().ajaxRequest('',purl,param.substr(1,param.length),'post',true,function callBack(result){showResult(result.responseText,eval(info));});
	return false;
}

function checkValue(value,type){
	var result = '';
	switch(type){
		case 'hasAccount' :
			result = hasAccountChar(value);
			break;
		case 'hasPassWord' :
			result = hasPassWordChar(value);
			break;
		case 'integer' :
			result = isInteger(value);
			break;
		case 'float' :
			result = isFloat(value);
			break;
		case 'string' :
		case 'textarea' :
			result = !isEmpty(value);
			break;
		case 'html' :
			result = !isEmpty(value);
			break;
		case 'radio' :
			result = isInteger(value);
			break;
		case 'date' :
			result = isDateChar(value);
			break;
		case 'telphone' :
			result = (isPhone(value) || isMobile(value));
			break;
		case 'email' :
			result = isEmail(decodeURIComponent(value));
			break;
		case 'none' :
			result = true;
			break;
	}
	return result;
}

function showResult(result,info){
	var infoCount = info.length;
	var tmpI = 0;
	if (infoCount > 0){
		for (i=0;i<infoCount;i++){
			if (info[i].id == result){
				if (info[i].msg != ''){
					alert(info[i].msg);	
				}
				if (info[i].url != ''){
					window.location.replace(info[i].url);
				}
			} else {
				tmpI++;
			}
		}
		if(tmpI == infoCount){
			alert(result);
		}
	} else {
		result = eval('('+result+')');
		if(result.msg != ''){
			alert(result['FlagString']);
		}
		if(result.url){
			window.location.replace(result.url);
		}
		
	}
	return false;
}

// 获取radio所选值，返回
function getRadio(name){
	var i,obj;
	obj=$$(name);
	for (i=0;i<obj.length;i++){
		if (obj[i].checked) break;
	}
	if (i>=obj.length){
		return "NULL";
	}
	else{ 
		return obj[i].value;
	}
}

// 获取checkbox所选值以“,”分隔返回
function getCheckBox(name){
	var result,strResult;
	result 		= '';
	strResult	= '';
	for(i=0;i<$$(name).length;i++){
		if($$(name).item(i).checked){
			result += $$(name).item(i).value + ",";
		}

	}
	if (result.length > 0){
		strResult = result.substr(0,result.length-1)
	}
	return strResult;
}

//时间格式化函数
function date(fmt,timestamp){
    if(timestamp > 0){
        var d = new Date(timestamp);
    }else{
        var d = new Date();
    }
    var o = {
    "M+" : d.getMonth()+1, //月份
    "d+" : d.getDate(), //日
    "h+" : d.getHours()%12 == 0 ? 12 : d.getHours()%12, //小时
    "H+" : d.getHours(), //小时
    "m+" : d.getMinutes(), //分
    "s+" : d.getSeconds(), //秒
    "q+" : Math.floor((d.getMonth()+3)/3), //季度
    "S" : d.getMilliseconds() //毫秒
    };
    var week = {
    "0" : "\u65e5",
    "1" : "\u4e00",
    "2" : "\u4e8c",
    "3" : "\u4e09",
    "4" : "\u56db",
    "5" : "\u4e94",
    "6" : "\u516d"
    };     
    if(/(y+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, (d.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    if(/(E+)/.test(fmt)){
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "\u661f\u671f" : "\u5468") : "")+week[d.getDay()+""]);
    }
    for(var k in o){
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}

//显示分页
function showPage(total,perpage){
	var perpage = isNaN(Number(perpage)) ? 20 : perpage;
	var pagebarnum = 10;
	var nowpage = window.location.search.replace(/.*page\=?/i,'');nowpage = isNaN(Number(nowpage)) ? 1 : parseInt(nowpage,10);
	var totalpage = Math.ceil(total / perpage);
	var pagebar = '';
	var pagestr = '有<span class="num">' + total + '</span>条记录&nbsp;&nbsp;&nbsp;共<span class="num">'+totalpage+'</span>页';
	if(totalpage > 1){
		plus = Math.ceil(pagebarnum / 2);
		if (pagebarnum-plus + nowpage>totalpage){
			plus = pagebarnum-totalpage+nowpage;
		}
		begin = nowpage-plus+1;
		begin = begin >= 1 ? begin : 1;
		for(var i=begin;i<begin+pagebarnum;i++){
			if (i <= totalpage){
				if (i != nowpage){
					pagebar += '<a href="'+window.location.search.replace(/(\&)?page\=.*/,'')+'&page='+i+'">'+i+'</a>';
				}else{
					pagebar += '<span class="curr">'+i+'</span>';
				}
			}else{
				break;
			}
			pagebar += "\n";
		}
		if (nowpage - 5 < 1){
			first_page = '';
		}else{
			first_page = '<a href="'+window.location.search.replace(/(\&)?page\=.*/,'')+'&page=1'+'">1</a>&nbsp;...&nbsp;';
		}
		if (nowpage + 5 >= totalpage){
			last_page = '';
		}else{
			last_page = '&nbsp;...&nbsp;<a href="'+window.location.search.replace(/(\&)?page\=.*/,'')+'&page='+totalpage+'">'+totalpage+'</a>';
		}
		pagestr += first_page+pagebar+last_page;
	}
	return pagestr;
}

//显示分页
function showPage2(nowpagecount,perpage){
	var perpage = isNaN(Number(perpage)) ? 20 : perpage;
	var nowpage = window.location.search.replace(/.*page\=?/i,'');nowpage = isNaN(Number(nowpage)) ? 1 : parseInt(nowpage,10);
	var prev = nowpage < 1 ? 1 : nowpage-1;
	var next = nowpagecount < perpage ? nowpage : nowpage+1;
	var pagestr = '当前第<span class="num">'+nowpage+'</span>页&nbsp;&nbsp;';
	pagestr += '<a href="'+window.location.search.replace(/(\&)?page\=.*/,'')+'&page='+prev+'">上一页</a>&nbsp;&nbsp;';
	pagestr += '<a href="'+window.location.search.replace(/(\&)?page\=.*/,'')+'&page='+next+'">下一页</a>';
	return pagestr;
}

function check_all(domID ,obj){
	var dName = domID+'[]';
	var iLen = $$(dName).length;
	for (var i = 0; i < iLen; i++)
    {
        if (obj.checked == true)
        {
            if ($$(dName).item(i).type == "checkbox" && $$(dName).item(i).disabled == false)
            {
                $$(dName).item(i).checked = true;
            }
        }
        else
        {
            if ($$(dName).item(i).type == "checkbox")
            {
                $$(dName).item(i).checked = false;
            }
        }
    }
	return false;
}


//获取radio所选值，返回
function getRadio(name){
	var i,obj;
	obj=$$(name);
	for (i=0;i<obj.length;i++){
		if (obj[i].checked) break;
	}
	if (i>=obj.length){
		return "NULL";
	}
	else{ 
		return obj[i].value;
	}
}

// 获取checkbox所选值以“,”分隔返回
function getCheckBox(name){
	var sname,result,strResult;
	sname = name+'[]';
	result 		= '';
	strResult	= '';
	for(i=0;i<$$(sname).length;i++){
		if($$(sname).item(i).checked){	
			result += $$(sname).item(i).value + ",";
		}
	}
	if (result.length > 0){
		strResult = result.substr(0,result.length-1)
	}
	return strResult;
}

//去左右空格; 
function trim(s){
 	return rtrim(ltrim(s));
}

//去左空格; 
function ltrim(s){
 	return s.replace(/(^\s*)/g,""); 
} 

//去右空格; 
function rtrim(s){ 
 	return s.replace(/(\s*$)/g,""); 
}

//空字符值; 
function isEmpty(s){
	s = trim(s); 
	return s.length == 0;
}

//Integer;
function isInteger(s){
	s = trim(s);
	var p = /^[-\+]?\d+$/;
	return p.test(s);
}

function isDateChar(s){
	var p = /^(\d{1,4})(-|\/)(\d{2})\2(\d{2})$/;
	return p.test(s);
}

//电话号码;
function isPhone(s){
	s = trim(s);
	var p = /^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/;
	return p.test(s);
}

//手机号码; 
function isMobile(s){
	s = trim(s);
	var p = /1\d{10}/;
	return p.test(s);
}

//Email;
function isEmail(s){
	s = trim(s); 
 	var p = /^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.){1,4}[a-z]{2,3}$/i;
 	return p.test(s);
}

//是姓名
function isName(name){
	var strReg  = /^[a-zA-Z0-9\(\)\_\-\（\）\u4e00-\u9fa5+]+$/;
	if (strReg.test(name)){
		return true;
	}
	else {
		return false;
	}
}

//英文字母
function isString(str){
	var strReg  = /^[a-zA-Z0-9]+$/;
	if (strReg.test(str)){
		return true;
	}
	else {
		return false;
	}
}

//是空串
function isNull(str){
	var strReg = /^.+$/;
	if (strReg.test(str)){
		return true;
	}
	else{
		return false;
	}
	
}

//是小数
function isFloat(ints){
	var intReg = /^\d+(\.\d{1,2})$/;
	if(intReg.test(ints)){
		return true;
	}
	else{
		return false;
	}
}

//是时间
function isTimes(str){
	var strReg = /^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/;
	if(strReg.test(str)){
		return true;
	}
	else{
		return false;
	}
}

//是用户名
function isUsername(username){
	var strReg = /^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){4,19}$/;
	if(strReg.test(username)){
		return true;
	}
	else{
		return false;
	}
}

//是密码
function isPassWord(str){
	var strReg = /^.{6,16}$/;
	if (strReg.test(str)){
		return true;
	}
	else{
		return false;
	}
}

//是IP
function isIP(ip){
	var reg=/^(([01]?[\d]{1,2})|(2[0-4][\d])|(25[0-5]))(\.(([01]?[\d]{1,2})|(2[0-4][\d])|(25[0-5]))){3}$/;
	if(reg.test(ip)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * obj 参数 必填
 * url 提交地址 必填
 * selectName 选择框名称 必填
 * domId dom容器ID 必填
 * selectId 当前选择项 可选
 */
function getSelect(obj,url,selectName,domId,selectId,fun){
	var _url = url;
	fun = fun == undefined ? '' : fun;
	new Ajax().ajaxRequest('',_url,obj,'get',true,function callBack(result){createSelect(result.responseText,selectName,domId,selectId,fun);});
}

function createSelect(str,selectName,domId,selectId,fun){
	var strVal,strLen,strSelect;
	strVal = eval("("+str+")");
	strSelect = '<select name="'+selectName+'" id="'+selectName+'"';
	if (fun != '') {
		param = fun.split(',');
		strSelect += ' onChange="getSelect('+param[0]+',\''+param[1]+'\',\''+param[2]+'\',\''+param[3]+'\',\''+param[4]+'\')";';
	}
	strSelect += '><option value="0">请选择</option>';
	for (var i = 0;i < strVal.length;i++){
		strSelect += '<option value="'+strVal[i].id+'"';
		if (selectId == strVal[i].id){
			strSelect += ' selected="selected"';
		}
		strSelect +='>'+strVal[i].name+'</option>';
	}
	strSelect += '</select>';
	$(domId).innerHTML = strSelect;
}

//请求AJAX
function Ajax2(domId,url,callBack,postData){
	if(typeof(postData) == 'undefined'){
		var url_parse = url.split('?');
		var url = url_parse[0];
		var parameter = url_parse[1];
		var method = 'get';
	} else{
		var parameter = postData;
		var method = 'post';
	}
	new Ajax().ajaxRequest(domId,url,parameter,method,true,callBack);
	return false;
}