<?php if (!class_exists('template')) die('Access Denied');?>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/plugin/lazyload/lazyload.min.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/plugin/slideBox/js/jquery.slideBox.min.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bootstrap/js/bootstrap.min.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/modules/rooms/js/rooms.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/resource/js/common.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/resource/js/placeHolder.js');;?>"></script>
<script type="text/javascript" src="/group_site/jscdn.php"></script>

<script type="text/javascript">
var groupId='<?php echo $groupId;?>';
var groupName='<?php echo $groupInfo["name"];?>';
var groupNameCode='<?php echo urlencode($groupInfo["name"]);;?>';
var Uin=0;
var myFocus='';
var myFocusNum=0;
var myCollect='';
var myCollectNum=0;
var myHostory='';
var removeVip=false;
var removeRoom=false;
var attentionUser='<?php echo $setting["my_attention"]["attention_user"]["is_open"];?>';
var attentionUserName='<?php echo $setting["my_attention"]["attention_user"]["name"];?>';
var collectionRoom='<?php echo $setting["my_attention"]["collection_room"]["is_open"];?>';
var collectionRoomName='<?php echo $setting["my_attention"]["collection_room"]["name"];?>';
var myFootprint='<?php echo $setting["my_attention"]["my_footprint"]["is_open"];?>';
var myFootprintName='<?php echo $setting["my_attention"]["my_footprint"]["name"];?>';
var richRankRuleId='<?php echo $groupExtInfo["richRankRuleId"]["value"];?>';
var artistRankRuleId='<?php echo $groupExtInfo["artistRankRuleId"]["value"];?>';
var backUrl='<?php echo $back_url;?>';
var roomTabName='';
var vipTabName='';
var messageLength='<?php echo count($messageList["messageList"]);;?>';
var roomListJson=$.parseJSON('<?php echo $roomListJson;?>');
var artistListJson=$.parseJSON('<?php echo $artistListJson;?>');

