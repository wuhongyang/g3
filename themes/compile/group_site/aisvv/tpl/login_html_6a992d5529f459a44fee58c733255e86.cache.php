<?php if (!class_exists('template')) die('Access Denied');?>
<div id="J_login" class="login">
    <form class="form-horizontal login" method="post" action="/passport/?account&login&group_id=<?php echo $groupId;?>" id="J_login_form">
        <input type="hidden" name="group_id" value="<?php echo $groupId;?>">
        <input type="hidden" name="url" value="<?php echo $back_url;?>" />
        <div class="clearfix">
        	<h3>快速登陆</h3>
            <a class="qicon" href="javascript:;" onclick="openlogin('qq');"><img alt="QQ登录" title="用QQ帐号登录" src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/qqlogin.png"></a>
        </div>
        <div><input name="username" id="username" type="text" placeholder="邮箱/手机帐号/用户名" onkeydown="if(event.keyCode==13){$('#J_login_form').submit();}" class="inputcs" /></div>
        <div><input name="password" id="password" type="password" placeholder="密码" onkeydown="if(event.keyCode==13){$('#J_login_form').submit();}" class="inputcs" /></div>
        <div><label class="checkbox"><input type="checkbox" checked="checked" value="1" name="rememberme">下次自动登录
<a href="/passport/?account&forgot" class="passwd">忘记密码？</a></label></div>
        <p class="clearfix"><button class="btn btn-danger pull-left" onClick="$('#J_login_form').submit();">登 录</button> <a href="/passport/?user_name&index" class="btn btn-inverse pull-right">新用户注册</a></p>
    </form>
</div>
<!--登陆后-->
<div class="user" id="J_login_info" style="display: none;">
    <div class="clearfix">
        <div class="pull-left userface"><a href="/service/profile.php"><img id="userimgurl" width="70" height="70"></div>
        <div class="pull-left userinfo">
            <div class="username"><a href="/service/profile.php" class="welcome-name">{*name*}</a></div>
            <div class="verify">
                <div style="height:25px;" class="clearfix"><a href="javascript:void(0);" id="J_phone"></a><span class="pull-left" id="J_phone_text">手机认证</span></div>
                <div style="height:25px;" class="clearfix"><a href="javascript:void(0);" id="J_mail"></a><span class="pull-left" id="J_mail_text">邮箱认证</span></div>
            </div>
        </div>
    </div>
    <div class="userbalance">
    	<div class="clearfix"><span class="pull-left">ID：{*uin*}</span> <a href="/service/profile.php" title="未读消息" class="pull-right">消息 (<span id="J_unread">0</span>)</a></div>
        <?php if(isset($groupExtInfo["artistRankRuleId"]["value"])) { ?>
		<div class="clearfix" id="artistRankBox">
            <div class="pull-left">
                <span>主播：</span>
                <img id="artistRankImg">
            </div>
            <div class="progress pull-right" style="width:100px;">
                <div class="bar" id="artistRank"></div>
            </div>
        </div>
        <?php } ?>
        <?php if(isset($groupExtInfo["richRankRuleId"]["value"])) { ?>
        <div class="clearfix" id="richRankBox">
            <div class="pull-left">
                <span>富豪：</span>
                <img id="richRankImg">
            </div>
            <div class="progress pull-right" style="width:100px;">
                <div class="bar" id="richRank"></div>
            </div>
        </div>
        <?php } ?>
        <div class="clearfix"><span class="pull-left">金币：{*voucher*}</span><a href="/shop/index.php?groupId=<?php echo $groupId;?>" target="_blank" class="pull-right">获取金币</a></div>
	</div>
    <div class="collectinfo" style="display:none;">
        <ul class="unstyled">
        	<?php if($setting['my_attention']['attention_user']['is_open']) { ?>
            <li onclick="get_focus(this);"><?php echo $setting['my_attention']['attention_user']['name'];?>（<span id="focus_num"></span>）</li>
            <?php } ?>
            <?php if($setting['my_attention']['collection_room']['is_open']) { ?>
            <li onclick="get_collect(this);"><?php echo $setting['my_attention']['collection_room']['name'];?>（<span id="collect_num"></span>）</li>
            <?php } ?>
            <?php if($setting['my_attention']['my_footprint']['is_open']) { ?>
            <li onclick="get_history(this);"><?php echo $setting['my_attention']['my_footprint']['name'];?>（<span id="history_num"></span>）</li>
            <?php } ?>
        </ul>
    </div>
</div>

<?php if($groupInfo['notice']) { ?><div class="notice"><?php echo $groupInfo['notice'];?>&nbsp;</div><?php } ?>
<script type="text/javascript">
function openlogin(type){
    var childWindow;
    childWindow = window.open("http://<?php echo $callback;?>/passport/openlogin/"+type+"/login.php?back=<?php echo $_SERVER['HTTP_HOST'];?>&redirect=/","OpenLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
}
</script>