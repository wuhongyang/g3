<?php
/**
*--------------------------------------------------------------
* 对图像进行裁切和缩放
*--------------------------------------------------------------
* 最后修改时间 2012-1-8 Leon
* @author Leon(tmkook@gmail.com)
* @date 2011-2-27
* @copyright GreenPHP
* @version $Id$
*--------------------------------------------------------------
* $tb = new thumb(file_get_contents('test.jpg'));
* $tb->zoom(100,0)->display(); //显示图像
* $tb->zoom(100,0)->save('path/filename'); //保存图像，无后缀名
*--------------------------------------------------------------
*/
class thumb
{
    protected $im;
    protected $im_type;
    protected $is_animat;
    
    /**
    * 构造函数
    * 
    * @param $im 读取的文件数据
    */
    public function __construct($im){
        $this->is_animat = preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$im); //是否动画
        $this->im_type   = $this->getType($im); //图像类型
        $this->im = ($this->im_type=='flash' || $this->is_animat || $this->im_type=='bmp')? $im : imagecreatefromstring($im); //创建图像
    }

    /**
	* 裁切图片
	* 1.设置了宽和高的尺寸时图片会缩放到设定的尺寸
	* 2.宽或高只设置一项则另一项按比例缩放
	* 3.裁切将从设置的坐标位置开始默认为左上角
	*
	* @param $w 图片宽
	* @param $h 图片高
	* @param $x x坐标
	* @param $y y坐标
	*/
    public function crop($w,$h,$x=0,$y=0){
        if($w <= 0 || $h <= 0) throw new Exception("尺寸不能小于或等于 0");
        if($this->is_animat && class_exists('Gmagick')){
            $gmagick = new Gmagick();
            $gmagick->readimageblob($this->im);
            $gmagick = $gmagick->coalesceImages();
            $gmagick->cropimage($w, $h, $x, $y);
            /*while($gmagick->hasNextImage()){
                $gmagick->nextImage();
                $gmagick->cropimage($w, $h, $x, $y);
            }*/
            $this->im = $gmagick->getimageblob();
            $gmagick->destroy();
        }elseif($this->im_type != 'flash' && !$this->is_animat && $this->im_type != 'bmp'){
            $im_w = imagesx($this->im);
            $im_h = imagesy($this->im);
            if($w > $im_w || $h > $im_h){
                throw new Exception("裁切尺寸不能大于原始图");
            }
            $dst_im = imagecreatetruecolor($w,$h);
            imagealphablending($dst_im,false);
            imagesavealpha($dst_im,true);
            $white = imagecolorallocatealpha($dst_im,255,255,255,127);
            imagefill($dst_im,0,0,$white);
            imagecopyresampled($dst_im,$this->im,0,0,$x,$y,$w,$h,$w,$h);
            $this->im = $dst_im;
        }
        return $this;
    }

    public function middleCrop($w,$h){
        $im_w = imagesx($this->im);
        $im_h = imagesy($this->im);
        $x = ($im_w - $w) / 2;
        $y = ($im_h - $h) / 2;
        return $this->crop($w,$h,$x,$y);
    }
    
    /**
	* 缩放图片
	* 1.设置了宽和高的尺寸时图片会缩放到设定的尺寸
	* 2.宽或高只设置一项则另一项按比例缩放
	*
	* @param $w 图片宽
	* @param $h 图片高
	*/
    public function zoom($w=0,$h=0){
        if($this->is_animat && class_exists('Gmagick')){
            $gmagick = new Gmagick();
            $gmagick->readimageblob($this->im);
            $gmagick = $gmagick->coalesceImages();
            if($w <= 0 && $h <= 0){
                $w = $gmagick->getImageWidth();
                $h = $gmagick->getImageHeight();
            }
            do {
                $gmagick->resizeImage($w, $h, Gmagick::FILTER_UNDEFINED, true);
            }while($gmagick->nextImage());
            $this->im = $gmagick->getimageblob();
            $gmagick->destroy();
        }elseif($this->im_type != 'flash' && !$this->is_animat && $this->im_type != 'bmp'){
            $im_w = imagesx($this->im);
            $im_h = imagesy($this->im);
            extract($this->getSmartZoomSize($w,$h,$im_w,$im_h));
            $dst_im = imagecreatetruecolor($canvas_w,$canvas_h);
            imagealphablending($dst_im,false);
            imagesavealpha($dst_im,true);
            $white = imagecolorallocatealpha($dst_im,255,255,255,127);
            imagefill($dst_im,0,0,$white);
            imagecopyresampled($dst_im,$this->im,$x,$y,0,0,$new_w,$new_h,$im_w,$im_h);
            $this->im = $dst_im;
        }
        return $this;
    }
    
    /**
	* 显示图片
	*/
    public function display(){
		if($this->im_type == 'flash'){
			header('Content-type:application/x-shockwave-flash');
			exit($this->im);
		}elseif($this->is_animat && $this->im_type == 'gif'){
			header("Content-type:image/gif");
			exit($this->im);
		}elseif($this->im_type == 'bmp'){
			header('Content-type:image/bmp');
			exit($this->im);
		}
        header("Content-type:image/{$this->im_type}");
		$func = "image{$this->im_type}";
		if($this->im_type=='jpeg'){
			$func($this->im,'',100);//jpeg图像质量
		}else{
			$func($this->im);
		}
    }
    
    /**
	* 保存图片
	*
	* @param $path 保存路径
	* @return boolen
	*/
    public function save($path){
		if($this->im_type=='flash' || $this->is_animat || $this->im_type=='bmp'){
			return file_put_contents($path,$this->im);
		}
		$func = "image{$this->im_type}";
        return $func($this->im,$path);
    }

    //获取图像类型
	protected function getType($im){
		$bin = substr($im,0,2);
		$str_info  = @unpack("C2chars", $bin);
		$type_code = intval($str_info['chars1'].$str_info['chars2']);
		switch($type_code){
			case 255216:
				$file_type = 'jpeg';
				break;
			case 7173:
				$file_type = 'gif';
				break;
			case 6677:
				$file_type = 'bmp';
				break;
			case 13780:
				$file_type = 'png';
				break;
			case 6787:
				$file_type = 'flash';
				break;
			case 7087:
				$file_type = 'flash';
				break;
			default:
				$file_type = $type_code;
				break;
		}
		return $file_type;
	}
    
    //按比例缩放画布尺寸
    public function getSmartZoomSize($w,$h,$im_w,$im_h){
        $x = $y = 0;
        if(empty($w) && $h > 0){ //自动定宽
            if($im_h > $h){
                $new_w = $h / $im_h * $im_w;
                $new_h = $h;
            }else{
                $new_w = $im_w;
                $new_h = $im_h;
            }
            $canvas_w = $new_w;
            $canvas_h = $new_h;
        }elseif(empty($h) && $w > 0){ //自动定高
            if($im_w > $w){
                $new_w = $w;
                $new_h = $w / $im_w * $im_h;
            }else{
                $new_w = $im_w;
                $new_h = $im_h;
            }
            $canvas_w = $new_w;
            $canvas_h = $new_h;
        }elseif($w > 0 && $h > 0){ //固定宽高
            if($im_w > $im_h || $w < $h){
                $new_h = intval(($w / $im_w) * $im_h);
                $new_w = $w;
            }else{
                $new_h = $h;
                $new_w = intval(($h / $im_h) * $im_w);
            }
            $x = intval(($w - $new_w) / 2); //画布x间距
            $y = intval(($h - $new_h) / 2); //画布y间距
            $canvas_w = $w;
            $canvas_h = $h;
        }else{ //无缩放
            $canvas_w = $new_w = $im_w;
            $canvas_h = $new_h = $im_h;
        }
        return array('canvas_w'=>$canvas_w,'canvas_h'=>$canvas_h,'new_w'=>$new_w,'new_h'=>$new_h,'x'=>$x,'y'=>$y);
    }

}