$(function(){
    $(".body-main .img").lazyload({skip_invisible:false,failure_limit:20,effect:"fadeIn"});
	$('#slideBox').slideBox({
		duration : 0.3,//滚动持续时间，单位：秒
		easing : 'linear',//swing,linear//滚动特效
		delay : 5,//滚动延迟时间，单位：秒
		hideClickBar : false,//不自动隐藏点选按键
		clickBarRadius : 10
	});
	$('select[name="province"]').change(function(){
        var province_id = $(this).val();
        _display_cities(province_id);
    });
	var oForm1Inputs = document.getElementsByTagName('input');
	for(var i=0;i<oForm1Inputs.length;i++){
		placeHolder(oForm1Inputs[i],true);
	}
	$(".room").hover(
	  function(){
		  $(this).addClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").show();
		  },
	  function(){
		  $(this).removeClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").hide();
		  }
	);
});
//判断登陆
$.ajax({
	url: '/rooms/ajax_info.php',
	type: 'POST',
	data: {"Tag":"GetLoginUser","GroupId":groupId},
	success: function(data){
		json = $.parseJSON(data);
		if(json.Flag == 100){
			//关注
			$.ajax({
				url: '/rooms/ajax_info.php',
				type: 'POST',
				data: {Tag:"GetFollow"},
				success: function(data){
					var json = $.parseJSON(data);
					if(json.Flag!=100){
						myFocus=new Object();
						myFocusNum=0;
					}
					else{
						myFocus=json.userList;
						myFocusNum=json.total;
					}
					if(attentionUser=='1'){
						$("#focus_num").html(myFocusNum);
						$('.collectinfo').show();
					}
					listen_user('middle');
				}
			});
			//房间收藏
			$.ajax({
				url: '/rooms/ajax_info.php',
				type: 'POST',
				data: {Tag:"GetCollect"},
				success: function(data){
					var json = $.parseJSON(data);
					myCollect=json.roomList;
					myCollectNum=json.total;
					if(collectionRoom=='1'){
						$('#collect_num').html(myCollectNum);
						$('.collectinfo').show();
					}
					user_collect_room('middle');
				}
			});
			//我的脚印
			if(myFootprint=='1'){
				$.ajax({
					url: '/rooms/footprint.php',
					type: 'GET',
					data: {"module":"getHistoryAccess","GroupId":groupId},
					success: function(data){
						var html = '';
						myHostory = $.parseJSON(data);
						$('#history_num').html(myHostory.length);
						$('.collectinfo').show();
					}
				});
			}
			//艺人等级艺人积分
			if(artistRankRuleId!=''){
				$.ajax({
					url: '/rooms/ajax_info.php',
					type: 'POST',
					data: {Tag:"GetUserGroupPoint",GroupId:groupId,Uin:json.Uin,Ruleid:artistRankRuleId},
					success: function(data){
						var json = $.parseJSON(data);
						if(json.Flag==100&&json.RolesImg){
							var rank=(json.Weight-json.ThisLevelWeight)/(json.NextLevelWeight-json.ThisLevelWeight);
							rank=parseInt(rank*100);
							var Diff=json.Diff;
							if(!Diff){
								Diff=0;
							}
							$('#artistRank').css({"width":rank+'%'});
							$('#artistRankImg').attr('src',json.RolesImg);
							$('#artistRankImg').attr('alt','距下一等级还差'+Diff+'积分');
							$('#artistRankImg').attr('title','距下一等级还差'+Diff+'积分');
							$('#artistRank').parent('.progress').attr('alt','距下一等级还差'+Diff+'积分');
							$('#artistRank').parent('.progress').attr('title','距下一等级还差'+Diff+'积分');
						}
						else{
							$('#artistRankBox').hide();
						}
					}
				});
			}
		    //富豪等级富豪积分
			if(richRankRuleId!=''){
				$.ajax({
					url: '/rooms/ajax_info.php',
					type: 'POST',
					data: {Tag:"GetUserGroupPoint",GroupId:groupId,Uin:json.Uin,Ruleid:richRankRuleId},
					success: function(data){
						var json = $.parseJSON(data);
						if(json.Flag==100&&json.RolesImg){
							var rank=(json.Weight-json.ThisLevelWeight)/(json.NextLevelWeight-json.ThisLevelWeight);
							rank=parseInt(rank*100);
							var Diff=json.Diff;
							if(!Diff){
								Diff=0;
							}
							$('#richRank').css({"width":rank+'%'});
							$('#richRankImg').attr('src',json.RolesImg);
							$('#richRankImg').attr('alt','距下一等级还差'+Diff+'积分');
							$('#richRankImg').attr('title','距下一等级还差'+Diff+'积分');
							$('#richRank').parent('.progress').attr('alt','距下一等级还差'+Diff+'积分');
							$('#richRank').parent('.progress').attr('title','距下一等级还差'+Diff+'积分');
						}
						else{
							$('#richRankBox').hide();
						}
					}
				});
			}
			
			//展示头部
			var h_html = '欢迎您 <a class="yellow" href="/service/profile.php" style="color: #F0B45E;">' + json.Nick + '</a>';
			h_html += '<a href="/service/loginout.php?back='+backUrl+'" class="gray">退出</a>';
			h_html += '<a href="/rooms/shortcut.php?group_id='+groupId+'&title='+groupNameCode+'">+收藏至桌面</a>';
			$('#J_head').html(h_html);
			Uin=parseInt(json.Uin);

			//右部登录信息
			var html = $('#J_login_info').html().replace('{*uin*}',json.Uin).replace('{*name*}',json.Nick).replace('{*money*}',Math.floor(json.Money)).replace('{*voucher*}', Math.floor(json.Voucher));
			$('#J_login_info').html(html);
			$("#userimgurl").attr("src",json.Face70);
			
			if(json.ChannelType){
				if(json.ChannelType == 8){
					$("#J_role").css({"background":"#a50505","border":"none"});
					$('#J_mail').css('cursor','pointer');
				}else if(json.ChannelType == 9){
					$("#J_role").css({"background":"#909","border":"none"});
					$('#J_mail').css('cursor','pointer');
				}else if(json.ChannelType == 15){
					$("#J_role").css({"background":"#ff339a","border":"none"});
					$('#J_mail').css('cursor','pointer');
				}
			}

            $('#J_unread').html(json.Count)
			
            if(json.Openid){
                if(json.Phone){
					$('#J_phone').attr('class','home-icon home-phone-active');
                    $('#J_phone').attr('title','手机已认证');
                }else{
					$('#J_phone').attr('class','home-icon home-phone');
                    $('#J_phone').attr('title','手机未认证');
                    $('#J_phone').attr('href','/service/safe_setting.php?module=phone');
                    $('#J_phone').css('cursor','pointer');
					$('#J_phone_text').html('<a href="/service/safe_setting.php?module=phone">手机认证</a>');
                }
                if(json.Email){
					$('#J_mail').attr('class','home-icon home-mail-active');
                    $('#J_mail').attr('title','邮箱已认证');
                }else{
					$('#J_mail').attr('class','home-icon home-mail');
                    $('#J_mail').attr('title','邮箱未认证');
                    $('#J_mail').attr('href','/service/safe_setting.php?module=email');
                    $('#J_mail').css('cursor','pointer');
					$('#J_mail_text').html('<a href="/service/safe_setting.php?module=email">邮箱认证</a>');
                }
            }
			$('#J_login').hide();
			$('#J_login_info').show();
		}
	}
});

