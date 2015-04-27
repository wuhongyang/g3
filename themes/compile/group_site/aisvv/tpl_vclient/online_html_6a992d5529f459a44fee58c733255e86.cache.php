<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($setting['online_count']['online_count']['is_open']) { ?>
<dl class="body-side-list online-box">
    <dt>当前在线</dt>
    <dd class="online-num"><?php echo $onlineNum;?><span class="small">位</span></dd>
</dl>
<?php } ?>