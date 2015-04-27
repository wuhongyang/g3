<?php

class PassportFactory{

	private static $types = array(1 => 'email', 2 => 'username', 3 => 'phone', 4 => 'qq');

	public static function getInstance($type){
		if(!in_array($type,array_keys(self::$types))){
			require_once 'passport.class.php';
			return new Passport();
		}
		$file = self::$types[$type].'_passport.class.php';
		require_once $file;
		$class = ucfirst(self::$types[$type]).'Passport';
		return new $class;
	}
}