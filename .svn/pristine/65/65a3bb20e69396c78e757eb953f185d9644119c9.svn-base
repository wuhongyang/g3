<?php
include '../data/library/global.fun.php';
$total = json_decode(socket_request(MONGO_API_PATH.'?cmd={"db":"kkyoo_action","table":"xhprof_runid","total":[],"where":{"bigcase_id":'.$_GET['bigcase_id'].',"case_id":'.$_GET['case_id'].',"parent_id":'.$_GET['parent_id'].',"child_id":'.$_GET['child_id'].'}}'),true);
$total = $total['total'];
$shownum = 20;
$page  = intval($_GET['page']);
$prev = $page - $shownum < 0? 0 : $page - $shownum;
$next = $page + $shownum > $total? $page : $page + $shownum;

$lists = json_decode(socket_request(MONGO_API_PATH.'/?cmd={"db":"kkyoo_action","table":"xhprof_runid","fields":[],"where":{"bigcase_id":'.$_GET['bigcase_id'].',"case_id":'.$_GET['case_id'].',"parent_id":'.$_GET['parent_id'].',"child_id":'.$_GET['child_id'].'},"option":{"limit":"'.$shownum.'","skip":"'.$page.'","sort":[["time","desc"]]}}'),true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Xhprof分析查看</title>
<style type="text/css">
*{padding:0;margin:0;}
table td{padding:10px 5px;}
table thead tr{background:#BEC8DC;padding:5px;}
table tbody tr:hover{background:#FFC;}
.bg{background:#DDD;}
i{color:#999;}
</style>
</head>

<body>
<table cellpadding="0" style="margin:10px auto" cellspacing="0" width="960">
  <thead>
    <tr>
      <th width="680">请求科目</th>
      <th width="680">ApacheID</th>
      <th width="200">耗时</th>
      <th width="200">请求时间</th>
      <th width="80">效率分析</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($lists['record'] as $key=>$list): ?>
    <tr<?php if($key % 2 != 0) echo ' class="bg"'; ?>>
      <td><?php echo "{$list['bigcase_id']} &raquo; {$list['case_id']} &raquo; {$list['parent_id']} &raquo; {$list['child_id']}" ?></td>
      <td align="center"><?php echo $list['mypid']; ?></td>
      <td align="center"><?php echo $list['runtime']; ?></td>
      <td align="center"><i><?php echo $list['time'] ?></i></td>
      <td align="center"><a href="./xhprof_html/?run=<?php echo $list['runid'] ?>" target="_blank">查看</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="3" align="center">当前第<?php echo $page / $shownum + 1;?>页 <a href="?<?php $_GET['page'] = $prev; echo http_build_query($_GET) ?>">上一页</a> <a href="?<?php $_GET['page'] = $next; echo http_build_query($_GET) ?>">下一页</a></td>
    </tr>
  </tfoot>
</table>
</body>
</html>