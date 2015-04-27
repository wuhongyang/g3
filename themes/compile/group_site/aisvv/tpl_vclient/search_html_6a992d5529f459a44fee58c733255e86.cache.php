<?php if (!class_exists('template')) die('Access Denied');?>
<?php if($searchConfig['common_search']==1) { ?>
<form class="search" onsubmit="__search(this.keywords.value);return false;" id="_search">
<div class="input-append">
  <input id="keywords" name="keywords" type="text" placeholder="房间/艺人" />
  <button class="btn" type="submit"><i class="icon-search"></i></button>
</div>
</form>
<?php } ?>
<?php if($searchConfig['vip_search']==1) { ?>
<dl class="body-side-list">
    <dt><i class="home-icon home-search"></i><h3><?php echo $searchConfig['title'];?></h3></dt>
    <dd>
    <form class="sit-form-search" method="get" action="search.php">
    <input type="hidden" name="module" value="s" />
        <div class="control-group">
            <span>性别:</span>
            <label class="radio inline">
                <input type="radio" name="gender" value="1" style="width:16px;" class="checkbox-ie"> 男
            </label>
            <label class="radio inline">
                <input type="radio" name="gender" value="2" style="width:16px;" class="checkbox-ie"> 女
            </label>
        </div>
        <div class="control-group">
            <span>年龄:</span>
            <select name="age_min" style="width:50px;">
                <option value=""></option>
                <?php if(is_array($age)) {foreach((array)$age as $val) {?>
                <option value="<?php echo $val;?>"<?php if($_GET['age_min']==$val) { ?> selected<?php } ?>><?php echo $val;?></option>
                <?php }} ?>
            </select>
            <em>~</em>
            <select name="age_max" style="width:50px;">
            	<option value=""></option>
                <?php if(is_array($age)) {foreach((array)$age as $val) {?>
                <option value="<?php echo $val;?>"<?php if($_GET['age_max']==$val) { ?> selected<?php } ?>><?php echo $val;?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="control-group">
            <span>地区:</span>
            <select name="province" style="width:55px;">
                <option value="-1"></option>
                <?php if(is_array($provinces)) {foreach((array)$provinces as $key=>$val) {?>
                <option value="<?php echo $key;?>" <?php if($_GET['province']==$key) { ?>selected<?php } ?>><?php echo $val;?></option>
                <?php }} ?>
            </select>
            <select name="city" id="city" style="width:55px;">
                <option value="-1"></option>
            </select>
        </div>
        <div class="control-group" style="padding-left:30px;">
            <input type="submit" class="btn btn-danger" value="搜索">
        </div>
    </form>  
    </dd>
</dl>
<?php } ?>