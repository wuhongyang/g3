<?php if (!class_exists('template')) die('Access Denied');?>
<dl class="body-side-list">
    <dt><h3><i class="home-icon home-room"></i><span>站内房间</span></h3></dt>
    <dd>
        <ul>
        <?php if(is_array($catList)) {foreach((array)$catList as $val) {?>
            <li><a href="javascript:select_category(this,<?php echo $val['id'];?>,'<?php echo $val['name'];?>');" title="<?php echo $val['name'];?>"><?php echo str_cut_out($val['name']);;?></a></li>
        <?php }} ?>
        </ul>
    </dd>
</dl>