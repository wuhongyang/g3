<?php
include '../data/library/global.fun.php';
//$db  = db::connect(config('database','default'));

//$sql = "SELECT COUNT(*) FROM g3_test.test_api WHERE `status`>0";
//$total = $db->get_var($sql);
//$shownum = 20;
//$page  = intval($_GET['page']);
//$prev = $page - $shownum < 0? 0 : $page - $shownum;
//$next = $page + $shownum > $total? $page : $page + $shownum;

//$sql = "select * from g3_test.test_api ORDER BY bigcase_id ASC,case_id ASC,parent_id ASC,child_id ASC LIMIT {$page},{$shownum}";
//$result = $db->get_results($sql,'ASSOC');
//$mongo = db::connect(config('mongodb','ktv'),'mongo');
$mongo = domain::main()->GroupDBConn('mongo');
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
      <th align="left">请求科目</th>
      <th>请求次数</th>
      <th>总时间</th>
      <th>平均时间</th>
      <th>请求明细</th>
    </tr>
  </thead>
  <tbody>
    <?php
	//foreach($result as $key=>$list){
	$map = new MongoCode("function(){".
		"emit(".
			"{bigcase_id:this.bigcase_id,case_id:this.case_id,parent_id:this.parent_id,child_id:this.child_id},".
			"{rect:1,msum:parseFloat(this.runtime)}".
		");".
	"}");
	$reduce = new MongoCode("function(k,vals){".
		"var ret = {rect:0,msum:0};".
		"for (var i in vals){".
			"ret.msum += vals[i].msum;".
			"ret.rect += vals[i].rect;".
		"}".
		"return ret;".
	"}");
	$info = $mongo->command('kkyoo_action.xhprof_runid',array(
			"mapreduce" => 'xhprof_runid',
			"map" => $map,
			"reduce" => $reduce,
			//"query" => array('bigcase_id'=>(int)$list['bigcase_id'],'case_id'=>(int)$list['case_id'],'parent_id'=>(int)$list['parent_id'],'child_id'=>(int)$list['child_id']),
			"out" => "xhprof_result"
		),
		array(
			'sort' => array(
				'value.msum' => -1
			)
		)
	);
	//$total = $mongo->fetch('kkyoo_action.xhprof_runid',array('bigcase_id'=>(int)$list['bigcase_id'],'case_id'=>(int)$list['case_id'],'parent_id'=>(int)$list['parent_id'],'child_id'=>(int)$list['child_id']),array(),array('COUNT'));
	//$total = $total[0]['COUNT'];
	//$total = json_decode(socket_request(MONGO_API_PATH.'?cmd={"db":"kkyoo_action","table":"xhprof_runid","total":[],"where":{"bigcase_id":'.$list['bigcase_id'].',"case_id":'.$list['case_id'].',"parent_id":'.$list['parent_id'].',"child_id":'.$list['child_id'].'}}'),true);
	//$total = $total['total'];
	foreach($info as $key=>$list){
	?>
    <tr<?php if($key % 2 != 0) echo ' class="bg"'; ?>>
      <td><?php echo "{$list['_id']['bigcase_id']} &raquo; {$list['_id']['case_id']} &raquo; {$list['_id']['parent_id']} &raquo; {$list['_id']['child_id']}" ?></td>
      <td align="center"><?php echo $list['value']['rect'] ?></td>
      <td align="center"><?php echo $list['value']['msum']; ?></td>
      <td align="center"><?php echo $list['value']['rect'] > 0 ? round($list['value']['msum'] / $list['value']['rect'],4) : 0; ?></td>
      <td align="center"><a href="./xhprof.php<?php echo "?bigcase_id={$list['_id']['bigcase_id']}&case_id={$list['_id']['case_id']}&parent_id={$list['_id']['parent_id']}&child_id={$list['_id']['child_id']}" ?>" target="_blank">查看</a></td>
    </tr>
    <?php
		ob_flush();
		flush();
	}
	?>
  </tbody>
  <tfoot>
    <tr>
      <!--<td colspan="5" align="center">当前第<?php echo $page / $shownum + 1;?>页 <a href="?page=<?php echo $prev ?>">上一页</a> <a href="?page=<?php echo $next ?>">下一页</a></td>-->
    </tr>
  </tfoot>
</table>
</body>
</html>