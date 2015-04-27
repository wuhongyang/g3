<?php

class env {
	
	protected $env_file = null;

	public function __construct($file = '') {
		if(empty($file)) {
			$file = dirname(dirname(__FILE__)).'/config/define.php';
		}
		$this->env_file = $file;
	}

	public function __destruct() {
		unset($this->env_file);
	}

	/**
	 * 读取配置文件内容
	 * 
	 * @param string $file 配置文件名
	 * @return json
	 */
	function env_read_config($config_name,$file = '') {
		if(empty($file)) {
			$file = $this->env_file;
		}
		$r_array = array();
		if(file_exists($file) && is_readable($file)) {
			$file_array = $this->read_file($file);
			$bracket_arr = $this->parse_bracket($this->read_file($file,false));
			foreach((array)$file_array as $key => $val) {
				$bracket = (string)$bracket_arr[$bracket_key-1];
				if(substr_count($val,'define(',0) == 1) {
					$array_key = (int)$array_key;
					list(,$tmp_key,$tmp_val,$tmp_var) = $this->parse_define($val);
					$r_array[$bracket][$array_key]['key'] = $tmp_key;
					$r_array[$bracket][$array_key]['val'] = $tmp_val;
					$r_array[$bracket][$array_key]['var'] = $tmp_var;
					$array_key++;
				} else {
					$array_key = 0;
					$bracket_key++;
				}
			}
		} else {
			$r_array[$config_name][] = array('Flag'=>'102','FlagString'=>'fail');
		}
		return (!empty($bracket_arr) && empty($config_name)) ? $r_array : $r_array[$config_name];
	}

	/**
	 * 写入新配置内容
	 * 
	 * @param string $file 配置文件名
	 * @return string
	 */
	function env_write_config($post_arr,$file = '') {
		if(empty($file)) {
			$file = $this->env_file;
		}
		if(file_exists($file) && is_writable($file)) {
			if(is_array($post_arr) && !empty($post_arr)) {
				$string = "<?php\n";
				$file_content = $this->read_file($file,false);
				preg_match_all('/(define\([\'].*[\']\s?,\s?[\']?).*[^\']([\']?\);\/\/.*)/i',$file_content,$matchs);
				$file_array = $this->read_file($file);
				$bracket_arr = $this->parse_bracket($this->read_file($file,false));
				$array_key = 0;
				$bracket_key = 0;
				foreach((array)$file_array as $key => $val) {
					$bracket = $bracket_arr[$bracket_key];
					if(substr_count($val,'define(',0) == 1) {
						list(,$tmp_key,$tmp_val) = $this->parse_define($val);
						if(trim($post_arr[$tmp_key]) != null) {
							$tmp_val = $post_arr[$tmp_key];
						}
						$string .= $matchs[1][$array_key].$tmp_val.$matchs[2][$array_key]."\n";
						$array_key++;
					} else {
						$string .= '/**['.$bracket.']**/'."\n";
						$bracket_key++;
					}
				}
				$string .= "?>";
				if($string != "<?php\n?>") {
					if(file_put_contents($file,$string)) {
						$r_array = array('Flag'=>'100','FlagString'=>'success');
					}
				} else {
					$r_array = array('Flag'=>'103','FlagString'=>'success');
				}
			} else {
				$r_array = array('Flag'=>'101','FlagString'=>'fail');
			}
		} else {
			$r_array = array('Flag'=>'102','FlagString'=>'fail');
		}
		return $r_array;
	}

	/**
	 * 读取配置文件
	 * 
	 * @param string $file 文件名 $is_array 返回类型
	 * @return array或string
	 */
	protected function read_file($file,$is_arr = true) {
		$content = file_get_contents($file);
		$content = trim($content);
		$content = substr($content, 0, 5) == '<?php' ? substr($content, 5) : $content;
		$content = substr($content, -2) == '?>' ? substr($content, 0, -2) : $content;
		if($is_arr == true) {
			$array = explode("\n",$content);
			$array = array_filter($array);
			foreach($array as $key => $val) {
				if(!empty($val) && $val != "\n" && $val != "\r") {
					$result[$key] = $val;
				}
			}
		} else {
			$result = $content;
		}
		return $result;
	}

	/**
	 * 解析define内容
	 * 
	 * @param string $string 字符串
	 * @return string
	 */
	protected function parse_define($string) {
		preg_match('/define\([\'](.*)[\']\s?,\s?[\']?(.*[^\'])[\']?\);\/\/(.*)/i',$string,$matchs);
		if(is_array($matchs) && !empty($matchs)) {
			return $matchs;
		}
	}

	/**
	 * 解析**[]**内容
	 * 
	 * @param string $string 字符串
	 * @return string
	 */
	protected function parse_bracket($string) {
		preg_match_all('/\/\*\*\[(.*)\]\*\*\//i',$string,$matchs);
		if(is_array($matchs) && !empty($matchs)) {
			return $matchs[1];
		}
	}
}