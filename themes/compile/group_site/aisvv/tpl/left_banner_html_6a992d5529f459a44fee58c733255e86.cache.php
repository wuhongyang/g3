<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($imgList[0]['status']==1&&$imgList[0]['img']!='') { ?>
<div class="body-side-img" style="margin-top:10px;">
<?php if($imgList[0]['link']) { ?>
    <a href="<?php echo $imgList[0]['link'];?>"><img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$imgList[0]['img'].'/145/80.jpg');;?>" width="145" height="80" /></a>
<?php } else { ?>
    <img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$imgList[0]['img'].'/145/80.jpg');;?>" width="145" height="80" />
<?php } ?>
</div>
<?php } ?>