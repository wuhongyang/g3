<?php
set_time_limit(0);
require '../data/library/global.fun.php';
$db  = db::connect(config('database','default'));
$sql = "select * from g3_test.test_api where `status`>0 ORDER BY bigcase_id ASC,case_id ASC,parent_id ASC,child_id ASC";
$result = $db->get_results($sql,'ASSOC');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PHP测试结果</title>
<style type="text/css">
*{padding:0;margin:0;}
table td{padding:5px;}
table thead tr{background:#BEC8DC;padding:5px;}
table tbody tr:hover{background:#FFC;}
.bg{background:#DDD;}
i{color:#999;}
</style>
</head>

<body>
<table cellpadding="0" style="margin:10px auto" cellspacing="0" width="60%">
<thead>
<tr>
<th>&nbsp;</th>
<th align="left">请求科目</th>
<th>请求次数</th>
<th>并发数</th>
<th>成功次数</th>
<th>请求最长时间</th>
<th>请求最短时间</th>
<th>平均请求时间</th>
</tr>
</thead>
<tbody>
<?php
foreach($result as $key=>$val){
	$sql = "SELECT COUNT(*) AS reqnum,MAX(runtime) AS maxtime,MIN(runtime) AS mintime,AVG(runtime) AS avgtime,AVG(paiallel) AS paiallel FROM g3_test.test_result WHERE parent_id={$val['parent_id']} AND child_id={$val['child_id']}";
	$reqresult = $db->get_row($sql,'ASSOC');
	$sql = "SELECT COUNT(*) FROM g3_test.test_result WHERE parent_id={$val['parent_id']} AND child_id={$val['child_id']} AND flag=100";
	$success = $db->get_var($sql);
?>
<tr>
<td align="center"><?php echo $key ?></td>
<td title="<?php echo $val['descr'] ?>"><?php echo "{$val['bigcase_id']} &raquo; {$val['case_id']} &raquo; {$val['parent_id']} &raquo; {$val['child_id']}" ?></td>
<td align="center"><?php echo $reqresult['reqnum'] ?></td>
<td align="center"><?php echo $reqresult['paiallel'] ?></td>
<td align="center"><?php echo $success ?></td>
<td align="center"><?php echo $reqresult['maxtime'] ?></td>
<td align="center"><?php echo $reqresult['mintime'] ?></td>
<td align="center"><?php echo $reqresult['avgtime'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
