<?php if (!class_exists('template')) die('Access Denied');?>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/frontend/plugin/lazyload/lazyload.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/frontend/plugin/slideBox/js/jquery.slideBox.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/frontend/base/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/frontend/modules/rooms/js/rooms.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/resource/js/common.js"></script>
<script type="text/javascript" src="<?php echo STATIC_API_PATH;?>/resource/js/placeHolder.js"></script>
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
//var messageLength='<?php echo count($messageList["messageList"]);;?>';
var messageLength=2;
var roomListJson=$.parseJSON('<?php echo $roomListJson;?>');
console.log(roomListJson);
//var artistListJson=$.parseJSON('<?php echo $artistListJson;?>');
var Token='';
$(function(){
    $(".body-main .img").lazyload({skip_invisible:false,failure_limit:20,effect:"fadeIn"});
	$('#slideBox').slideBox({
		duration : 0.3,//滚动持续时间，单位：秒
		easing : 'linear',//swing,linear//滚动特效
		delay : 5,//滚动延迟时间，单位：秒
		hideClickBar : false,//不自动隐藏点选按键
		clickBarRadius : 10
	});
	
	var oForm1Inputs = document.getElementsByTagName('input');
	for(var i=0;i<oForm1Inputs.length;i++){
		placeHolder(oForm1Inputs[i],true);
	}
	
	var tabs=$("div.gift_tabs ul li a");
	tabs.first().addClass("hover");
	tabs.click(function(){
		tabs.removeClass("hover");
		$(this).addClass("hover").siblings().removeClass("hover");
		var index=tabs.index(this);
		$(".gift_body .gift_content").eq(index).show().siblings().hide()
	})	

	$("dt.arrow").toggle(
	function(){
		var self=$(this);
		self.next("ul").slideToggle(600,function(){
			self.removeClass("arrow_up").addClass("arrow_down");
			
		})
	},
	function(){
		var self=$(this);
		self.next("ul").slideToggle(600,function(){
			self.removeClass("arrow_down").addClass("arrow_up");
		})
	})	
	rankList();
});

function rankList(){
    var rank = '<?php echo $rankList;?>';
    //$(rank)
    console.log(rank);
}
//判断登陆
$.ajax({
	url: '/rooms/ajax_info.php',
	type: 'POST',
	data: {"Tag":"GetLoginUser","GroupId":groupId},
	success: function(data){
		json = $.parseJSON(data);
        console.log(json);
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
						myFocus=json.roomList;
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

			//用户登录信息
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
			
			var sessionUrl1=$('.sessionUrl1');
			var sessionUrl2=$('.sessionUrl2');
			for(var i=0;i<sessionUrl1.length;i++){
				var sessionUrl=$(sessionUrl1[i]).attr('href')+'?SessionKey='+json.Token;
				$(sessionUrl1[i]).attr('href',sessionUrl);
			}
			for(var i=0;i<sessionUrl2.length;i++){
				var sessionUrl=$(sessionUrl2[i]).attr('href')+'&SessionKey='+json.Token;
				$(sessionUrl2[i]).attr('href',sessionUrl);
			}
			Token=json.Token;
			
		}
	}
});

function create_my_room(){
	alert(12);
	var obj1=myCollect;
	var obj2=myHostory;
	alert(myHostory);
	var html='';
	html+='<div class="myroom"><h3>我的房间</h3><div class="rooms"><h5 class="collect_room">我收藏的房间</h5><ul class="clearfix">';
	for(var i in obj1){
		html+='<li><a href="javascript:;" onclick="external.cpp_goSrc(\'/v/'+obj1[i].id+'?vclient\',\''+obj1[i].name+'\')" title="'+obj1[i].name+'" target="_blank" class="img"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/roomid/'+obj1[i].id+'/150/110.jpg')+'" width="100" height="73" alt="'+obj1[i].name+'" title="'+obj1[i].name+'" class="img" /></a>';
		html+='<a href="#" target="_blank" title="'+obj1[i].name+'" class="name">'+obj1[i].name+'</a>';
		html+='<p>房间号：'+obj1[i].id+'</p><p>人数：<span class="num">'+obj1[i].curuser+'</span></p><a href="#" class="del">删除</a>';
		html+='</li>';
	}
	html+='</ul>';
	html+='<h5 class="reach_room">我去过的房间</h5><ul class="clearfix">';
	for(var i in obj2){
		html+='<li><a href="javascript:;" onclick="external.cpp_goSrc(\'/v/'+obj2[i].id+'?vclient\',\''+obj2[i].name+'\')" title="'+obj2[i].name+'" target="_blank" class="img"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/roomid/'+obj2[i].id+'/150/110.jpg')+'" width="100" height="73" alt="'+obj2[i].name+'" title="'+obj2[i].name+'" class="img" /></a>';
		html+='<a href="#" target="_blank" title="'+obj2[i].name+'" class="name">'+obj2[i].name+'</a>';
		html+='<p>房间号：'+obj2[i].id+'</p><p>人数：<span class="num">'+obj2[i].curuser+'</span></p>';
		html+='</li>';
	}	
	html+='</ul></div></div>';
	$("#main_body").html("");
	$("#main_body").html(html);
}

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

