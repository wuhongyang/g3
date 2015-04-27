<?php if (!class_exists('template')) die('Access Denied');?>
<div class="container_head">
    <div class="user_box clearfix" id="J_login_info">
        <div class="img"><a href="/service/profile.php" target="_blank" class="sessionUrl1"><img id="userimgurl" width="52" height="52"/></a></div>
        <div class="info">
            <p class="name"><a href="/service/profile.php" target="_blank" class="sessionUrl1 welcome-name">{*name*}({*uin*})</a></p>
            <p class="rankimg"><img src="../img/img1.png" width="61" height="18"><img src="../img/img2.png" width="18" height="17"></p>
        </div>
    </div>
    <div class="topnav">
        <ul>
            <li><a href="/self/index.php"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/uline/offical_site.png" width="40" height="40"> <span>官方网站</span></a></li>
            <li><a href="#"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/uline/myrecharge.png" width="40" height="40"> <span>我要充值</span></a></li>
            <li><a href="#"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/uline/rank.png" width="40" height="40"> <span>排行榜</span></a></li>
            <li><a href="#"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/uline/active_center.png" width="40" height="40"> <span>活动中心</span></a></li>
        </ul>
    </div>
</div>
