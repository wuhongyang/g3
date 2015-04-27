<?php
class pagecache{

	private static $cache = null;
    private static $instance = false;

    private function __construct(){
    }

    private function __clone(){
    }

    public static function main(){
        if(!self::$instance instanceof self){
            self::$cache = cache::connect(config('cache','memcache'));
            self::$instance = new self();
        }
        return self :: $instance;
    }

	public function start($key = ''){
		if(empty($key)){
			$key = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		return self::$cache->get($key);
	}

	public function end($key = ''){
		if(empty($key)){
			$key = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		$data = ob_get_contents();
		return self::$cache->set($key,$data,10);
	}

	public function long_data(){
		if(empty($key)){
			$key = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		return self::$cache->long_get($key);
	}
}
$data = pagecache::main()->start();
if(!empty($data)){
	exit($data);
}else{
	$data = pagecache::main()->long_data();
}
ob_start();
?>