function listen_user(target){
	var uins = $('#'+target).find('p.listen');
	if(uins.length>0){
		for(var i=0; i<uins.length; i++){
			if(Uin==uins[i].innerHTML){
				continue;
			}
			if(myFocus[uins[i].innerHTML]){
				if(removeVip){
					str = '<a class="attention" href="javascript:void(0);" onclick="delete_my_focus('+uins[i].innerHTML+',true);">取消关注</a>';
				}
				else{
					str = '<a class="attention" href="javascript:void(0);" onclick="delete_my_focus('+uins[i].innerHTML+');">取消关注</a>';
				}
			}
			else{
				str = '<a class="attention" href="javascript:void(0);" onclick="add_my_focus('+uins[i].innerHTML+');">+关注</a>';
			}
			$("#J_"+uins[i].id).html(str);
		}
		if(removeVip){
			removeVip=false;
		}
	}
}

function add_my_focus(uin){
	var result=false;
	var result=addFocus(uin,false);
	if(result){
		myFocus[uin]=artistListJson[uin];
		myFocusNum++;
		str = '<a class="attention" href="javascript:void(0);" onclick="delete_my_focus('+uin+');">取消关注</a>';
		$('.J_'+uin).html(str);
		$('#focus_num').html(myFocusNum);
	}
}

function delete_my_focus(uin,hide){
	var result=false;
	var result=cancelFocus(uin,false);
	if(result){
		delete myFocus[uin];
		myFocusNum--;
		str = '<a class="attention" href="javascript:void(0);" onclick="add_my_focus('+uin+');">+关注</a>';
		$('.J_'+uin).html(str);
		$('#focus_num').html(myFocusNum);
		if(hide==true){
			$('#L_J_'+uin).hide();
		}
	}
}

function user_collect_room(target){
	var roomIds = $('#'+target).find('p.collect');
	if(roomIds.length>0){
		for(var i=0; i<roomIds.length; i++){
			if(myCollect[roomIds[i].innerHTML]){
				if(removeRoom){
					str = '<a class="cancel" href="javascript:void(0);" onclick="delete_my_collect('+roomIds[i].innerHTML+',true);">取消</a>';
				}
				else{
					str = '<a class="cancel" href="javascript:void(0);" onclick="delete_my_collect('+roomIds[i].innerHTML+');">取消</a>';
				}
			}
			else{
				str = '<a class="zhan" href="javascript:void(0);" onclick="add_my_collect('+roomIds[i].innerHTML+');">收藏</a>';
			}
			$("#R_"+roomIds[i].id).html(str);
		}
		if(removeRoom){
			removeRoom=false;
		}	
	}
}

function add_my_collect(id){
	var result=false;
	var result=collectRoom(id,false);
	if(result){
		myCollect[id]=roomListJson[id];
		myCollectNum++;
		str = '<a class="cancel" href="javascript:;" onclick="delete_my_collect('+id+');">取消</a>';
		$('.R_'+id).html(str);
		$('#collect_num').html(myCollectNum);
	}
}

