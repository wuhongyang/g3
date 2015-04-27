<?php
class db {
	
	private function __construct() {}
	
	private function __clone() {}
	
	private static $instance = false;
	
	public static function connect($options = array(), $engine = '') {
		if (extension_loaded('mysqli') && (empty($engine) || $engine == 'mysql')) {
			include_once dirname(__FILE__).'/db_pack/_mysqli.class.php';
			if (!(self :: $instance === _mysqli::get_instance() && self :: $instance->options == $options)) {
				self :: $instance = _mysqli::get_instance('false');
				self :: $instance->set_options($options);
			}
		}
		elseif (extension_loaded('pdo_mysql') && (empty($engine) || $engine == 'pdo_mysql')) {
			include_once dirname(__FILE__).'/db_pack/_pdo_mysql.class.php';
			if (!(self :: $instance === _pdo_mysql::get_instance() && self :: $instance->options == $options)) {
				self :: $instance = _pdo_mysql::get_instance('false');
				self :: $instance->set_options($options);
			}
		}
		elseif (extension_loaded('mongo') && (empty($engine) || $engine == 'mongo')) {
			include_once dirname(__FILE__).'/db_pack/_mongo.class.php';
			if (!(self :: $instance === _mongo::get_instance() && self :: $instance->options == $options)) {
				self :: $instance = _mongo::get_instance('false');
				self :: $instance->set_options($options);
			}
		}
		return self :: $instance;
	}
}
