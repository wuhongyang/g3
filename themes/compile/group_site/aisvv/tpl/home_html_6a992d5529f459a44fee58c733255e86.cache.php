<?php if (!class_exists('template')) die('Access Denied');?>
<!DOCTYPE HTML>
<html>

<!--template compile at 2014-04-28 09:17:32-->

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $groupInfo['name'];?></title>
<meta property="qc:admins" content="24562763266113666375" />
<script type="text/javascript" src="<?php echo cdn_url(STATIC_API_PATH.'/static/js/quality.js');;?>"></script>
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bootstrap/css/bootstrap.min.css');;?>" rel="stylesheet" type="text/css">
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/plugin/slideBox/css/jquery.slideBox.css');;?>" rel="stylesheet" type="text/css" />
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/layout.css');;?>" rel="stylesheet" type="text/css">
</head>
<body>
<?php include(template::getInstance()->getfile('header_group_site.html')); ?>
<?php include(template::getInstance()->getfile('header.html')); ?>
<?php if($is_intention) { ?>
<?php include(template::getInstance()->getfile('index/intention_create.html')); ?>
<?php } ?>
<div class="container">
    <div class="top-bg"><img src="<?php echo THEMES_URL;?>group_site/aisvv/src/img/top-bg.jpg"></div>
</div>

<div class="container">
	<div class="body clearfix">
        <div class="body-side pull-left">
            <!-- 当前在线 -->
        	<?php include(template::getInstance()->getfile('index/online.html')); ?>
            <!-- 当前在线 -->
            <!-- 左边顶部banner -->
        	<?php include(template::getInstance()->getfile('index/left_banner.html')); ?>
            <!-- 左边顶部banner -->
            <!-- 搜索 -->
        	<?php include(template::getInstance()->getfile('index/search.html')); ?>
            <!-- 搜索 -->
            <!-- 分类 -->
        	<?php include(template::getInstance()->getfile('index/category.html')); ?>
            <!-- 分类 -->
            <!-- 自定义导航 -->
			<?php include(template::getInstance()->getfile('index/left_menu.html')); ?>
            <!-- 自定义导航 -->
        </div>
		<div class="body-main pull-right clearfix">
        	<div class="body-main-left pull-left">
                <!-- 轮播图 -->
                	<?php include(template::getInstance()->getfile('index/adv_cycle.html')); ?>
            	<!-- 轮播图 -->
                <!-- 推荐位 -->
                <div id="middle">
                    <?php include(template::getInstance()->getfile('index/recommend.html')); ?>
                </div>
                <div id="rooms"></div>
                <div id="vips"></div>
                <!-- 推荐位 -->
            </div>
            <div class="body-main-right pull-right">
            	<!-- 登录 -->
                    <?php include(template::getInstance()->getfile('index/login.html')); ?>
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

<?php if($is_guide && $all_role && $current_role['account_details']) { ?>
<?php include(template::getInstance()->getfile('index/guide.html')); ?>
<?php } ?>
<?php include(template::getInstance()->getfile('footer.html')); ?>
<?php include(template::getInstance()->getfile('index/index_js.html')); ?>
</body>
</html>