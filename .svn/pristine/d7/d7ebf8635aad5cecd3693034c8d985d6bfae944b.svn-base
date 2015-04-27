<?php

class _apc extends _cache {
	
	protected $options = array ();
	
	private static $instance = false;
	
	private function __construct() {
	}
	
	private function __clone() {
	}
	
	public static function get_instance() {
		if (!self :: $instance instanceof self) {
			self :: $instance = new self();
		}
		return self :: $instance;
	}
	
	public function set_options(array $options) {
		if ($options && is_array($options)) {
			foreach ($options as $name => $value) {
				$this->options[$name] = $value;
			}
		} else {
			$this->options = array (
				'path' => './cache/',
				'prefix' => 'test',
				'time' => '3600',
				'servers' => array() // for memcached
			);
		}
		$this->connect();
	}
	
	protected function connect() {
		$this->prefix = rtrim($this->options['prefix'], '/') . '/';
	}
	
	public function set($key, $content, $ttl = '') {
		if($ttl === '') {
			$expire = $this->options['time'];
		} else {
			$expire = $ttl;
		}
		$data = array('time' => time(), 'expire' => (int)$expire, 'data' => $content);
		apc_store($this->prefix.$key, $data, 0);
	}
	
	public function get($key) {
		$data = apc_fetch($this->prefix.$key);
		if ($this->expire($data['time'],$data['expire'])) {
			return null;
		}
		return $data['data'];
	}
	
	public function long_get($key) {
		$data = apc_fetch($this->prefix.$key);
		return $data['data'];
	}
	
	public function delete($keys) {
		foreach ($keys as $key) {
			apc_delete($this->prefix.$key);
		}
	}
	
	public function flush() {
		apc_clear_cache();
	}
	
	protected function expire($time,$expire) {
		return ($expire > 0 && (time() - $time) > $expire) ? true : false;
	}
}