function delete_my_collect(id,hide){
	var result=false;
	var result=cancelRoom(id,false);
	if(result){
		delete myCollect[id];
		myCollectNum--;
		str = '<a class="zhan" href="javascript:;" onclick="add_my_collect('+id+');">收藏</a>';
		$('.R_'+id).html(str);
		$('#collect_num').html(myCollectNum);
		if(hide==true){
			$('#L_R_'+id).hide();
		}
	}
}

function get_focus(obj){
	vipTabName=attentionUserName;
	$('#middle').html('');
	$('#rooms').html('');
	removeVip=true;
	create_middle_vip_html(myFocus,'vips','0','',0,false);
	$('.unstyled').find('li').removeClass('hover');
	$(obj).addClass('hover');
}

function get_collect(obj){
	roomTabName=collectionRoomName;
	$('#middle').html('');
	$('#vips').html('');
	removeRoom=true;
	create_middle_room_html(myCollect,'rooms','0','');
	$('.unstyled').find('li').removeClass('hover');
	$(obj).addClass('hover');
}

function get_history(obj){
	roomTabName=myFootprintName;
	$('#middle').html('');
	$('#vips').html('');
	create_middle_room_html(myHostory,'rooms','0','');
	$('.unstyled').find('li').removeClass('hover');
	$(obj).addClass('hover');
}

function __search(keywords){
	roomTabName='房间搜索';
	var action_url='/index.html?module=get_group_room&cat_id=0&keywords='+keywords+'&page=1';
	get_group_room(action_url);
	
	vipTabName='艺人搜索';
	var action_url='/index.html?module=get_group_artist&keywords='+keywords+'&page=1';
	get_group_artist(action_url);
}

function select_category(obj,cat_id,cat_name){
	roomTabName=cat_name;
	var action_url='/zhan/'+groupId+'&page=1';
	$('#vips').html('');
	get_group_room(action_url,cat_id,'',cat_name);
}

function get_recommend_sub(action_url){
	var m=/(.+)sub_id=([0-9]+)(.*)/;
	var s=m.exec(action_url);
	var sub_id=s[2];
	var m=/(.+)page=([0-9]+)(.*)/;
	var s=m.exec(action_url);
	var p=s[2];
	$.ajax({
		url: '/group_site/index.php',
		type: 'GET',
		data: {module:'get_recommend_sub',sub_id:sub_id,page:p,code:Math.random()},
		success: function(data){
			var html = '';
			var json = $.parseJSON(data);
			if(json.list.length > 0){
				for(var i in json.list){
					if(json.type=='1'){
						create_middle_room_html(json.list,"recommend_"+sub_id,sub_id,json.page);
					}
					else if(json.type=='4'){
						create_middle_vip_html(json.list,"recommend_"+sub_id,sub_id,json.page,0,true);
					}
				}
			}
		}
	});
}

function get_group_room(action_url,cat_id,keywords){
	var m=/(.+)page=([0-9]+)(.*)/;
	var s=m.exec(action_url);
	var p=s[2];
	if(keywords){
		var k=keywords;
	}
	else{
		var m=/(.+)\&keywords=(.+)\&(.*)/;
		var s=m.exec(action_url);
		if(s){
			var k=s[2];
		}
		else{
			var k='';
		}
	}
	if(cat_id){
		var c=cat_id;
	}
	else{
		var m=/(.+)\&cat_id=([0-9]+)\&(.*)/;
		var s=m.exec(action_url);
		if(s){
			var c=s[2];
		}
		else{
			var c=0;
		}
	}
	$.ajax({
		url: '/group_site/index.php',
		type: 'GET',
		data: {module:'get_group_room',cat_id:c,code:Math.random(),keywords:k,page:p},
		success: function(data){
			var json = $.parseJSON(data);
			$('#middle').html('');
			create_middle_room_html(json.roomList,'rooms','0',json.page);
		}
	});
}