function get_recommend_sub(sub_id,page){
	if(page>0){
		listPage=page;
		artistTotal=0;
		roomTotal=0;
	}
	if(listPage<=1){
		var action='change';
	}
	else{
		var action='add';
	}
	$.ajax({
		url: '/group_site/index.php',
		type: 'GET',
		data: {module:'get_recommend_sub',sub_id:sub_id,page:listPage,code:Math.random()},
		success: function(data){
			var html = '';
			var json = $.parseJSON(data);
			if(json.Flag!=100){
				alert('加载失败，请稍后再试');
				return;
			}
			if(json.list){
				if(json.type=='1'){
					var type='room';
					var html='<a href="javascript:void(0)" class="btn btn-large btn-primary" onclick="get_recommend_sub('+sub_id+');" type="button"><span class="ie6-vertical arrow-tip" id="ArrowTip"></span><span>点击查看更多房间</span></a>';
				}
				else if(json.type=='4'){
					var type='artist';
					var html='<a href="javascript:void(0)" class="btn btn-large btn-primary" onclick="get_recommend_sub('+sub_id+');" type="button"><span class="ie6-vertical arrow-tip" id="ArrowTip"></span><span>点击查看更多主播</span></a>';
				}
				if(listPage==1){
					$('#moreLinkWrap').html(html);
					$('#moreLinkWrap').show();
				}
				$('#tabName').html(recommendSubJson[sub_id].name);
				create_middle_html(json.list,type,sub_id,action,json.total,false,'main_body');
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


function create_middle_html(obj,type,sub_id,action,total,remove,target){
	alert(12);
	var html='';
	if(obj){
	alert(obj);
	
		for(var i in obj){
		
			if(type=='artist'){
				artistTotal++;
				html+='<li id="'+obj[i].uin+'"><div class="thumbnail room"><a href="/v/'+obj[i].room_id+'" title="'+obj[i].nick+'" target="_blank"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/uin/'+obj[i].uin+'big/150/110.jpg')+'" alt="'+obj[i].nick+'" title="'+obj[i].nick+'" width="160" height="120" class="img"></a><p class="look">'+obj[i].curuser+'人正在观看...</p>';
				
				html+='<p><a href="/v/'+obj[i].room_id+'" title="'+obj[i].nick+'" class="name">';
				if(artistListJson[obj[i].uin].artistRankImg){
					html+='<img src="'+artistListJson[obj[i].uin].artistRankImg+'" title="'+artistListJson[obj[i].uin].artistRankName+'" alt="'+artistListJson[obj[i].uin].artistRankName+'" height="17" /> ';
				}
				html+=obj[i].nick+'</a></p><p class="listen" style="display:none;" id="'+sub_id+'_'+obj[i].uin+'">'+obj[i].uin+'</p><p class="listenArtist"><span id="J_'+sub_id+'_'+obj[i].uin+'" class="J_'+obj[i].uin+'">';
				
				html+='</span></p><p>艺人ID：'+obj[i].uin+'</p></div></li>';
			}
			else if(type=='room'){
				roomTotal++;
				roomListJson[obj[i].id]=obj[i];
				html+='<li id="'+obj[i].id+'"><div class="thumbnail room"><a href="/v/'+obj[i].id+'" title="'+obj[i].name+'" target="_blank"><img src="'+cdn_url('<?php echo PIC_API_PATH;?>/roomid/'+obj[i].id+'/150/110.jpg')+'" alt="'+obj[i].name+'" title="'+obj[i].name+'" width="160" height="120" class="img"></a><p class="look">'+obj[i].curuser+'人正在观看...</p>';
				if(obj[i].hasplay>0){
					html+='<p class="zhibo">直播中</p>';
				}
				html+='<p><a href="/v/'+obj[i].id+'" title="'+obj[i].name+'" class="name">'+obj[i].name+'</a></p><p class="collect" style="display:none;" id="'+sub_id+'_'+obj[i].id+'">'+obj[i].id+'</p><p class="listenRoom"><span id="R_'+sub_id+'_'+obj[i].id+'" class="R_'+obj[i].id+'">';
				if(myCollect[obj[i].id]){
					if(remove){
						html+='<a class="cancel" href="javascript:void(0);" onclick="delete_my_collect('+obj[i].id+',true);">取消</a>';
					}
					else{
						html+='<a class="cancel" href="javascript:void(0);" onclick="delete_my_collect('+obj[i].id+');">取消</a>';
					}
				}
				else{
					if(Uin>0){
						html+='<a class="zhan" href="javascript:void(0);" onclick="add_my_collect('+obj[i].id+');">收藏</a>';
					}
				}
				html+='</span></p><p>房间号：'+obj[i].id+'</p></div></li>';
			}
		}
	}
	else{
		$('#moreLinkWrap').html('');
	}
	if(action=='change'){
		$('#'+target).html(html);
	}
	else if(action=='add'){
		$('#'+target).append(html);
	}
	if(type=='artist'){
		if(artistTotal>=total){
			$('#moreLinkWrap').html('');
		}
	}
	else if(type=='room'){
		if(roomTotal>=total){
			$('#moreLinkWrap').html('');
		}
	}
	if(target=='middle2'){
		$('#secondTab').show();
	}
	else{
		$('#secondTab').hide();
	}
	if(html==''){
		if(target=='middle2'){
			$('#'+target).html('<div style="font-size:14px;height:40px;padding:10px;"><center>没有艺人哦。</center></div>');
		}
		else{
			$('#'+target).html('<div style="font-size:14px;height:40px;padding:10px;"><center>没有房间哦。</center></div>');
		}
	}
	//热门房间鼠标移上去效果 
	$(".room").hover(
	  function(){$(this).addClass("hoverbd").find(".listenRoom").show()},
	  function(){$(this).removeClass("hoverbd").find(".listenRoom").hide()}
	);
	listPage++;
}




//右侧新闻滚动
alert(parseInt(messageLength));
if(parseInt(messageLength)>0){
	newscroll("notices");
}
function newscroll(o){
		var minTime,maxTime,divTop,newTop=0;
		var textDiv = document.getElementById(o);
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