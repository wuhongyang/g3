<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
//推荐位房间
$param = array(
    'extparam' => array('Tag'=>'GetRecommedRooms','RegionId'=>$site['region_id']),
    'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$recommend_rooms = request($param);
$recommend_rooms = (array)$recommend_rooms['HotRooms'];
$recommend_rooms = array_slice($recommend_rooms, 0, 10);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
body{
	width:100%;
	margin:0;
	padding:0;
	font-family:"微软雅黑", Verdana, Geneva, sans-serif;
}
*{
	margin:0;
	padding:0;
}
div, ul, li, p, h1, h2, img{
	margin:0;
	padding:0;
}
img{
	border:0;
}
/* 清理浮动 */
.clearfix:after {
    visibility: hidden;
    display: block;
    font-size: 0;
    content: " ";
    clear: both;
    height: 0;
}
.clearfix {
    zoom: 1; /* for IE6 IE7 */
}
ul li{
	list-style:none outside none;
}
.popbox{
	width:680px;
	height:490px;
	background:#000;
	position:relative;
}
.popbox .close{
	position:absolute;
	width:20px;
	height:20px;
	cursor:pointer;
	top:5px;
	right:15px;
	background:url("images/close.png") 0 0 no-repeat;
}
.popbox .close:hover{
	background-position:0 -20px;
}
.popbox h1{
	height:34px;
	background:url("images/popbg.png") no-repeat 0 0;
	border-bottom:1px solid #1e293e;
	text-indent:-9999px;
	overflow:hidden;
}
.popbox h1.tit2{
		background:url("images/popbg2.png")  no-repeat 0 0;
}
.popbox h2{
	margin:10px auto;
	width:620px;
	height:27px;
	background:url("images/hotroombg.png") no-repeat 0 0;
	text-indent:-9999px;
	overflow:hidden;
}
.popbox .task{
	margin:0 auto;
	width:620px;
	height:160px;
	padding:10px;
}
.popbox .task ul li{
	width:120px;
	height:80px;
	text-align:center;
	float:left;
	border-right:1px solid #2e2e2e;
	border-bottom:1px solid #2e2e2e;
}
.popbox .task ul li .imgwrap{
	width:100px;
	height:45px;
	padding:5px;
}
.popbox .task ul li p{
	color:#fff;
	font-size:12px;
	text-align:center;
}
.popbox .room-list{
	margin:0 auto;
	width:620px;
	height:180px;
}
.popbox .room-list ul li{
	position:relative;
	width:120px;
	height:90px;
	float:left;
}
.popbox .room-list ul li p{
	color:#fff;
	font-size:12px;
	font-family:Arial;
	height:22px;
	line-height:22px;
	text-indent:5px;
	width:100%;
	opacity: 0.8;
	filter:alpha(opacity=80);
	-moz-opacity:0.8;
	background:#000;
}
.popbox .room-list ul li .items{
	display:block;
	width:120px;
	height:90px;
}
.popbox .room-list ul li .items img{
	margin:0;
	padding:0;
	width:120px;
	height:90px;
	display:block;
	border:0;
}
.popbox .room-list ul li .items p{
	position:absolute;
	top:68px;
	left:0;
}
.popbox .room-list ul li .items-hover{
	position:absolute;
	top:-3px;
	left:-3px;
	display:none;
	border:3px solid #ffc600;
	z-index:200;
}
.popbox .room-list ul li .items-hover p{
	position:absolute;
	top:68px;
	left:0;
}
.popbox .room-list ul li .items-hover p strong{
	display:block;
	float:left;
	color:#ffea00;
	font-size:12px;
	width:60px;
	height:22px;
	overflow:hidden;
	text-overflow:ellipsis;
	-o-text-overflow:ellipsis;
	white-space:nowrap;
}
.popbox .room-list ul li .items-hover span{
	position:absolute;
	top:0;
	left:0;
	color:#ff0096;
	background:#ffc600;
	width:28px;
	height:16px;
	display:block;
	font-size:12px;
}
</style>
</head>
<body>
<div class="popbox">
  	<h1>
        <span>升级任务</span>
        <a class="close" id="close"></a>
    </h1>
    <div class="task">
        <ul>
            <li>
                <div class="imgwrap"><img src="images/taskicon1.png" width="100" height="45" /></div> 
                <p>延长您的在线时间</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon2.png" width="100" height="45" /></div> 
                <p>延长您上麦时间</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon3.png" width="100" height="45" /></div> 
                <p>延长您管理麦时间</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon5.png" width="100" height="45" /></div> 
                <p>参与对对碰游戏</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon6.png" width="100" height="45" /></div> 
                <p>参与连连看游戏</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon7.png" width="100" height="45" /></div> 
                <p>参与吃金币游戏</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon8.png" width="100" height="45" /></div> 
                <p>参与抢金币游戏</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon9.png" width="100" height="45" /></div> 
                <p>参与你演我猜</p>
            </li>
            <li>
                <div class="imgwrap"><img src="images/taskicon9.png" width="100" height="45" /></div> 
                <p>你演我猜出题</p>
            </li>
        </ul>
    </div>
    <h2>热门房间</h2>
    <div class="room-list">
        <ul>
            <?php foreach($recommend_rooms as $key=>$val): ?>
            <li class="room-item">
                <div class="items">
                    <img src="{PIC_API_PATH}/roomid/<?php echo $val['id']; ?>/120/90.jpg" width="120" height="90" />
                    <p><?php echo $val['id']; ?></p>
                </div>
                <div class="items-hover">
                    <a href="/v/<?php echo $val['id']; ?>" target="_blank" title="<?php echo $val['name']; ?>">
                        <img src="{PIC_API_PATH}/roomid/<?php echo $val['id']; ?>/120/90.jpg" width="120" height="90" />
                    </a>
                    <p><strong><?php echo $val['name']; ?></strong>(<?php echo $val['id']; ?>)</p>
                    <?php if($key=='0' || $key==1): ?>
                    <span>推荐</span>
                    <?php endif;?>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<script type="text/javascript" src="js/jquery-1.8.0.min.js"></script>
<script type="text/javascript">
$(function(){
    $(".room-item").hover(
    	function(){$(this).children(".items-hover").show()},
    	function(){$(this).children(".items-hover").hide()}
    );
    //去掉遮罩
    $('#close').click(function(){
        parent.pop.modal('hide');
    });
});
</script>
</body>
</html>
