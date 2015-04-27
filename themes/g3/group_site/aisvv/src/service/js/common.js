//发布微博表单下图片 音乐 视频等工具按钮
function showtool(id){
	var tools = new Array('add_pic','add_mus','add_vid');
	for(i=0; i<tools.length;i++){
		if(tools[i] !== id){
			document.getElementById(tools[i]).style.display = 'none';
		}
	}
	showid(id);
}

//添加图片 音乐 视频完成
function addOK(divid,addStr){
	showid(divid);
	tbody = document.getElementById('tbody');
	if(tbody.value == ''){
		initInput(tbody);
		tbody.value = addStr;
	}
}

//取消添加图片 音乐 视频
function addCancel(divid,valid){
	showid(divid);
	document.getElementById(valid).value = '';
}

function move(id){document.getElementById(id).style.display = 'block';}
function moveout(id){document.getElementById(id).style.display = 'none';}

//检查图片地址
function checkPic(obj){
	var upimg = document.getElementById('upimg');
	var check = new UpLoadFileCheck(); 
	check.IsImg = true;
	check.AllowImgFileSize = 1024;
	if( ! check.CheckExt(obj)){
		upimg.value = '';
	}
}

//检查MP3
function checkMp3(obj){
	/*var preg = /^http[s]?:\/\/.*\.mp3$/i;
	if( ! obj.value.match(preg)){
		alert('Mp3地址不正确,请输入正确的Mp3地址如:http://g3.kkyoo.com/music/ok.mp3');
		obj.value = '';
	}*/
}

function checkVideo(obj){
	var preg = /^http[s]?:\/\/.*\.swf.*/i;
	if( ! obj.value.match(preg) || !obj.value){
		alert('视频地址不正确,请输入正确的视频地址如:http://player.youku.com/player.php/Type/Folder/Fid/21472641/Ob/1/sid/XNjUzNTc0MzE2/v.swf');
		obj.value = '';
	}
}

//显示视频缩略图,必须在页面加载完后执行
var videos = $('.tvideo');
var videonum = videos.length;
for(i=0; i<videonum; i++){
	var data = videos[i].title.split('|');
	if(data.length > 1){
		videos[i].innerHTML = '<img src="'+data[0]+'" width="130" height="100" onclick="playVideo(this.parentNode)" />';
	}
}

//播放视频
function playVideo(obj){
	var data = obj.title.split('|');
	if(data.length > 1)
	obj.innerHTML = '<a href="javascript:void(0)" title="点击收起视频" onclick="hideVideo(this.parentNode)">收起视频</a><br><embed height="400" width="480" align="middle" type="application/x-shockwave-flash" allowscriptaccess="always" quality="high" flashvars="playMovie=true&isAutoPlay=true&auto=1&autoPlay=true&adss=0" allowfullscreen="true" src="'+data[1]+'"></embed>';

}

//收起视频
function hideVideo(obj){
	var data = obj.title.split('|');
	if(data.length > 1)
	obj.innerHTML = '<img src="'+data[0]+'" width="130" height="100" onclick="playVideo(this.parentNode)" />';
}

//点击图片放大或缩小
function thumb(obj){
	var lang = obj.lang;
	obj.lang = obj.src;
	obj.src = lang;
}

//发表微博的文本框初始化
function initInput(obj){
	initSmileys('smileys_list','tbody');
	//obj.style.height = '120px';
}

//回复微博文本框初始化
function initReInput(id){
	showid('relay-'+id);
	initSmileys('smileys-'+id,'tbody-'+id);
}

//显示隐藏ID
function showid(id){
	var id = document.getElementById(id);
	if(id.style.display == 'none' || id.style.display == ''){
		id.style.display = 'block';
	}else{
		id.style.display = 'none';
	}
}

//获取textarea的焦点并插入内容
function insertInFocus(id, str) {
		var obj = document.getElementById(id);
		obj.focus();
		if (document.selection) {
		var sel = document.selection.createRange();
		sel.text = str;
	} else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
		var startPos = obj.selectionStart,
		endPos = obj.selectionEnd,
		cursorPos = startPos,
		tmpStr = obj.value;
		obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
		cursorPos += str.length;
		obj.selectionStart = obj.selectionEnd = cursorPos;
	} else {
		obj.value += str;
	}
}

//初始化表情
function initSmileys(id,addInid){
	id = document.getElementById(id);
	if(id.innerHTML != '') return;
	var addInid = "'"+addInid+"'";
	var smileNum = 22;
	//var smileUrl = 'http://test.vvku.com/kkyoo/themes/g3/member/images/smileys/';
	var smileUrl = mysmile+'service/images/smileys/';
	for(i=1; i<= smileNum; i++){
		ubb = "'(:"+i+")'";
		id.innerHTML += '<img onclick="insertInFocus('+addInid+','+ubb+')" src="'+smileUrl+i+'.gif" /> ';
	}
}

