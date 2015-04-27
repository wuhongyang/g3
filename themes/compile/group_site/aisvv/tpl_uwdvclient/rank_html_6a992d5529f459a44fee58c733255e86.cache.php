<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(is_array($rankList)) {foreach((array)$rankList as $n=>$val) {?>
<div class="rank_gift">
    <h5><?php echo $val['name'];?></h5>
    <div class="gift_tabs">
        <ul>
            <li><a href="#" class="hover">本周</a></li>
            <li><a href="#">上周</a></li>
        </ul>
    </div>
    <div class="gift_body">
        <div class="gift_content" style="display:block">
            <ul>
                <?php if(is_array($val['month'])) {foreach((array)$val['month'] as $key=>$val2) {?>
                <li class="clearfix">
                    <span class="td1"><a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/<?php echo $val2['FourthId'];?>.png" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="35" height="35"></a></span>
                    <a class="td2" href="/service/home.php?user=<?php echo $val2['Link'];?>" target="_blank" title="<?php echo $val2['Nick'];?>"><?php echo str_cut_out($val2['Nick']);;?></a>
                    <span class="td3"><?php echo $val2['Weight'];?></span>
                </li>
                <?php }} ?>
            </ul>
        </div>
        <div class="gift_content" style="display:none">
            <ul>
                <?php if(is_array($val['total'])) {foreach((array)$val['total'] as $key=>$val2) {?>
                <li class="clearfix">
                    <span class="td1"><a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/<?php echo $val2['FourthId'];?>.png" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="35" height="35"></a></span>
                    <a class="td2" href="/service/home.php?user=<?php echo $val2['Link'];?>" target="_blank" title="<?php echo $val2['Nick'];?>"><?php echo str_cut_out($val2['Nick']);;?></a>
                    <span class="td3"><?php echo $val2['Weight'];?></span>
                </li>
                <?php }} ?>
            </ul>
        </div>
    </div>
</div>
<?php }} ?>