function get_group_artist(action_url,keywords){
	var m=/(.+)page=([0-9]+)(.*)/;
	var s=m.exec(action_url);
	var p=s[2];
	if(keywords){
		var k=keywords;
	}
	else{
		var m=/(.+)\&keywords=(.+)\&(.*)/;
		var s=m.exec(action_url);
		if(s){
			var k=s[2];
		}
		else{
			var k='';
		}
	}
	$.ajax({
		url: '/group_site/index.php',
		type: 'GET',
		data: {module:'get_group_artist',code:Math.random(),keywords:k,page:p},
		success: function(data){
			var html = '';
			var json = $.parseJSON(data);
			$('#middle').html('');
			create_middle_vip_html(json.artistList,'vips','0',json.page,0,true);
		}
	});
}

function create_middle_vip_html(obj,target,sub_id,page,pic,is_artist){
	if(target=='vips'){
		var html='<div class="body-main-rooms"><div class="tabbable"><ul class="nav nav-tabs"><li class="active" id="allart_tab"><a href="#allart" data-toggle="tab" id="artist_name">'+vipTabName+'</a></li></ul><div class="tab-content"><div class="tab-pane active" id="allart">';
	}
	else{
		html='';
	}
	if(obj.length > 0 || obj){
		html += '<ul class="thumbnails">';
		for(var i in obj){
			if(artistListJson[obj[i].uin]){
				obj[i].room_id=artistListJson[obj[i].uin].room_id;
				obj[i].curuser=artistListJson[obj[i].uin].curuser;
				obj[i].hasplay=parseInt(artistListJson[obj[i].uin].hasplay, 10);
				html += '<li id="L_J_'+obj[i].uin+'"><div class="thumbnail room"><a href="/v/'+obj[i].room_id+'" title="'+obj[i].nick+'" target="_blank"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/uin/'+obj[i].uin+'big/150/110.jpg')+'" class="img" width="150" height="110" alt="'+obj[i].nick+'" title="'+obj[i].nick+'" /></a><p class="look">'+obj[i].curuser+'人正在观看...</p>';
				if(obj[i].hasplay > 0){
					html += '<p class="zhibo">直播中</p>';
				}
				html += '<p><a href="/v/'+obj[i].room_id+'" title="'+obj[i].nick+'" target="_blank" class="name">'+obj[i].nick+'</a></p><p class="listen" style="display:none;" id="'+sub_id+'_'+obj[i].uin+'">'+obj[i].uin+'</p><p class="num"><span id="J_'+sub_id+'_'+obj[i].uin+'" class="J_'+obj[i].uin+'"></span></span>艺人ID:'+obj[i].uin+'</p></div></li>';
			}
			else{
				html += '<li id="L_J_'+obj[i].uin+'"><div class="thumbnail room"><a href="/service/home.php?user='+obj[i].uin+'" title="'+obj[i].nick+'" target="_blank"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/uin/'+obj[i].uin+'big/150/110.jpg')+'" class="img" width="150" height="110" alt="'+obj[i].nick+'" title="'+obj[i].nick+'" /></a><p><a href="/service/home.php?user='+obj[i].uin+'" title="'+obj[i].nick+'" target="_blank" class="name">'+obj[i].nick+'</a></p><p class="listen" style="display:none;" id="'+sub_id+'_'+obj[i].uin+'">'+obj[i].uin+'</p><p class="num"><span id="J_'+sub_id+'_'+obj[i].uin+'" class="J_'+obj[i].uin+'"></span></span>艺人ID:'+obj[i].uin+'</p></div></li>';
			}
		}
		html +='</ul>';
		if(page!=''){
			html += page;
		}
	}
	else{
		html += '<center>找不到艺人哦。</center>';
	}
	if(target=='vips'){
		html+='</div></div></div></div>';
	}
	$("#"+target).html(html);
	$("#"+target).find('div[class="thumbnail room"]').hover(
	  function(){
		  $(this).addClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").show();
	  },
	  function(){
		  $(this).removeClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").hide();
	  }
	);
	$("#"+target).show();
	if(Uin>0){
		listen_user(target);
	}
}

