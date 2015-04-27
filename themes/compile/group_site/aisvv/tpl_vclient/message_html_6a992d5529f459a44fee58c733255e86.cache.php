<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($messageList['messageList']) { ?>
<dl class="body-main-right-ranks">
    <dt><h3><?php echo $messageList['title'];?></h3></dt>
    <dd>
        <div class="scrolltext-wrap">
        <div id="rollText">
            <ul class="scrolltext-list">
            <?php if(is_array($messageList['messageList'])) {foreach((array)$messageList['messageList'] as $val) {?>
                <li><?php echo $val['content'];?></li>
            <?php }} ?>
            </ul>
        </div>
        </div>
    </dd>
</dl>
<?php } ?>