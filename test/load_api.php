<?php
set_time_limit(0);
require '../library/global.fun.php';

$num = $_POST['num'];
$postparam = $_POST['param'];
while($num--){
	foreach($postparam as $key=>$param){
		$param = json_decode($param,true);
		$row_start = microtime(true);
		$row = request($param,$_POST['http']);
		$result[$key][$row['Flag']]['Num'] += 1;
		$result[$key][$row['Flag']]['FlagString'] = $row['FlagString'];
		$result[$key][$row['Flag']]['Result'] = json_encode($row);
		$row_end = microtime(true) - $row_start;
		$row_time[$key] += $row_end;
		if($row_end > $maxtime[$key]) $maxtime[$key] = $row_end;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $_POST['num'] ?>次测试结果</title>
<style type="text/css">
*{padding:0;margin:0;font-size:12px;}
.row{border:solid 1px #CCC;line-height:25px;padding:10px;margin:5px;}
.row:hover{background:#FFC;}
.row p{padding:5px 20px;}
.row p span{margin-right:20px;color:#C00; font-weight:bold;}
.testCount{padding:20px 10px;}
</style>
</head>

<body>
<?php
foreach($postparam as $key=>$param){
	$i = $key+1;
	$row = "<div class=\"row\"><h3>#{$i} {$param}</h3>";
	$row .= "<p><span>总计时间：".number_format($row_time[$key],4)." 秒 &nbsp; 最大时间：".number_format($maxtime[$key],4)." 秒 &nbsp; 平均时间：".number_format($row_time[$key] / $_POST['num'],4)." 秒</span></p>";
	foreach($result[$key] as $flag=>$rst){
		$row .= "<p><span>{$rst['FlagString']} {$rst['Num']} 次</span>{$rst['Result']}</p>";
	}
	$row .= '</div>';
	echo $row;
}

$totalTime = array_sum($row_time);
$totalNum  = $_POST['num'] * count($postparam);
$totalMaxTime = max($maxtime);
$totalMinTime = min($maxtime);
?>

<div class="testCount">
总计时间：<?php echo number_format($totalTime,4) ?> 秒，
平均每次请求时间：<?php echo number_format($totalTime / $totalNum,4) ?> 秒，
每次请求最长时间：<?php echo number_format($totalMaxTime,4) ?> 秒，
每次请求最短时间：<?php echo number_format($totalMinTime,4) ?> 秒
</div>
</body>
</html>