function create_middle_room_html(obj,target,sub_id,page){
	if(target=='rooms'){
		var html='<div class="body-main-rooms"><div class="tabbable"><ul class="nav nav-tabs"><li class="active" id="allrooms_tab"><a href="#allrooms" data-toggle="tab">'+roomTabName+'</a></li></ul><div class="tab-content"><div class="tab-pane active" id="allrooms">';
	}
	else{
		html='';
	}
	if(obj.length > 0|| obj){
		html += '<ul class="thumbnails">';
		for(var i in obj){
			if(parseInt(obj[i].id) > 0){
				roomListJson[obj[i].id]=obj[i];
				
				html += '<li id="L_R_'+obj[i].id+'"><div class="thumbnail room"><a href="/v/'+obj[i].id+'" target="_blank" title="'+obj[i].name+'"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/roomid/'+obj[i].id+'/150/110.jpg')+'" width="150" height="110" alt="'+obj[i].name+'" title="'+obj[i].name+'" class="img" /></a><p class="look">'+obj[i].curuser+'人正在观看...</p>';
                if(parseInt(obj[i].hasplay)>0){
					html += '<p class="zhibo">直播中</p>';
				}
                html += '<p><a href="/v/'+obj[i].id+'" target="_blank" title="'+obj[i].name+'" class="name">'+obj[i].name+'</a></p><p class="collect" style="display:none;" id="'+sub_id+'_'+obj[i].id+'">'+obj[i].id+'</p><p class="num"><span id="R_'+sub_id+'_'+obj[i].id+'" class="R_'+obj[i].id+'"></span>房间号：'+obj[i].id+'</p></div></li>';
			}
		}
		html +='</ul>';
		if(page!=''){
			html += page;
		}
	}
	else{
		html += '<center>找不到房间哦。</center>';
	}
	if(target=='rooms'){
		html+='</div></div></div></div>';
	}
	$("#"+target).html(html);
	$("#"+target).find('div[class="thumbnail room"]').hover(
	  function(){
		  $(this).addClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").show();
	  },
	  function(){
		  $(this).removeClass("redbd");
		  $(this).find(".zhan, .cancel, .attention").hide();
	  }
	);
	$("#"+target).show();
	if(Uin>0){
		user_collect_room(target);
	}
}

//右侧新闻滚动
if(parseInt(messageLength)>0){
	newscroll("rollText");
}
function newscroll(rollTextId){
		var minTime,maxTime,divTop,newTop=0;
		var textDiv = document.getElementById(rollTextId);
		var textList = textDiv.getElementsByTagName("li");
		if(textList.length > 1){
			var textDat = textDiv.innerHTML;
			var br = textDat.toLowerCase().indexOf("<li",textDat.toLowerCase().indexOf("<li")+3);
			textDiv.innerHTML = textDat+textDat+textDat.substr(0,br);
			textDiv.style.cssText = "position:absolute; top:0";
			var textDatH = textDiv.offsetHeight;MaxRoll();
			textDiv.onmouseover=function() {clearInterval(minTime)}
			textDiv.onmouseout=function() {MaxRoll()}
		}
	function MinRoll(){
		newTop++;
		if(newTop<=divTop+80){
		textDiv.style.top = "-" + newTop + "px";
		}else{
		clearInterval(minTime);
		maxTime = setTimeout(MaxRoll,100);
		}
	}
	function MaxRoll(){
		divTop = Math.abs(parseInt(textDiv.style.top));
		if(divTop>=0 && divTop<textDatH-80){
		minTime = setInterval(MinRoll,80);
		}else{
		textDiv.style.top = 0;divTop = 0;newTop=0;MaxRoll();
		}
	}
}

//质量数据记录
window.onload = function(){
	var onLoadTime = new quality().getPageLoadTime();
	new quality().addlog({onLoadTime:onLoadTime});
    var browser = new quality().getBrowserInfo();
    if(browser == 'IE6' || browser == 'IE7'){
        var oHead = document.getElementsByTagName("head")[0];
        var oScript= document.createElement("script");
        oScript.type = "text/javascript";
        oScript.src = "<?php echo STATIC_API_PATH;?>/frontend/base/bsie/js/bootstrap-ie.js";
        oHead.appendChild(oScript);
    }
}
</script>