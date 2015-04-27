<?php
include_once dirname(__FILE__).'/cache_pack/_cache.class.php';

class cache {
	
	private function __construct() {}
	
	private function __clone() {}
	
	private static $instance = false;
	
	public static function connect($options = array(), $engine = 'memcache') {
		if ($engine == 'serialize_file') {
			include_once dirname(__FILE__).'/cache_pack/_serialize_file.class.php';
			if (!(self :: $instance === _serialize_file::get_instance())) {
				self :: $instance = _serialize_file::get_instance();
				self :: $instance->set_options($options);
			}
		}
		elseif ($engine == 'json_file') {
			include_once dirname(__FILE__).'/cache_pack/_json_file.class.php';
			if (!(self :: $instance === _json_file::get_instance())) {
				self :: $instance = _json_file::get_instance();
				self :: $instance->set_options($options);
			}
		}
		elseif ($engine == 'memcache') {
			include_once dirname(__FILE__).'/cache_pack/_memcache.class.php';
			if (!(self :: $instance === _memcache::get_instance())) {
				self :: $instance = _memcache::get_instance();
				self :: $instance->set_options($options);
			}
		}
		elseif ($engine == 'redis') {
			include_once dirname(__FILE__).'/cache_pack/_redis.class.php';
			if (!(self :: $instance === _redis::get_instance())) {
				self :: $instance = _redis::get_instance();
				self :: $instance->set_options($options);
			}
		}
		elseif ($engine == 'ssdb') {
			include_once dirname(__FILE__).'/cache_pack/_ssdb.class.php';
			if (!(self :: $instance === _ssdb::get_instance())) {
				self :: $instance = _ssdb::get_instance();
				self :: $instance->set_options($options);
			}
		}
		elseif ($engine == 'apc') {
			include_once dirname(__FILE__).'/cache_pack/_apc.class.php';
			if (!(self :: $instance === _apc::get_instance())) {
				self :: $instance = _apc::get_instance();
				self :: $instance->set_options($options);
			}
		}
		return self :: $instance;
	}
}
