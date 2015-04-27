<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($messageList['messageList']) { ?>
<div class="offical_notice">
	<h5><?php echo $messageList['title'];?></h5>
	<div class="notice">
		<div  class="notice_list">
			 <ul id="notices" >
				<?php if(is_array($messageList['messageList'])) {foreach((array)$messageList['messageList'] as $val) {?>
                <li><?php echo $val['content'];?></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
</div>
<?php } else { ?>
<div class="offical_notice">
    <h5>系统公告</h5>
    <div class="notice">
        <div class="notice_list">
            <ul id="notices" >
                <li>爱尚火热招聘艺人，欢迎各项才艺的亲们报名，有意请联系站内人员</li>
                <li>爱尚让你爱上生活爱上娱乐</li>
            </ul>
        </div>
    </div>
</div>
<?php } ?>