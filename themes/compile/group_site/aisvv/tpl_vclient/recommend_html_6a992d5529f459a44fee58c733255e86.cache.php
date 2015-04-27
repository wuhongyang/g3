<?php if (!class_exists('template')) die('Access Denied');?>
<?php if(is_array($recommendCat)) {foreach((array)$recommendCat as $key=>$val) {?>
<?php if($val['child']) { ?>
<div class="body-main-rooms">
    <div class="tabbable">
        <ul class="nav nav-tabs">
        <?php if(is_array($val['child'])) {foreach((array)$val['child'] as $key2=>$val2) {?>     	
            <li class="<?php if($key2==0) { ?>active<?php } ?>"><a href="#recommend_<?php echo $val2['id'];?>" data-toggle="tab"><?php echo $val2['name'];?></a></li>
        <?php }} ?>
        </ul>
        <div class="tab-content">
        <?php if(is_array($val['child'])) {foreach((array)$val['child'] as $key2=>$val2) {?>
            <div class="tab-pane<?php if($key2==0) { ?> active<?php } ?>" id="recommend_<?php echo $val2['id'];?>">
                <ul class="thumbnails">
                <?php if(is_array($val2['list'])) {foreach((array)$val2['list'] as $val3) {?>
                <?php if($val['type']==1) { ?>
                    <li>
                        <div class="thumbnail room">
                            <a href="javascript:;" onclick="external.cpp_goSrc('/v/<?php echo $val3['id'];?>?vclient','<?php echo $val3['name'];?>')" title="<?php echo $val3['name'];?>"><img src="<?php echo STATIC_API_PATH;?>/frontend/plugin/lazyload/default.gif" class="img" data-original="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val3['id'].'/150/110.jpg');;?>" width="150" height="110" alt="<?php echo $val3['name'];?>" title="<?php echo $val3['name'];?>" /></a>
                            <p class="look"><?php echo $val3['curuser'];?>人正在观看...</p>
                            <?php if($val3['hasplay']>0) { ?>
                            <p class="zhibo">直播中</p>
                            <?php } ?>
                            <p><a href="javascript:;" onclick="external.cpp_goSrc('/v/<?php echo $val3['id'];?>?vclient','<?php echo $val3['name'];?>')" class="name"><?php echo $val3['name'];?></a></p>
                            <p class="collect" style="display:none;" id="<?php echo $val2['id'].'_'.$val3['id'];?>"><?php echo $val3['id'];?></p>
                            <p class="num"><span id="R_<?php echo $val2['id'].'_'.$val3['id'];?>" class="R_<?php echo $val3['id'];?>"></span>房间号：<?php echo $val3['id'];?></p>
                        </div>
                    </li>
                <?php } elseif ($val['type']==4) { ?>
                    <li>
                        <div class="thumbnail room">
                            <a href="javascript:;" onclick="external.cpp_goSrc('/v/<?php echo $val3['room_id'];?>?vclient','<?php echo $val3['nick'];?>')" title="<?php echo $val3['nick'];?>" target="_blank">
                            <img src="<?php echo STATIC_API_PATH;?>/frontend/plugin/lazyload/default.gif" class="img" data-original="<?php echo cdn_url(PIC_API_PATH.'/uin/'.$val3['uin'].'big/150/110.jpg');;?>" width="150" height="110" alt="<?php echo $val3['nick'];?>" title="<?php echo $val3['nick'];?>" />
                            </a>
                            <p class="look"><?php echo $val3['curuser'];?>人正在观看...</p>
                            <?php if($val3['hasplay']>0) { ?>
                            <p class="zhibo">直播中</p>
                            <?php } ?>
                            <p><a href="javascript:;" onclick="external.cpp_goSrc('/v/<?php echo $val3['room_id'];?>?vclient','<?php echo $val3['nick'];?>')" title="<?php echo $val3['nick'];?>" target="_blank" class="name"><?php echo $val3['nick'];?></a></p>
                            <p class="listen" style="display:none;" id="<?php echo $val2['id'].'_'.$val3['uin'];?>"><?php echo $val3['uin'];?></p>
                            <p class="num"><span id="J_<?php echo $val2['id'].'_'.$val3['uin'];?>" class="J_<?php echo $val3['uin'];?>"></span>艺人ID:<?php echo $val3['uin'];?></p>
                        </div>
                    </li>
                <?php } elseif ($val['type']==3) { ?>
                    <?php if($val2['mode']==1) { ?>
                    <li>
                        <div class="thumbnail room">
                            <a href="<?php echo $val3['link'];?>" title="<?php echo $val3['title'];?>" target="_blank"><img src="<?php echo STATIC_API_PATH;?>/frontend/plugin/lazyload/default.gif" class="img" data-original="<?php echo cdn_url(PIC_API_PATH.'/p/'.$val3['pic'].'/150/110.jpg');;?>" width="150" height="110" alt="<?php echo $val3['title'];?>" title="<?php echo $val3['title'];?>"></a>
                            <p class="name"><?php echo $val3['title'];?></p>
                            <p><?php echo $val3['subtitle'];?></p>
                        </div>
                    </li>
                    <?php } ?>
                <?php } ?>
                <?php }} ?>
                </ul> 
                <?php if($val2['page']!='') { ?>
                    <?php echo $val2['page'];?>
                <?php } ?>  
            </div>
        <?php }} ?>
        </div>
    </div>				
</div>
<?php } ?>
<?php }} ?>