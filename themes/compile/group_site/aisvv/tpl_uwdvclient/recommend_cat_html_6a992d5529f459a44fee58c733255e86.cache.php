<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(is_array($recommendCat)) {foreach((array)$recommendCat as $key=>$val) {?>
<?php if($val['child']&&($val['type']==1||$val['type']==4)) { ?>
 <dl>
	<dt class="arrow arrow_up"><?php if($val['icon']!='') { ?><i class="menu_icon"><img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val['icon'].'/22/22.jpg');;?>" width="14" height="14"></i><?php } ?><?php echo $val['name'];?></dt>
	<ul>
		<?php if(!empty($val['child'])) { ?>
		<?php if(is_array($val['child'])) {foreach((array)$val['child'] as $val2) {?>
		<li><a href="javascript:void(0);" title="<?php echo $val2['name'];?>" onclick="get_recommend_sub(<?php echo $val2['id'];?>,1);"><?php echo $val2['name'];?></a></li>
		<?php }} ?>
		<?php } ?>		
	</ul>
</dl>	
<?php } ?>
<?php }} ?>