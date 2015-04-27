<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$firstWeekTimeStamp = strtotime('2013-4-16');
$param = array(
    'extparam' => array('Tag'=>'GetRoomRank','Period'=>array('week'),'Ruleid'=>28,'Time'=>$firstWeekTimeStamp,'Rows'=>3),
    'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>111)
);
$result = request($param);
$firstWeekMoneyRank = (array)$result['Result']['week'];
unset($result);

$param = $param = array(
    'extparam' => array('Tag'=>'GetRoomRank','Period'=>array('week'),'Ruleid'=>29,'Time'=>$firstWeekTimeStamp,'Rows'=>3),
    'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>111)
);
$result = request($param);
$firstWeekRank = (array)$result['Result']['week'];
unset($result);

$secondWeekTimeStamp = strtotime('2013-4-27');
$param = array(
    'extparam' => array('Tag'=>'GetRoomRank','Period'=>array('week'),'Ruleid'=>28,'Time'=>$secondWeekTimeStamp,'Rows'=>3),
    'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>111)
);
$result = request($param);
$secondWeekMoneyRank = (array)$result['Result']['week'];
unset($result);

$param = $param = array(
    'extparam' => array('Tag'=>'GetRoomRank','Period'=>array('week'),'Ruleid'=>29,'Time'=>$secondWeekTimeStamp,'Rows'=>3),
    'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>111)
);
$result = request($param);
$secondWeekRank = (array)$result['Result']['week'];
unset($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>我是人气积分王--VV酷人气积分快速成长计划</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="VV酷会员成长，人气，积分" />
<meta name="description" content="VV酷我是人气积分王，人气、积分快速成长计划"/>
<link href="css/modal-bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php include dirname(__FILE__).'/header.html' ?>
<div class="main">
    <div class="content">
        <div class="topbox">
            <a href="javascript:void(0);" class="jfbtncs" style="left:542px;top:-86px;">我要升积分</a>
            <a href="javascript:void(0);" class="rqbtncs" style="left:704px;top:-86px;">我要升人气</a>
        </div>
        <div class="step">
            <h1 class="tit1">
                1重礼
                <a href="javascript:void(0);" class="sjbtncs" style="left:257px;top:21px;">升级任务</a>
            </h1>
            <div class="giftbox">
                <div class="left-wrap">
                    <div>
                        <h2>"积分王"奖品：</h2>
                        <a href="javascript:void(0);" class="jfbtncs" style="left:422px;top:5px;">我要升积分</a>
                    </div>
                    <div>
                        <p class="picwrap"><img src="images/11.png" width="183" height="142" /></p>
                        <p class="picwrap"><img src="images/12.png" width="183" height="142" /></p>
                        <p class="picwrap"><img src="images/13.png" width="183" height="142" /></p>
                    </div>
                </div>
                <div class="right-wrap">
                    <h3 class="tit1">积分王排行榜：</h3>
                    <div class="door_container">
                        <div class="TabTitle">
                            <ul id="myTab">
                            <li class="active" onclick="nTabs(this,0);">第一周<span style="font-size:10px; font-weight:normal;">（4.15-4.21）</span></li>
                            <li class="normal" onclick="nTabs(this,1);">第二周<span style="font-size:10px; font-weight:normal;">（4.22-4.28）</span></li>
                            </ul>
                        </div>
                        <div class="TabContent">
                            <div id="myTab_Content0">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tablecs">
                                  <tr>
                                    <th>排名</th>
                                    <th>昵称</th>
                                    <th>积分</th>
                                  </tr>
                                  <?php if(!empty($firstWeekMoneyRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#eb008b;">第一名</span></td>
                                    <td><?php echo empty($firstWeekMoneyRank[0]['Nick']) ? $firstWeekMoneyRank[0]['UinId'] : $firstWeekMoneyRank[0]['Nick']; ?></td>
                                    <td><?php echo $firstWeekMoneyRank[0]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($firstWeekMoneyRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#155a94;">第二名</span></td>
                                    <td><?php echo empty($firstWeekMoneyRank[1]['Nick']) ? $firstWeekMoneyRank[1]['UinId'] : $firstWeekMoneyRank[1]['Nick']; ?></td>
                                    <td><?php echo $firstWeekMoneyRank[1]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($firstWeekMoneyRank[0])): ?>
                                  <tr>
                                    <td>第三名</td>
                                    <td><?php echo empty($firstWeekMoneyRank[2]['Nick']) ? $firstWeekMoneyRank[2]['UinId'] : $firstWeekMoneyRank[2]['Nick']; ?></td>
                                    <td><?php echo $firstWeekMoneyRank[2]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                </table>
                            </div>
                            <div class="none" id="myTab_Content1">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tablecs">
                                  <tr>
                                    <th>排名</th>
                                    <th>昵称</th>
                                    <th>积分</th>
                                  </tr>
                                  <?php if(!empty($secondWeekMoneyRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#eb008b;">第一名</span></td>
                                    <td><?php echo empty($secondWeekMoneyRank[0]['Nick']) ? $secondWeekMoneyRank[0]['UinId'] : $secondWeekMoneyRank[0]['Nick']; ?></td>
                                    <td><?php echo $secondWeekMoneyRank[0]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($secondWeekMoneyRank[1])): ?>
                                  <tr>
                                    <td><span style="color:#155a94;">第二名</span></td>
                                    <td><?php echo empty($secondWeekMoneyRank[1]['Nick']) ? $secondWeekMoneyRank[1]['UinId'] : $secondWeekMoneyRank[1]['Nick']; ?></td>
                                    <td><?php echo $secondWeekMoneyRank[1]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($secondWeekMoneyRank[2])): ?>
                                  <tr>
                                    <td>第三名</td>
                                    <td><?php echo empty($secondWeekMoneyRank[2]['Nick']) ? $secondWeekMoneyRank[2]['UinId'] : $secondWeekMoneyRank[2]['Nick']; ?></td>
                                    <td><?php echo $secondWeekMoneyRank[2]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="step">
            <h1 class="tit2">2重礼</h1>
            <div class="giftbox">
                <div class="left-wrap">
                    <div>
                        <h2>"积分达人"奖品：</h2>
                        <a href="javascript:void(0);" class="rqbtncs" style="left:422px;top:5px;">我要升人气</a>
                    </div>
                    <div>
                        <p class="picwrap"><img src="images/21.png" width="265" height="138" /></p>
                        <p class="picwrap"><img src="images/22.png" width="265" height="138" /></p>
                    </div>
                </div>
                <div class="right-wrap">
                    <h3 class="tit2">积分王排行榜：</h3>
                    <div class="door_container">
                        <div class="TabTitle">
                            <ul id="myTab2">
                            <li class="active" onclick="nTabs(this,0);">第一周<span style="font-size:10px; font-weight:normal;">（4.15-4.21）</span></li>
                            <li class="normal" onclick="nTabs(this,1);">第二周<span style="font-size:10px; font-weight:normal;">（4.22-4.28）</span></li>
                            </ul>
                        </div>
                        <div class="TabContent">
                            <div id="myTab2_Content0">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tablecs">
                                  <tr>
                                    <th>排名</th>
                                    <th>昵称</th>
                                    <th>积分</th>
                                  </tr>
                                  <?php if(!empty($firstWeekRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#eb008b;">第一名</span></td>
                                    <td><?php echo empty($firstWeekRank[0]['Nick']) ? $firstWeekRank[0]['UinId'] : $firstWeekRank[0]['Nick']; ?></td>
                                    <td><?php echo $firstWeekRank[0]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($firstWeekRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#155a94;">第二名</span></td>
                                    <td><?php echo empty($firstWeekRank[1]['Nick']) ? $firstWeekRank[1]['UinId'] : $firstWeekRank[1]['Nick']; ?></td>
                                    <td><?php echo $firstWeekRank[1]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($firstWeekRank[0])): ?>
                                  <tr>
                                    <td>第三名</td>
                                    <td><?php echo empty($firstWeekRank[2]['Nick']) ? $firstWeekRank[2]['UinId'] : $firstWeekRank[2]['Nick']; ?></td>
                                    <td><?php echo $firstWeekRank[2]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                </table>
                            </div>
                            <div class="none" id="myTab2_Content1">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tablecs">
                                  <tr>
                                    <th>排名</th>
                                    <th>昵称</th>
                                    <th>积分</th>
                                  </tr>
                                  <?php if(!empty($secondWeekRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#eb008b;">第一名</span></td>
                                    <td><?php echo empty($secondWeekRank[0]['Nick']) ? $secondWeekRank[0]['UinId'] : $secondWeekRank[0]['Nick']; ?></td>
                                    <td><?php echo $secondWeekRank[0]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($secondWeekRank[0])): ?>
                                  <tr>
                                    <td><span style="color:#155a94;">第二名</span></td>
                                    <td><?php echo empty($secondWeekRank[1]['Nick']) ? $secondWeekRank[1]['UinId'] : $secondWeekRank[1]['Nick']; ?></td>
                                    <td><?php echo $secondWeekRank[1]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                  <?php if(!empty($secondWeekRank[0])): ?>
                                  <tr>
                                    <td>第三名</td>
                                    <td><?php echo empty($secondWeekRank[2]['Nick']) ? $secondWeekRank[2]['UinId'] : $secondWeekRank[2]['Nick']; ?></td>
                                    <td><?php echo $secondWeekRank[2]['Weight']; ?></td>
                                  </tr>
                                  <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <div class="step">
            <h1 class="tit3">3重礼</h1>
            <div class="giftbox">
                <div class="left-wrap">
                    <div>
                        <h2>其他活动同步奖品：</h2>
                    </div>
                    <div>
                        <p class="picwrap"><img src="images/31.png" width="265" height="138" /></p>
                        <p class="picwrap"><img src="images/32.png" width="265" height="138" /></p>
                    </div>
                </div>
                <div class="right-wrap" style="padding-top:20px;">
                    <img src="images/text.png" width="222" height="169" />
                </div>
            </div>
        </div>
        <div class="step">
            <h1 class="tit4">4重礼</h1>
            <div style="height:150px;margin-bottom:20px;">
                <p class="picwrap" style="margin:0 25px;"><img src="images/41.png" width="250" height="149" /></p>
                <p class="picwrap" style="margin:0 25px;"><img src="images/42.png" width="250" height="149" /></p>
                <p class="picwrap" style="margin:0 20px;"><img src="images/43.png" width="250" height="149" /></p>
            </div>
        </div>
        <!--step4 end-->
        <div class="topbox2">
            <h5>活动规则：</h5>
            <pre>
                一、活动时间：2013-4-15至2013-4-28。
                二、参与条件：本站的注册会员。
                三、参与方式：1.可以通过升级任务，持续提升用户积分。
                                     2.可以通过接受别人用户人气票，提升自己的人气数。
                四、奖品发放：1.全站会员积分最高的前三名用户可获得积分王奖励。
                                     2.全站会员人气票数最高的前两名用户可获得人气达人奖励。
                                     3.凡是参加该活动得奖的用户均有机会获得百元红包大奖。
                                     4.赠送奖品将在活动结束日2013年4月29日为您发放，节假日顺延。
                                     5.如有疑问，请联系官方运营QQ：787699583、810892644。
            </pre>
        </div>
    </div>        
</div>
<?php include dirname(__FILE__).'/footer.html'; ?>

<div id="jfpop" class="modal hide fade apop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
    <iframe src="/activity/jfpopbox.php" width="680" height="490" scrolling="no"></iframe>
</div>
<div id="rqpop" class="modal hide fade apop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width:auto;">
    <iframe src="/activity/rqpopbox.php" width="680" height="300" scrolling="no"></iframe>
</div>
</body>
</html>
<script src="js/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
var pop = $('.apop');
$('.jfbtncs').click(function(){
    $('#jfpop').modal();
});

$('.rqbtncs').click(function(){
    $('#rqpop').modal();
});

$('.sjbtncs').click(function(){
    $('#jfpop').modal();
});

function nTabs(thisObj,Num){
    if(thisObj.className == "active")return;
    var tabObj = thisObj.parentNode.id;
    var tabList = document.getElementById(tabObj).getElementsByTagName("li");
    for(i=0; i <tabList.length; i++)
    {
        if (i == Num)
        {
            thisObj.className = "active";
            document.getElementById(tabObj+"_Content"+i).style.display = "block";
        }else{
            tabList[i].className = "normal";
            document.getElementById(tabObj+"_Content"+i).style.display = "none";
        }
    }
}
</script>