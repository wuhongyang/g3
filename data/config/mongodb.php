<?php
$config = array(
    'ktv' => array (
        'dbhost'	=> $_SERVER['mongoDB_HOST'],
        'dbuser'	=> $_SERVER['mongoDB_NAME'],
        'dbpw'		=> $_SERVER['mongoDB_PASS'],
        'dbport'	=> $_SERVER['mongoDB_PORT'],
        'dbname'	=> 'ktv',
        'dbcharset' => 'utf8',
        'debug'		=> true,
    ),
    'pic' => array(
        'dbhost'	=> $_SERVER['mongoDB_HOST'],
        'dbuser'	=> $_SERVER['mongoDB_NAME'],
        'dbpw'		=> $_SERVER['mongoDB_PASS'],
        'dbport'	=> $_SERVER['mongoDB_PORT'],
        'dbname'	=> 'kkyoo_images',
        'dbcharset' => 'utf8',
        'debug'		=> true,
    ),
	'channel' => array (
			'dbhost'	=> $_SERVER['mongoDB_HOST'],
			'dbuser'	=> $_SERVER['mongoDB_NAME'],
			'dbpw'		=> $_SERVER['mongoDB_PASS'],
			'dbport'	=> $_SERVER['mongoDB_PORT'],
			'dbname'	=> 'parter_income',
			'dbcharset' => 'utf8',
			'debug'		=> true,
	),
);
