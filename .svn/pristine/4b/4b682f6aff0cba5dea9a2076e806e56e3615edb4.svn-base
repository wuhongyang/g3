<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$param=array(
    'extparam'=>array('Tag'=>'VipRank'),
    'param'=>array('BigCaseId'=>10006,'CaseId'=>10048,'ParentId'=>10267,'ChildId'=>101,'Desc'=>'会员列表')
);
$rank = request($param);
$rank = $rank['Rank'];
$top1 = $rank[0];
$top2 = $rank[1];
$top3 = $rank[2];
unset($rank[0],$rank[1],$rank[2]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>争做站内VIP</title>
<link href="/activity/css/vip.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php include dirname(__FILE__).'/header.html' ?>
<div class="main">
    <div class="w1600">
        <div class="w962" style="background:url(images/vip/vip_01.jpg) 0 0 no-repeat;width:962px;height:212px;"></div>
        <div class="w962" style="background:url(images/vip/vip_02.jpg) 0 0 no-repeat;width:962px;height:152px;"></div>
        <div class="w962" style="background:url(images/vip/vip_03.jpg) 0 0 no-repeat;width:962px;height:290px;"></div>
        <div class="w962" style="background:url(images/vip/vip_04.jpg) 0 0 no-repeat;width:962px;height:489px;position:relative;">
            <table width="215" border="0" cellspacing="0" cellpadding="0" class="tables">
                <tr>
                    <th>站名称</th>
                    <th>VIP会员数</th>
                </tr>
                <?php if(!empty($top1)): ?>
                <tr>
                    <td><span style="color:#eb008b;"><?php echo $top1['name']; ?></span></td>
                    <td><span style="color:#eb008b;"><?php echo $top1['vip_nums']; ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(!empty($top2)): ?>
                <tr>
                    <td><span style="color:#07749d;"><?php echo $top2['name']; ?></span></td>
                    <td><span style="color:#07749d;"><?php echo $top2['vip_nums']; ?></span></td>
                </tr>
                <?php endif; ?>
                <?php if(!empty($top3)): ?>
                <tr>
                    <td><span style="color:#8ab301;"><?php echo $top3['name']; ?></span></td>
                    <td><span style="color:#8ab301;"><?php echo $top3['vip_nums']; ?></span></td>
                </tr>
                <?php endif; ?>
                <?php foreach((array)$rank as $val): ?>
                <tr>
                    <td><?php echo $val['name']; ?></td>
                    <td><?php echo $val['vip_nums']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="w962" style="background:url(images/vip/vip_05.jpg) 0 0 no-repeat;width:962px;height:360px;"></div>
    </div>
</div>
<?php include dirname(__FILE__).'/footer.html' ?>
</body>
</html>
