<?php if (!class_exists('template')) die('Access Denied');?>
<div id="slideBox" class="slideBox">
      <ul class="items">
      <?php if(empty($carouselList)) { ?>
      		<li><a href="/rooms/recruit.php" title="招募站长" target="_blank"><img src="/pic/group/default.jpg" alt="招募站长" title="招募站长" width="540" height="200"></a></li>
      <?php } else { ?>
      	<?php if(is_array($carouselList)) {foreach((array)$carouselList as $val) {?>
            <li><a <?php if($val['url']!='') { ?>href="<?php echo $val['url'];?>"<?php } else { ?>href="#" onclick="return false;"<?php } ?> title="<?php echo $val['explain'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val['image'].'/540/200.jpg');;?>" alt="<?php echo $val['explain'];?>" title="<?php echo $val['explain'];?>" width="540" height="200"></a></li>
      	<?php }} ?>
      <?php } ?>
      </ul>
</div>