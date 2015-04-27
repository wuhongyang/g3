<?php
/**
* example:
* 1 2 3 4 5 6 7 8 9 10 ... 28
*/
require_once 'page.class.php';
class extpage extends page
{
    public function __construct($array){
        parent :: page($array);
        $this->first_page = 1;
        $this->last_page = $this->totalpage;
        $this->format_left = '';
        $this->format_right = '';
    }

    public function show(){
        $pagestr = '<div class="pagenavi" id="lopage">有<span class="num">' . $this->total . '</span>条记录';
        $pagestr .= '&nbsp;&nbsp;&nbsp;共<span class="num">' . $this->totalpage . '</span>页';
        if ($this->totalpage > 1) { //只有大1页时才显示分页按钮
            $pagestr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $pagestr .= $this->first_page() . ' ';
            $pagestr .= $this->nowbar('', 'curr');
            $pagestr .= $this->last_page();
        }
        $pagestr .= '</div>';
        return $pagestr;
    }
    public function show1(){
        $pagestr = "<div class = 'page'>";
        if ($this->totalpage > 1) { //只有大1页时才显示分页按钮
            $this->pre_page = "上一页";
            $this->next_page = "下一页";
            $this->pagebarnum = 5;
            $pagestr .= $this->pre_page('curr');
            $pagestr .= $this->first_page('curr');
            $pagestr .= $this->nowbar('', 'curr');
            $pagestr .= $this->last_page('curr');
            $pagestr .= $this->next_page('curr');
        }
        $pagestr .= "</div>";
        return $pagestr;
    }
    public function simple_page($nowpagecount){
        if($this->nowindex < 1){
            $this->nowindex = 1;
        }
        $prev = $this->nowindex <= 1 ? 1 : $this->nowindex - 1;
        $next = $nowpagecount < $this->perpage ? $this->nowindex+1 : $this->nowindex + 1;
        $pagestr = '当前第<span class="num">'.$this->nowindex.'</span>页&nbsp;&nbsp;';
        $pagestr .= '<a href="'.$this->url.$prev.'">上一页</a>&nbsp;&nbsp;';
        $pagestr .= '<a href="'.$this->url.$next.'">下一页</a>';
        return $pagestr;
    }
    public function simple_limit(){
        return ($this->nowindex - 1) * $this->perpage.','.$this->perpage;
    }
    public function limit(){
        $num = ($this->nowindex - 1) * $this->perpage; //计算当前取值起点
        if ($num <= 0 OR $this->nowindex > $this->total) {
            //若用户自己更改页码小于等于零时,则仍视为第一页，如第一页参数1被修改为0或-1时
            //或者，用户在手工更改页码大于总页码时也仍为第一页，如总共3页，用户修改到了30页
            $num = 0;
        }
        $actual = $this->total - $num; // 实际条数
        if($actual < $this->perpage) {
            $differ = $this->perpage - $actual; // 相差条数
            $this->perpage -= $differ;
        }
        $limitSql = $num . ',' . $this->perpage;
        return $limitSql;
    }
}

/*
.pagenavi 
{
text-align:center;  
font: 10px Arial, tahoma, sans-serif; 
padding-top: 5px; 
padding-bottom: 5px; 
margin: 0px;
}

.pagenavi a 
{
border: 1px solid #E2F1AF; 
background: #FFFFFF; 
text-decoration: none; 
color:#C16012; 
display:inline-block; 
padding-left:6px; 
padding-right:6px; 
padding-top:2px; 
padding-bottom:2px
}

.pagenavi a:visited
{
border: 1px solid #E2F1AF; 
background: #FFFFFF; 
text-decoration: none; 
padding-left:6px; 
padding-right:6px; 
padding-top:2px; 
padding-bottom:2px
}

.pagenavi .break
{
border: medium none;  
text-decoration: none; 
color:#C16012; background:;; 
padding-left:6px; 
padding-right:6px; 
padding-top:2px; 
padding-bottom:2px
}

.pagenavi .num 
{
color:#C16012; 
font: 10px Arial, tahoma, sans-serif; 
padding-left:3px; 
padding-right:3px; 
padding-top:0; 
padding-bottom:0
}

.pagenavi .curr 
{
padding: 2px 6px; 
border-color: #999; 
font-weight: bold; 
font: 10px Arial, tahoma, sans-serif; 
background:transparent;
}

.pagenavi a:hover 
{
color: #C16012; 
background: #E2F1AF; 
text-decoration: none
}
*/
