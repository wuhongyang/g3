<?php if (!class_exists('template')) die('Access Denied');?>
<!DOCTYPE HTML>
<html>

<!--template compile at 2014-04-28 09:17:16-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $groupInfo['name'];?></title>
<meta property="qc:admins" content="24562763266113666375" />
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/sinauc.css');;?>" rel="stylesheet" type="text/css">
</head>
<body>
<!--顶部导航-->
<link href="<?php echo cdn_url(STATIC_API_PATH.'/resource/css/topnav-min.css?b=14kkk');;?>" rel="stylesheet" type="text/css"></head>
<link href="<?php echo cdn_url(STATIC_API_PATH.'/resource/css/topnav-min.css');;?>" rel="stylesheet" type="text/css"></head>
<div class="hd">
    <div class="topnav clearfix" style="width:1000px;">
        <div class="topnav-left fll" id="J_head">
            欢迎您
            <a href="/passport/?account&index&url=/">登录</a>
            | <a href="/passport/?user_name&index">注册</a>
            | <a href="/rooms/shortcut.php?group_id=<?php echo $groupId;?>&title=<?php echo urlencode($groupInfo['name']);;?>" class="minor_text">+收藏至桌面</a>
        </div>
        <div class="topnav-right">
            <div id="site-nav">
                <ul class="quick-menu">
                    <li class="home" style="padding-right:0;">
                     <a class="menu-hd gray" href="index.html" style="padding-right:5px;">官网首页</a>|
                     <a class="menu-hd gray" href="/self/home.php" style="padding-right:0;">直播大厅</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/jquery/jquery.min.js');;?>"></script>
<script type="text/javascript">
JsonUin=0;
$.ajax({
	url: '/rooms/ajax_info.php',
	type: 'POST',
	async: false,
	data: {"Tag":"GetLoginUser","GroupId":"<?php echo $groupId;?>"},
	success: function(data){
		json = jQuery.parseJSON(data);
		if(json.Flag == 100){
			//展示头部
			var h_html = '欢迎您 <a class="yellow" href="/service/profile.php" style="color: #F0B45E;">' + json.Nick + '</a>  ';
			h_html += '&nbsp;<a href="/service/loginout.php?back=<?php echo $back_url;?>" class="gray">退出</a>  ';
			h_html += '&nbsp;<a href="/rooms/shortcut.php?group_id=<?php echo $groupId;?>&title=<?php echo urlencode($groupInfo['name']);;?>"> +收藏至桌面</a>';
			$('#J_head').html(h_html);
			JsonUin=parseInt(json.Uin);
		}
	}
});
</script>
<!--[if  IE 6]>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bsie/js/fixTopNavForIE6.min.js');;?>"></script>
<![endif]-->

<div class="nav-part">
	<div class="nav-box clears">
        <a class="logo fl" href="/index.html" hidefocus="true" title="vv酷">&nbsp;</a>
        <div class="nav-list fr clears">
            <a href="/index.html" title="首页" hidefocus="true" class="current">首页</a>
            <a href="/self/home.php" title="直播大厅" hidefocus="true">直播大厅</a>
            <a href="/top.html" title="排行榜" hidefocus="true">排行榜</a>
            <a href="/shop/index.php" title="商城" hidefocus="true" class="active">商城</a>
        </div>
    </div>
</div>
<div class="banner-part">
	<div class="banner-box">
    	<div class="banner-detail">
            <p class="fs24">下载客户端</p>
            <p class="fs18">更多激情，更爽体验！</p>
            <a href="http://static.vvku.com/smallktv/Installas.exe" class="download">立即下载</a>
            <div class="rolls">
                <?php if($activeList['activeList']) { ?>
                <h5>活动动态</h5>
                <div class="scrolltext-wrap">
                    <div id="rollText">
                        <ul class="scrolltext-list">
							<?php if(is_array($activeList['activeList'])) {foreach((array)$activeList['activeList'] as $val) {?>
                                <li><a href="active_<?php echo $val['id'];?>.html" title="<?php echo $val['title'];?>" target="_blank">&gt; <?php echo $val['title'];?></a></li>
                            <?php }} ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="step-part">
	<div class="step-box"></div>
</div>
<div class="product-part">
	<div class="product-box">
    	<ul class="clears">
        	<li>
                <img src="<?php echo THEMES_URL.'group_site/'.$themes.'/src/img/ucimg/1501501.jpg';;?>" width="150" height="150">
                <p>高清画质</p>
                <div class="popbox">
                    <div class="box-tp"></div>
                	<div class="box-md"><p>业界第一的高清画质，流畅清晰，且可随分辨率大小自动拉伸，适合人体视觉体验。</p></div>
                    <div class="box-bt"></div>
                </div>
            </li>
        	<li>
                <img src="<?php echo THEMES_URL.'group_site/'.$themes.'/src/img/ucimg/1501502.jpg';;?>" width="150" height="150">
                <p>天籁之音</p>
                <div class="popbox">
                    <div class="box-tp"></div>
                	<div class="box-md"><p>业界第一的高清音质，犹如天籁之音，真正体验KTV音乐房的视听享受。</p></div>
                    <div class="box-bt"></div>
                </div>
            </li>
        	<li>
                <img src="<?php echo THEMES_URL.'group_site/'.$themes.'/src/img/ucimg/1501503.jpg';;?>" width="150" height="150">
                <p>幸运礼物</p>
                <div class="popbox">
                    <div class="box-tp"></div>
                	<div class="box-md"><p>独特的幸运礼物刷礼模式，精彩纷呈，劲爆到爽的玩法体验。</p></div>
                    <div class="box-bt"></div>
                </div>
            </li>
        	<li>
                <img src="<?php echo THEMES_URL.'group_site/'.$themes.'/src/img/ucimg/1501504.jpg';;?>" width="150" height="150">
                <p>特权体系</p>
                <div class="popbox">
                    <div class="box-tp"></div>
                	<div class="box-md"><p>玩的就是特权！昵称特殊标识、独特识别标识、房间优先展示，更可与靓丽主播面对面私聊！你，还在等什么？</p></div>
                    <div class="box-bt"></div>
                </div>
            </li>
        	<li>
                <img src="<?php echo THEMES_URL.'group_site/'.$themes.'/src/img/ucimg/1501505.jpg';;?>" width="150" height="150">
                <p>座驾系统</p>
                <div class="popbox">
                    <div class="box-tp"></div>
                	<div class="box-md"><p>豪华座驾，彰显尊贵身份！</p></div>
                    <div class="box-bt"></div>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="footer-part">
	<p>Copyright © 2006-2014 vvku.com    版权所有 浙ICP备12027086号-1</p>
</div>
</div>

<script type="text/javascript">
$(function(){
	$(".product-box ul li").hover(
	  function(){
		  $(this).find(".popbox").show();
		  },
	  function(){
		  $(this).find(".popbox").hide();
		  }
	)
})

//右侧新闻滚动
var activeLength='<?php echo count($activeList["activeList"]);;?>';
if(parseInt(activeLength)>0){
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

</script>

</body>
</html>