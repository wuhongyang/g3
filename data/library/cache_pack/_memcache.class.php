<?php

class _memcache extends _cache {
	
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
				'time' => 1800,
				'prefix' => '',
				'servers' => array(
					array (
						'host'  => 'localhost',
						'port'  => 11211,
						'persistent' => false,
						'weight'     => 1,
						'timeout'    => 1,
						'retryInterval' => 15,
						'status'     => true,
						'callback'   => null
					)
				)
			);
		}
		$this->connect();
	}
	
	protected function connect() {
		$this->prefix = $this->options['prefix'] ? rtrim($this->options['prefix'], '/') . '/' : '';
		$this->server = new Memcache();
		if(!empty($this->options['servers'])) {
			foreach ($this->options['servers'] as $key => $server) {
				if(is_array($server) && !empty($server)) {
					$this->server->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retryInterval'], $server['status'], $server['callback']);
				}
			}
			$this->server->setCompressThreshold(2048, 0.2);
		}
	}
	
	public function set($key, $content, $ttl = '') {
		if($ttl === '') {
			$expire = $this->options['time'];
		} else {
			$expire = $ttl;
		}
		$data = array('time' => time(), 'expire' => (int)$expire, 'data' => $content);
		return $this->server->set($this->prefix.$key, $data, false, 0);
	}
	
	public function get($key) {
		$data = $this->server->get($this->prefix . $key);
		if($this->expire($data['time'],$data['expire'])) {
			return $data['data'];
		}
		return null;
	}
	
	public function long_get($key) {
		$data = $this->server->get($this->prefix . $key);
		return $data['data'];
	}
	
	public function delete($keys) {
		foreach ($keys as $key) {
			return $this->server->delete($this->prefix . $key);
		}
	}
	
	public function flush() {
		return $this->server->flush();
	}
	
	protected function expire($time,$expire) {
		return ($expire > 0 && (time() - $time) < $expire) ? true : false;
	}
}
