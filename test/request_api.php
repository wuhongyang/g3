<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>测试结果</title>
<style type="text/css">
body{color:#C00;font-weight:bold;}
</style>
</head>

<body>
<?php
$num = $_POST['num'];
$http = $_POST['http'];
$param = $_POST['param'];
$load_type = $_POST['load_type'];
$parallel_num = $_POST['parallel_num'];

for($i=0;$i<$parallel_num;$i++){
	$target = $load_type == 'window' ? '_blank' : 'iframe_'.$i;
?>
	<?php
	if($load_type == 'iframe'){
		echo '#'.($i+1);
	?>
		<iframe name="iframe_<?php echo $i;?>" src="" width="100%" height="300" frameborder="0" scrolling="yes"></iframe>
	<?php }?>
	<form id="load_<?php echo $i;?>" action="load_api.php" method="post" target="<?php echo $target;?>">
	<input type='hidden' name='num' value='<?php echo $num;?>' />
	<input type='hidden' name='http' value='<?php echo $http;?>' />
	<?php foreach(($param) as $row){?>
		<input type='hidden' name='param[]' value='<?php echo $row;?>' />
	<?php }?>
	</form>
<?php }?>
<script language="javascript">
function getBrowser(){
   if(navigator.userAgent.indexOf("MSIE")>0)return 1;//IE
   if(isFirefox=navigator.userAgent.indexOf("Firefox")>0)return 2;//Firefox
   if(isSafari=navigator.userAgent.indexOf("Chrome")>0)return 3;//Chrome
   if(isSafari=navigator.userAgent.indexOf("Safari")>0)return 4;//Safari
   if(isCamino=navigator.userAgent.indexOf("Camino")>0)return 5;//Camino
   if(isMozilla=navigator.userAgent.indexOf("Gecko/")>0)return 6;//Gecko
   return 0;//other...
}
function submitAllForm(){
	for(var i=0;i<<?php echo $parallel_num;?>;i++){
		document.getElementById('load_'+i).submit();
	}
	<?php
	if($load_type == 'window'){
	?>
	//if(getBrowser() < 3){
		window.opener = null;
		window.open('', '_self');
		window.close();
	//}
	<?php }?>
}
window.onload = function(){submitAllForm();}
</script>
</body>
</html>