<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台登录 - vv酷在线娱乐</title>
<link href="template/css/alogin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form id="form1" action="index.php" method="post">
  <div class="Main">
    <ul>
      <li class="top"></li>
      <li class="top2"></li>
      <li class="topA"></li>
      <li class="topB"><span><img src="template/images/login/logo.gif" alt="" style="" /></span></li>
      <li class="topC"></li>
      <li class="topD">
        <ul class="login">
          <li><span class="left">用户名：</span> <span style="left">
            <input id="user" type="text" name="username" class="txt" />
            </span></li>
          <li><span class="left">密 码：</span> <span style="left">
            <input id="password" type="password" name="password" class="txt" />
            </span></li>
          <li><span class="left">验证码：</span> <span style="left">
            <input id="captcha" type="text" name="captcha" class="txtCode" /> <img src="captcha.php" onclick="this.src=this.src+'?1'" title="看不清？点击换一张！" align="absmiddle" />
            </span></li>
        </ul>
      </li>
      <li class="topE"></li>
      <li class="middle_A"></li>
      <li class="middle_B"></li>
      <li class="middle_C"><input value="" type="submit" class="btn"></li>
      <li class="middle_D"></li>
      <li class="bottom_A"></li>
      <li class="bottom_B">vv酷在线娱乐社区，当前连接：<?php echo $_SERVER['HOSTNAME'];?> ！</li>
    </ul>
  </div>
</form>
</body>
</html>
