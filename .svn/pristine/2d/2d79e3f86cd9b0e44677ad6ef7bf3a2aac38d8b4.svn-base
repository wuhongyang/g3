<?php
include '../library/global.fun.php';
$captcha = new captcha();
$captcha->setImSize(78,28);
$captcha->create();
//$captcha->drawNoise();
$captcha->drawCurve();
session_start();
$_SESSION['captcha'] = strtolower($captcha->getCodeChar(true));
$captcha->display();