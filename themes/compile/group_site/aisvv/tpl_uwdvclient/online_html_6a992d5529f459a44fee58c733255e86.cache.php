<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($setting['online_count']['online_count']['is_open']) { ?>
<div class="online">在线人数：<span class="num"><?php echo $onlineNum;?></span></div>
<?php } else { ?>
<div class="online">在线人数：<span class="num">0</span><span>人</span></div>
<?php } ?>

