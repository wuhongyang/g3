<?php if (!class_exists('template')) die('Access Denied');?>
<div class="container">
	<div class="body clearfix">
        <div class="footer">
            <div><a href="#">关于我们</a> <a href="#">联系我们</a> <a href="#">产品与服务</a> <a href="/links.php" target="_blank">友情链接</a></div>
            <div><?php echo $footerInfo['icp_info'];?></div>
            <div>平台支持：<a href="http://www.vvku.com" title="vvku" target="_blank">vvku</a></div>
        </div>
    </div>
</div>
<!--[if lte IE 7]>
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bsie/css/ie.css');;?>" rel="stylesheet" type="text/css">
<link href="<?php echo cdn_url(STATIC_API_PATH.'/frontend/base/bsie/css/bootstrap-ie6.css');;?>" rel="stylesheet" type="text/css">
<![endif]-->
<!--[if lte IE 6]>
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/layout.css');;?>" rel="stylesheet" type="text/css">
<link href="<?php echo cdn_url(THEMES_URL.'group_site/'.$themes.'/src/css/layout_ie.css');;?>" rel="stylesheet" type="text/css">
<![endif]-->
<div style="display:none">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F5698333870a14f89a37f58c9c30ffe60' type='text/javascript'%3E%3C/script%3E"));
</script>
</div>