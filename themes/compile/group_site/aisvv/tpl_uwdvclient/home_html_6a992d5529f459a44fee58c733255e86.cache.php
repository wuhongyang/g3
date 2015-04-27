<?php if (!class_exists('template')) die('Access Denied');?>
<!DOCTYPE HTML>
<html>

<!--template compile at 2014-04-28 10:20:14-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $groupInfo['name'];?></title>
<!--[if lte IE 7]>
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bsie/css/bootstrap-ie6.css');;?>" rel="stylesheet" type="text/css">
<![endif]-->
<meta property="qc:admins" content="24562763266113666375" />
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/static/js/quality.js');;?>"></script>
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/jquery/jquery.min.js');;?>"></script>
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/plugin/slideBox/css/jquery.slideBox.css');;?>" rel="stylesheet" type="text/css" />
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/uline.css');;?>" rel="stylesheet" type="text/css">
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/reset.css');;?>" rel="stylesheet" type="text/css">
</head>
<body>
<?php include(template::getInstance()->getfile('header.html')); ?>

<div class="container">
    <div class="container_body">
    	<div class="sidewrap">
            <div class="sidetop">
                <!-- 当前在线 -->
                <?php include(template::getInstance()->getfile('index/online.html')); ?>
                <!-- 当前在线 -->
                <!-- 搜索 -->
                <?php include(template::getInstance()->getfile('index/search.html')); ?>
                <!-- 搜索 -->
            </div>
            <div class="left_menu">
				<h2><a href="javascript:void(0);"  onclick="create_my_room()">我的房间</a></h2>
				<!-- 分类 -->
				<?php include(template::getInstance()->getfile('index/recommend_cat.html')); ?>
				<!-- 分类 -->                
				<!-- 自定义导航 -->
                <?php include(template::getInstance()->getfile('index/left_menu.html')); ?>
                <!-- 自定义导航 -->
            </div>
        </div>
        <div class="main_wrap">
        	<div class="main">			
                <div class="main_body clearfix" id="main_body">				
                    <div class="main_left">
                        <!-- 轮播图 -->
                            <?php include(template::getInstance()->getfile('index/adv_cycle.html')); ?>
                        <!-- 轮播图 -->
                        <!-- 推荐位 -->
                            <?php include(template::getInstance()->getfile('index/recommend.html')); ?>
                        <!-- 推荐位 -->
                    </div>
					<div class="main_right">
						<!-- 充值室主申请按钮 -->
							<?php include(template::getInstance()->getfile('index/recharge_apply.html')); ?>
						<!-- 登录 -->
						<!-- 滚动消息 -->
							<?php include(template::getInstance()->getfile('index/message.html')); ?>
						<!-- 滚动消息 -->
						<!-- 排行榜 -->
							<?php include(template::getInstance()->getfile('index/rank.html')); ?>
						<!-- 排行榜 -->
					</div>												
                </div>
				
            </div>
        </div>
    </div>
</div>

<?php include(template::getInstance()->getfile('index/index_js.html')); ?>
</body>
</html>