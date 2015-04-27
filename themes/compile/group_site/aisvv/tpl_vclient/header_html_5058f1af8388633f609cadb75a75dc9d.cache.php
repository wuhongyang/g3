<?php if (!class_exists('template')) die('Access Denied');?>
<div class="wrap">
    <div class="container">
        <div class="header-nav clearfix">
            <div class="header-logo pull-left">
                <a href="/"><?php if($groupInfo['logo']=='') { ?><div style="width:97px;">&nbsp;</div><?php } else { ?><img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$groupInfo['logo'].'/100/30.jpg');;?>" title="<?php echo $groupInfo['name'];?>" alt="<?php echo $groupInfo['name'];?>" width="97" height="35" /><?php } ?></a>        
            </div>
            <ul class="header-nav-list pull-left">
            	<li><a href="/self/home.php"<?php if($moduleAction=='index') { ?> class="active"<?php } ?>>直播大厅</a></li>
                <li><a href="/top.html"<?php if($moduleAction=='top') { ?> class="active"<?php } ?> target="_blank">排行榜</a></li>
                <li><a href="/shop/index.php" target="_blank">商城</a></li>    				
                <li><a href="/active.html"<?php if($moduleAction=='active') { ?> class="active"<?php } ?> target="_blank">活动中心</a></li>  
            </ul>
            <div class="shortcut pull-right">
                <a href="/rooms/join.php?module=info&amp;type=2" target="_blank" class="sessionUrl1">室主/艺人申请</a>
            </div>
        </div>
    </div>
</div>