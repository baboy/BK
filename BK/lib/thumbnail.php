<?php
class Thumbnail {
 
        private $maxWidth;
        private $maxHeight;
        private $scale;
        private $inflate;
        private $types;
        private $imgLoaders;
        private $imgCreators;
        private $source;
        private $sourceWidth;
        private $sourceHeight;
        private $sourceMime;
        private $thumb;
        private $thumbWidth;
        private $thumbHeight;
        public $last_error = null;
 
        public function __construct($maxWidth, $maxHeight, $scale = true, $cut = true, $inflate = true) {
            $this->maxWidth = $maxWidth;
            $this->maxHeight = $maxHeight;

            $this->thumbWidth = $maxWidth;
            $this->thumbHeight = $maxHeight;
            $this->scale = $scale;
            $this->cut = $cut;
            $this->inflate = $inflate;
            $this->types = array(
                'image/jpeg',
                'image/png',
                'image/gif'
            );
            //加载MIME类型图像的函数名称
            $this->imgLoaders = array(
                'image/jpeg'        =>      'imagecreatefromjpeg',
                'image/png'         =>      'imagecreatefrompng',
                'image/gif'         =>      'imagecreatefromgif'
            );
            //储存创建MIME类型图片的函数名称
            $this->imgCreators = array(
                'image/jpeg'        =>      'imagejpeg',
                'image/png'         =>      'imagepng',
                'image/gif'         =>      'imagegif'
            );           
        }
        /**
         * 文件方式加载图片
         * @param       string  $image 源图片
         * @return      bool    
         */
        public function setImageSourcePath($image){
            if(!$dims = @getimagesize($image)){
                $this->last_error = "源图片不存在";
                return FALSE;
            }
            if(in_array($dims['mime'], $this->types)){
                $loader = $this->imgLoaders[$dims['mime']];
                $this->source = $loader($image);
                $this->sourceWidth = $dims[0];
                $this->sourceHeight = $dims[1];
                $this->sourceMime = $dims['mime'];
                $this->setup();
                return TRUE;
            }

            $this->last_error = '不支持'.$dims['mime']."图片类型";
            return FALSE;
        }

        public function loadFile($image){
            $this->setImageSourcePath($image);
        }
        /**
         * 字符串方式加载图片
         * @param       string $image  字符串
         * @param       string $mime    图片类型
         * @return type 
         */
        public function loadData($image,$mime){
            if(in_array($mime, $this->types)){
                if($this->source = @imagecreatefromstring($image)){
                    $this->sourceWidth = imagesx($this->source);
                    $this->sourceHeight = imagesy($this->source);
                    $this->sourceMime = $mime;
                    $this->setup();
                    return TRUE;
                }
                $this->last_error =   "不能从字符串加载图片";
                return FALSE;
            }
            $this->last_error = "不支持".$mime."图片格式";
            return FALSE;
        }
        /**
         * 生成缩略图
         * @param       string  $file   文件名。如果不为空则储存为文件，否则直接输出到浏览器
         */
        public function create($file = null){
            if(!$this->sourceMime){
                return FALSE;
            }
            $creator = $this->imgCreators[$this->sourceMime];
            if(isset($file)){
                    return $creator($this->thumb,$file);
            }else{
                    return $creator($this->thumb);
            }
        }
        public function buildThumb($file = null){
            return $this->create($file);
        }
        /**
         * 处理缩放
         */
        public function setup(){
            $cx = 0; $cy = 0; $cw = $this->sourceWidth; $ch = $this->sourceHeight;
            if($this->cut){
                $ori_wh_scale = $this->sourceWidth / $this->sourceHeight;
                $cut_wh_scale = $this->maxWidth / $this->maxHeight;
                if ( $ori_wh_scale < $cut_wh_scale ) {
                    $ch = $cw / $cut_wh_scale;
                    $cy = ($this->sourceHeight - $ch)/2;
                }else{
                    $cw =  $ch * $cut_wh_scale;
                    $cx = ($this->sourceWidth - $cw)/2;
                }
            }
            if(!$this->scale && !$this->scale){
                if($this->sourceWidth > $this->sourceHeight){
                        $this->thumbWidth = $this->maxWidth;
                        $this->thumbHeight = floor($this->sourceHeight*($this->maxWidth/$this->sourceWidth));
                }elseif($this->sourceWidth < $this->sourceHeight){
                        $this->thumbHeight = $this->maxHeight;
                        $this->thumbWidth = floor($this->sourceWidth*($this->maxHeight/$this->sourceHeight));
                }else{
                        $this->thumbWidth = $this->maxWidth;
                        $this->thumbHeight = $this->maxHeight;
                }
            }
            //echo intval($cx).",". intval($cy).",".intval($cw).",". intval($ch);
            //echo $this->thumbWidth.",".$this->thumbHeight;
            //return;

            $this->thumb = imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);
            if ($this->sourceMime == "image/png" || $this->sourceMime == "image/gif" ){
                imagesavealpha($this->source, true);
                imagealphablending($this->thumb, false);
                imagesavealpha($this->thumb, true);   
            }
             
            if($this->sourceWidth <= $this->maxWidth && $this->sourceHeight <= $this->maxHeight && $this->inflate == FALSE){
                $this->thumb = $this->source;
            }else{
                imagecopyresampled($this->thumb, $this->source, 
                    0, 0, intval($cx), intval($cy), 
                    $this->thumbWidth, $this->thumbHeight,
                    $cw, $ch);
            }
        }
         
        public function getMine(){
            return $this->sourceMime;
        }
         
        public function getThumbWidth(){
            return $this->thumbWidth;
        }
         
        public function getThumbHeight(){
            return $this->thumbHeight;
        } 
}
function createThumbnail($src, $dest, $width, $height){
    $thumbnail = new Thumbnail($width, $height);
    $ok = $thumbnail->setImageSourcePath($src);
    if(!$ok)
        return FALSE;
    return $thumbnail->create($dest);
}
?>