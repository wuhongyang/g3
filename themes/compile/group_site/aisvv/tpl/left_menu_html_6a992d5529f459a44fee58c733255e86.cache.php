<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(!empty($menuList)) { ?>
<?php if(is_array($menuList)) {foreach((array)$menuList as $val) {?>
<dl class="body-side-list">
    <dt><h3><i class="home-icon home-channel" style="background:url('<?php echo cdn_url(PIC_API_PATH.'/p/'.$val['icon'].'/22/22.jpg');;?>') no-repeat;margin-top:3px;"></i><span><?php echo $val['name'];?></span></h3></dt>
    <?php if(!empty($val['child'])) { ?>
    <dd>
        <ul class="game-list">
        <?php if(is_array($val['child'])) {foreach((array)$val['child'] as $val2) {?>
            <li>
            	<a<?php if($val2['url']!='') { ?> href="/group_site/create_url.php?url=<?php echo base64_encode($val2['url']);;?>" target="_blank"<?php } else { ?> href="javascript:void(0);"<?php } ?> title="<?php echo $val2['name'];?>">
            	<?php if(!empty($val2['icon'])) { ?>
                	<img width="22" height="22" src="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val2['icon'].'/22/22.jpg');;?>" class="pull-left">
                <?php } ?>
                <?php echo str_cut_out($val2['name']);;?>
            	</a>
            </li>
        <?php }} ?>
        </ul>
    </dd>
    <?php } ?>
</dl>
<?php }} ?>
<?php } ?>