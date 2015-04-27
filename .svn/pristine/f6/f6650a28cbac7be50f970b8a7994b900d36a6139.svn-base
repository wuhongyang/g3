<?php
include '../library/global.fun.php';
$captcha = new captcha();
$captcha->setImSize(96,34);
$captcha->create();
//$captcha->drawNoise();
$captcha->drawCurve();
session_start();
$_SESSION['captcha'] = strtolower($captcha->getCodeChar(true));
$captcha->display();