<?php
define("ROOMS_GOODS_BUY_CONFIG","---
title: K房商品配置
key: 
  name: 名称
  desc: 备注
  expire: 时限
  site: 线路
  maxuser: 规模
  price: 价格
  lowest: <font color=red>参考价</font>
  home: 首页显示
  status: 状态
list: 
  0: 
    name: 50人弦音房间
    desc: |
      1、可同时50人在房间内K歌以及视频文字交互；<br />
      2、享有1年使用期限；<br />
      3、享受电信+网通双线服务带宽；
    expire: 365
    site: sx
    maxuser: 50
    price: 300
    lowest: 50
    home: 1
    status: 1
  1: 
    name: 100人弦音房间
    desc: |
      1、可同时100人在房间内K歌以及视频文字交互；<br />
      2、享有1年使用期限；<br />
      3、享受电信+网通双线服务带宽；
    expire: 365
    site: sx
    maxuser: 100
    price: 500
    lowest: 100
    home: 1
    status: 1
  2: 
    name: 300人弦音房间
    desc: |
      1、可同时300人在房间内K歌以及视频文字交互；<br />
      2、享有1年使用期限；<br />
      3、享受电信+网通双线服务带宽；
    expire: 365
    site: sx
    maxuser: 300
    price: 1000
    lowest: 300
    home: 1
    status: 1
  3: 
    name: 500人弦音房间
    desc: 500人弦音房间
    expire: 365
    site: sx
    maxuser: 500
    price: 1
    lowest: 500
    home: 0
    status: 1
");
define("ROOMS_GOODS_UPGRADE_CONFIG","---
title: K房升级配置
key: 
  name: 名称
  expire: 时限
  site: 线路
  maxuser: 规模
  price: 价格
  lowest: <font color=red>参考价</font>
  status: 状态
list: 
  0: 
    name: 50人弦音房间
    expire: 1
    site: sx
    maxuser: 50
    price: 0.82
    lowest: 0.14
    status: 1
  1: 
    name: 100人弦音房间
    expire: 1
    site: sx
    maxuser: 100
    price: 1.37
    lowest: 0.27
    status: 1
  2: 
    name: 300人弦音房间
    expire: 1
    site: sx
    maxuser: 300
    price: 2.74
    lowest: 0.82
    status: 1
");
?>