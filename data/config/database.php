<?php
$config = array (
    'default' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kexoo_im',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
    'kmoney' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kkyoo_kmoney',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
    'kwealth' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kkyoo_kwealth',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
    'im' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kexoo_im',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
    'kkyoo_new_rooms' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kkyoo_new_rooms',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
    'voucher' => array(
        'dbhost'	=> $_SERVER['kkyooDB_HOST'],
        'dbuser'	=> $_SERVER['kkyooDB_NAME'],
        'dbpw'		=> $_SERVER['kkyooDB_PASS'],
        'dbport'	=> $_SERVER['kkyooDB_PORT'],
        'dbname'	=> 'kkyoo_voucher',
        'dbcharset' => 'utf8',
        'pconnect'	=> 0,
        'debug'		=> false,
        'tablepre'	=> '',
        'time'		=> ''
    ),
);

define('DB_NAME_IM', 'kexoo_im');
define('DB_NAME_ADMIN', 'kkyoo_admin');
define('DB_NAME_COMMON','kkyoo_common');
define('DB_NAME_KMONEY', 'kkyoo_kmoney');
define('DB_NAME_KWEALTH','kkyoo_kwealth');
define('DB_NAME_KWEALTH_PLAT','kkyoo_kwealth_plat');
define('DB_NAME_KINCOME','kkyoo_income');
define('DB_NAME_KINCOME_PLAT','kkyoo_income_plat');
define('DB_NAME_VOUCHER','kkyoo_voucher');
define('DB_NAME_VOUCHER_PLAT', 'kkyoo_voucher_plat');
define('DB_NAME_TAX','kkyoo_tax');
define('DB_NAME_CCS','kkyoo_ccs');
define('DB_NAME_NEW_ROOMS','kkyoo_new_rooms');
define('DB_NAME_REGION','g3_regions');
define('DB_NAME_PROPS','kkyoo_props');
define('DB_NAME_GROUP', 'g3_groups');
define('DB_NAME_SYSTEM_CONFIG','g3_system_config');
define('DB_NAME_PARTNER','g3_partner');
define('DB_NAME_WEIBO', 'kkyoo_weibo');
define('DB_NAME_BEHAVIOR','kkyoo_behavior');
define('DB_NAME_INTEGRAL','kkyoo_integral');
define('DB_NAME_PERMISSION','g3_permission');
define('DB_NAME_FLASH_GAME','kkyoo_flash_games');
define('DB_NAME_HELP','g3_help');
define('DB_NAME_MARGIN','kkyoo_margin');
define('DB_NAME_SHOP', 'g3_shop');
define('DB_NAME_ISSUE', 'aodian_issue_tracking');
define('DB_NAME_TPL', 'g3_template');
