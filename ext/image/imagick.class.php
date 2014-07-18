<?php
/*
*imagick 图片处理
*@author idsky<idsky360@163.com>
*/
class mygoExtImageImagick{

	private $image;
	private $info;

	public function __construnct($image){
		$image && $this->open($image);
	}

	public function open($imageName){
        //检测图像文件
        if(!is_file($imageName)) E('不存在的图像文件');
		
		//销毁已存在的图像
        empty($this->image) || $this->image->destroy();

        //载入图像
        $this->image = new \Imagick(realpath($imageName));

        //设置图像信息
        $this->info = array(
            'width'  => $this->image->getImageWidth(),
            'height' => $this->image->getImageHeight(),
            'type'   => strtolower($this->image->getImageFormat()),
            'mime'   => $this->image->getImageMimeType(),
        );
    }


    


}
?>