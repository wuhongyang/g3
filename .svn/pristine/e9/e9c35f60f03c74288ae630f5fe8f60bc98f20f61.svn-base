<?php

class _redis extends _cache {
	
	protected $options = array ();
	
	protected $server = null;
	
	private static $instance = false;
	
	private function __construct() {
	}
	
	private function __clone() {
	}
	
	public static function get_instance() {
		if(!self :: $instance instanceof self) {
			self :: $instance = new self();
		}
		return self :: $instance;
	}
	
	public function set_options(array $options) {
		if($options && is_array($options)) {
			foreach ((array)$options as $name => $value) {
				$this->options[$name] = $value;
			}
		} else {
			$this->options = array(
				'time' => '0',
				'prefix' => 'test',
				'master' => array(
					'host' => 'localhost',
					'port' => 6379
				),
				'slave' => array(
					array(
						'host' => '',
						'port' => ''
					)
				)
			);
		}
		$this->connect();
	}
	
	protected function connect() {
		$this->prefix = $this->options['prefix'] ? rtrim($this->options['prefix'], '/') . '/' : '';
		$this->server = new Redis();
		if(!empty($this->options['master'])) {
			$this->server->connect($this->options['master']['host'], $this->options['master']['port']);
		}
	}
	
	public function set($key, $content, $ttl = '') {
		if($ttl === '') {
			$expire = $this->options['time'];
		} else {
			$expire = $ttl;
		}
		$data = $content;
		if($expire == 0) {
			return $this->server->set($this->prefix . $key, $data);
		} else {
			return $this->server->setex($this->prefix . $key, $data, $expire);
		}
	}
	
	public function get($key) {
		return $this->server->get($this->prefix . $key);
	}
	
	public function rpush($key, $content) {
		return $this->server->rpush($this->prefix . $key, $content);
	}
	
	public function lpush($key, $content) {
		return $this->server->lpush($this->prefix . $key, $content);
	}
	
	public function rpop($key) {
		return $this->server->rpop($this->prefix . $key);
	}
	
	public function lpop($key) {
		return $this->server->lpop($this->prefix . $key);
	}
	
	public function lrange($key, $start, $end) {
		return $this->server->lrange($this->perfix . $key, $start, $end);
	}
	
	public function ltrim($key, $start, $end) {
		return $this->server->ltrim($this->perfix . $key, $start, $end);
	}
	
	public function llen($key) {
		return $this->server->llen($this->perfix . $key);
	}
	
	public function delete($keys) {
		foreach ($keys as $key) {
			return $this->server->delete($this->prefix . $key);
		}
	}
	
	public function flush() {
		return $this->server->flushDB();
	}
	
	protected function expire($time,$expire) {
		return ($expire > 0 && (time() - $time) < $expire) ? true : false;
	}
}
