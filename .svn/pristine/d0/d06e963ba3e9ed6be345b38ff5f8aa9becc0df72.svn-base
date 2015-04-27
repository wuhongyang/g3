<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$param = array(
  'extparam' => array('Tag'=>'GetActivityRanks','Type'=>'week','Ruleid'=>27,'Uptime'=>(int)date('oW'),'Rows'=>10,'Roomid'=>1398),
  'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101) 
);
$zjhrank = (array)request($param);


$param = array(
  'extparam' => array('Tag'=>'GetActivityRanks','Type'=>'week','Ruleid'=>25,'Uptime'=>(int)date('oW'),'Rows'=>10,'Roomid'=>1398),
  'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$dnrank = (array)request($param);


$param = array(
  'extparam' => array('Tag'=>'GetActivityRanks','Type'=>'week','Ruleid'=>26,'Uptime'=>(int)date('oW'),'Rows'=>10,'Roomid'=>1398),
  'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$ddzrank = (array)request($param);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>谁是她的守护者</title>
<style type="text/css">
body{ font-size:14px; font-family:"微软雅黑"; color:#FFF; margin:0 auto; width:100%; background:url(/static/guardian/0_07.jpg) repeat;}
.top{ width:100%; height:815px; margin: 0 auto; background:url(/static/guardian/0_01.jpg) repeat-x;}
.Tnro2{ width:999px; margin:0 auto; height:815px;}
.Tnro2 p{ padding:0; margin:0 auto;}
.btnFJ{ width:164px; height:42px; margin:0 auto;}
.btnFJ a{ width:164px; height:42px; display:block; background:url(/static/guardian/b_06.jpg) no-repeat;}
.btnFJ a:hover{ width:164px; height:42px;display:block; background:url(/static/guardian/bb_06.jpg) no-repeat;}
.topOne{ color:#fff000;}
.foot{ float:left; width:100%; height:88px; color:#a9a9a9;}
.foot a{ color:#a9a9a9;text-decoration: none;}
.foot a:hover{ color:#f03b9d;text-decoration: underline;}
</style>
</head>

<body>
<div class="top">
<div class="Tnro2">
<p><img src="{STATIC_API_PATH}/static/guardian/0_02.jpg" width="999" height="105" /></p>
<p><img src="{STATIC_API_PATH}/static/guardian/0_04.jpg" width="999" height="349" /></p>
<div style="background:url(/static/guardian/0_05.jpg) no-repeat; height:42px; width:999px;">
<div class="btnFJ"><a href="http://www.vvku.com/v/1398"></a></div>
</div>
<p><img src="{STATIC_API_PATH}/static/guardian/0_06.jpg" width="999" height="319" /></p>
</div>
<div style=" width:999px; height:428px; margin:0 auto 30px auto;"><img src="{STATIC_API_PATH}/static/guardian/0_08.jpg" width="999" height="428" /></div>
<div style="width:999px; height:auto; margin:0 auto;">
<div style="width:321px; height:454px; float:left; margin-left:10px; background:url(/static/guardian/0_12.jpg) no-repeat; margin-bottom:40px;">
<table width="305" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="37" colspan="3" align="center"><img src="{STATIC_API_PATH}/static/guardian/0_18.jpg" width="138" height="37" /></td>
    </tr>
  <tr>
    <td width="15%" height="35" align="center">排位</td>
    <td width="55%" height="35" align="center">昵称ID</td>
    <td width="25%" height="35" align="center">贡献值</td>
  </tr>
  <?php foreach((array)$zjhrank['Result'] as $key=>$rank): ?>
  <tr<?php if($key<1): ?> class="topOne"<?php endif; ?>>
    <td height="35" align="center"><?php echo $key+1 ?></td>
    <td height="35" align="center"><?php echo $rank['Nick'] ?>(<?php echo $rank['UinId'] ?>)</td>
    <td height="35" align="center"><?php echo $rank['Weight'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
<div style="width:321px; height:454px; float:left; margin-left:10px; background:url(/static/guardian/0_12.jpg) no-repeat; margin-bottom:40px;">
<table width="305" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="37" colspan="3" align="center"><img src="{STATIC_API_PATH}/static/guardian/0_15.jpg" width="116" height="37" /></td>
    </tr>
  <tr>
    <td width="15%" height="35" align="center">排位</td>
    <td width="55%" height="35" align="center">昵称ID</td>
    <td width="25%" height="35" align="center">贡献值</td>
  </tr>
  <?php foreach((array)$dnrank['Result'] as $key=>$rank): ?>
  <tr<?php if($key<1): ?> class="topOne"<?php endif; ?>>
    <td height="35" align="center"><?php echo $key+1 ?></td>
    <td height="35" align="center"><?php echo $rank['Nick'] ?>(<?php echo $rank['UinId'] ?>)</td>
    <td height="35" align="center"><?php echo $rank['Weight'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
<div style="width:321px; height:454px; float:left; margin-left:10px; background:url(/static/guardian/0_12.jpg) no-repeat; margin-bottom:40px;">
<table width="305" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="37" colspan="3" align="center"><img src="{STATIC_API_PATH}/static/guardian/0_20.jpg" width="136" height="37" /></td>
    </tr>
  <tr>
    <td width="15%" height="35" align="center">排位</td>
    <td width="55%" height="35" align="center">昵称ID</td>
    <td width="25%" height="35" align="center">贡献值</td>
  </tr>
  <?php foreach((array)$ddzrank['Result'] as $key=>$rank): ?>
  <tr<?php if($key<1): ?> class="topOne"<?php endif; ?>>
    <td height="35" align="center"><?php echo $key+1 ?></td>
    <td height="35" align="center"><?php echo $rank['Nick'] ?>(<?php echo $rank['UinId'] ?>)</td>
    <td height="35" align="center"><?php echo $rank['Weight'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
</div>
<div  class="foot">
<table width="999" border="0" cellspacing="0" cellpadding="0" style="font-size:12px; font-family:Georgia, 'Times New Roman', Times, serif;">
  <tr>
    <td height="25" align="center"><a href="http://www.vvku.com/about.html">关于我们</a>|   <a href="http://www.vvku.com/contactus.html">联系我们</a>|   <a href="http://www.vvku.com/agreement.html">用户协议</a>|   <a href="http://www.vvku.com/employ.html">高薪诚聘</a>|   <a href="http://www.vvku.com/custom.html">客服中心</a>|   <a href="http://www.vvku.com/agent.html">渠道代理</a><br /></td>
    </tr>
  <tr>
    <td height="25" align="center">Copyright © 2006-2013 vvku.com 版权所有 浙ICP备12027086号-1</td>
    </tr>
</table>
</div>
</div>

</div>
</body>
</html>
