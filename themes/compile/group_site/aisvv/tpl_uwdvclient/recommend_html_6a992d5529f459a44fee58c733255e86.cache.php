<?php if (!class_exists('template')) die('Access Denied');?>
<div class="recom_room">
    <h3>热门房间</h3>
	<div class="rooms">
		<ul>
			<?php if(is_array($roomList['roomList'])) {foreach((array)$roomList['roomList'] as $key=>$val) {?>
			<?php if($key<12 ) { ?>      
				<li id="<?php echo $val['id'];?>">
					<a href="/v/<?php echo $val['id'];?>"  target="_blank" title="<?php echo $val['name'];?>" class="img">
						<img src="<?php echo cdn_url(PIC_API_PATH.'/roomid/'.$val['id'].'/150/110.jpg');;?>"  width="100" height="73" alt="<?php echo $val['name'];?>" title="<?php echo $val['name'];?>" />
					</a>
					<a href="/v/<?php echo $val['id'];?>" target="_blank" title="<?php echo $val['name'];?>" class="name"><?php echo $val['name'];?></a>
					<p>房间号：<?php echo $val['id'];?></p>
					<p>人数：<span class="num"><?php echo $val['curuser'];?></span></p>
				</li>
			<?php } ?>
			<?php }} ?>
		</ul>
	</div>
</div>
