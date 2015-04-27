<?php if (!class_exists('template')) die('Access Denied');?>
<form class="search" onsubmit="__search(this.keywords.value);return false;" id="_search">
<div class="input-append">
  <input id="keywords" name="keywords" type="text" placeholder="房间/艺人" />
  <button class="btn" type="submit"><i class="icon-search"></i></button>
</div>
</form>