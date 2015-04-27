<?php
set_time_limit(0);
require '../data/library/global.fun.php';
$db  = db::connect(config('database','default'));
$sql = "select * from g3_test.test_httpload ORDER BY bigcase_id ASC,case_id ASC,parent_id ASC,child_id ASC";
$result = $db->get_results($sql,'ASSOC');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>http_load测试结果</title>
<style type="text/css">
*{padding:0;margin:0;}
body{font-size:14px;line-height:22px;}
table td{padding:5px;}
table thead tr{background:#BEC8DC;padding:5px;}
table tbody tr:hover{background:#FFC;}
.bg{background:#DDD;}
i{color:#999;}
.descr{position:fixed;right:10px;top:28px;width:34%;}
</style>
</head>

<body>
<div class="descr">
49 fetches, 2 max parallel, 289884 bytes, in 10.0148 seconds<br />
运行了49个请求，最大的并发进程数是2，总计传输的数据是289884，运行的时间是10.0148秒<br />
5916 mean bytes/connection<br />
每一连接平均传输的数据量289884/49=5916<br />
4.89274 fetches/sec, 28945.5 bytes/sec<br />
每秒的响应请求为4.89274，每秒传递的数据为28945.5 bytes/sec<br />
msecs/connect: 28.8932 mean, 44.243 max, 24.488 min<br />
每连接平均响应时间是28.8932 msecs，最大的响应时间44.243 msecs，最小的响应时间24.488 msec<br />
</div>
<table cellpadding="0" style="margin:10px" cellspacing="0" width="60%">
<thead>
<tr>
<th>&nbsp;</th>
<th>请求科目</th>
<th>测试结果</th>
</tr>
</thead>
<tbody>
<?php
foreach($result as $key=>$val){
?>
<tr>
<td align="center"><?php echo $key ?></td>
<td align="center"><?php echo "{$val['bigcase_id']} &raquo; {$val['case_id']} &raquo; {$val['parent_id']} &raquo; {$val['child_id']}" ?></td>
<td align="center"><?php echo $val['result'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>
