<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(is_array($rankList)) {foreach((array)$rankList as $n=>$val) {?>
<dl class="body-main-right-ranks">
    <dt><h3><?php echo $val['name'];?></h3></dt>
    <dd>
        <ul class="nav nav-pills">
            <li class="active"><a href="#week_<?php echo $n;?>" data-toggle="tab">本周</a></li>
            <li><a href="#last_week_<?php echo $n;?>" data-toggle="tab">上周</a></li>
            <li><a href="#month_<?php echo $n;?>" data-toggle="tab">月榜</a></li>
            <li><a href="#total_<?php echo $n;?>" data-toggle="tab">总榜</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="week_<?php echo $n;?>">
                <table>
                <?php if(is_array($val['week'])) {foreach((array)$val['week'] as $key=>$val2) {?>
                    <tr>
                        <td width="20" align="center">
                        <?php if($key<3) { ?>
                            <span class="home-icon home-top<?php echo $key+1;?>"><?php echo $key+1;?></span>
                        <?php } else { ?>
                            <span><?php echo $key+1;?></span>
                        <?php } ?>
                        </td>
						<td align="center">
						<?php if($val2['SortType']==2) { ?>
							<a href="/v/<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val2['Link'].'/40/28.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="28" /></a>
						<?php } else { ?>
							<a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/uin/'.$val2['Link'].'/40/40.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="40" /></a>
						<?php } ?>
						</td>
                        <td align="left"><?php echo str_cut_out($val2['Nick']);;?></td>
                        <td align="right"><?php echo $val2['Weight'];?></td>
                    </tr>
                <?php }} ?>
                </table>
            </div>
            <div class="tab-pane" id="last_week_<?php echo $n;?>">
                <table>
                <?php if(is_array($val['last_week'])) {foreach((array)$val['last_week'] as $key=>$val2) {?>
                    <tr>
                        <td width="20" align="center">
                        <?php if($key<3) { ?>
                            <span class="home-icon home-top<?php echo $key+1;?>"><?php echo $key+1;?></span>
                        <?php } else { ?>
                            <span><?php echo $key+1;?></span>
                        <?php } ?>
                        </td>
						<td align="center">
						<?php if($val2['SortType']==2) { ?>
							<a href="/v/<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val2['Link'].'/40/28.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="28" /></a>
						<?php } else { ?>
							<a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/uin/'.$val2['Link'].'/40/40.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="40" /></a>
						<?php } ?>
						</td>
                        <td align="left"><?php echo str_cut_out($val2['Nick']);;?></td>
                        <td align="right"><?php echo $val2['Weight'];?></td>
                    </tr>
                <?php }} ?>
                </table>
            </div>
            <div class="tab-pane" id="month_<?php echo $n;?>">
                <table>
                <?php if(is_array($val['month'])) {foreach((array)$val['month'] as $key=>$val2) {?>
                    <tr>
                        <td width="20" align="center">
                        <?php if($key<3) { ?>
                            <span class="home-icon home-top<?php echo $key+1;?>"><?php echo $key+1;?></span>
                        <?php } else { ?>
                            <span><?php echo $key+1;?></span>
                        <?php } ?>
                        </td>
						<td align="center">
						<?php if($val2['SortType']==2) { ?>
							<a href="/v/<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val2['Link'].'/40/28.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="28" /></a>
						<?php } else { ?>
							<a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/uin/'.$val2['Link'].'/40/40.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="40" /></a>
						<?php } ?>
						</td>
                        <td align="left"><?php echo str_cut_out($val2['Nick']);;?></td>
                        <td align="right"><?php echo $val2['Weight'];?></td>
                    </tr>
                <?php }} ?>
                </table>
            </div>
            <div class="tab-pane" id="total_<?php echo $n;?>">
                <table>
                <?php if(is_array($val['total'])) {foreach((array)$val['total'] as $key=>$val2) {?>
                    <tr>
                        <td width="20" align="center">
                        <?php if($key<3) { ?>
                            <span class="home-icon home-top<?php echo $key+1;?>"><?php echo $key+1;?></span>
                        <?php } else { ?>
                            <span><?php echo $key+1;?></span>
                        <?php } ?>
                        </td>
						<td align="center">
						<?php if($val2['SortType']==2) { ?>
							<a href="/v/<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val2['Link'].'/40/28.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="28" /></a>
						<?php } else { ?>
							<a href="/service/home.php?user=<?php echo $val2['Link'];?>" title="<?php echo $val2['Nick'];?>" target="_blank"><img src="<?php echo cdn_url(PIC_API_PATH.'/uin/'.$val2['Link'].'/40/40.jpg');;?>" alt="<?php echo $val2['Nick'];?>" title="<?php echo $val2['Nick'];?>" width="40" height="40" /></a>
						<?php } ?>
						</td>
                        <td align="left"><?php echo str_cut_out($val2['Nick']);;?></td>
                        <td align="right"><?php echo $val2['Weight'];?></td>
                    </tr>
                <?php }} ?>
                </table>
            </div>
        </div>
    </dd>
</dl>
<?php }} ?>