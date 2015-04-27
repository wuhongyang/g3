<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(!empty($menuList)) { ?>
<?php if(is_array($menuList)) {foreach((array)$menuList as $val) {?>
<dl>
	<dt class="arrow arrow_up"><i class="menu_icon"><img src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val['icon'].'/22/22.jpg');;?>" width="14" height="14"></i><?php echo $val['name'];?></dt>
	<ul>
		<?php if(!empty($val['child'])) { ?>
		<?php if(is_array($val['child'])) {foreach((array)$val['child'] as $val2) {?>
		<li>
			<a <?php if($val2['url']!='') { ?> href="/create_url.php?url=<?php echo base64_encode($val2['url']);;?>" target="_blank"<?php } else { ?> href="javascript:void(0);"<?php } ?> title="<?php echo $val2['name'];?>">
				<?php if(!empty($val2['icon'])) { ?>
						<img width="22" height="22" src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val2['icon'].'/22/22.jpg');;?>" class="pull-left">
				<?php } ?>
				<?php echo str_cut_out($val2['name']);;?>	
			</a>
		</li>
		<?php }} ?>
		<?php } ?>		
	</ul>
</dl>
<?php }} ?>
<?php } ?>

 
 