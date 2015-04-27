<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(CLASS_PATH."ErrorCase.class.php");
//require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/data/library/global.fun.php';
class Recorder{
    private static $data;
    private $inc;
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();

        //-------读取配置文件
        $incFileContents = file_get_contents(ROOT."comm/inc.php");
        $this->inc = json_decode($incFileContents);
        if(empty($this->inc)){
            $this->error->showError("20001");
        }

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
		$GroupData = domain::main()->GroupData();
		$ext = json_decode($GroupData['EXT'], true);
    	if($GroupData['groupid'] > 0 && !empty($ext[$name])){
			if($name == 'callback'){
				return 'http://'.$ext[$name]['value'].'/passport/openlogin/qq/callback.php';
			}
			return $ext[$name]['value'];
    	}else{
    		if(empty($this->inc->$name)){
    			return null;
    		}else{
    			return $this->inc->$name;
    		}
    	}
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}
