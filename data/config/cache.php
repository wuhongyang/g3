<?php
//memcache配置
$config = array(
	'memcache' => array(
		'time'		=> 1800,
		'prefix'	=> '',
		'servers'	=> array(
			array(
				'host'  => $_SERVER["memcache_HOST"],
				'port'  => 11211,
				'persistent' => false,
				'weight'     => 1,
				'timeout'    => 1,
				'retryInterval' => 15,
				'status'     => true,
				'callback'   => null
			)
		)
	),
	'redis' => array(
		'time'		=> 0,
		'prefix'	=> '',
		'master'	=> array (
			'host'  => $_SERVER["redisDB_HOST"],
			'port'  => $_SERVER["redisDB_PORT"]
		)
	)
);
