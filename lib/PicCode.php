<?php
//��Ҫgd���freetype��
class PicCode{
	 //������֤��ͼƬ�߶�
	 private $height;
	 //������֤��ͼƬ���
	 private $width;
	 //������֤���ַ�����
	 private $textNum;
	 //������֤���ַ�����
	 private $textContent;
	 //�����ַ���ɫ
	 private $fontColor;
	 //�����������������ɫ
	 private $randFontColor;
	 //���������С
	 private $fontSize;
	 //��������
	 private $fontFamily;
	 //���屳����ɫ
	 private $bgColor;
	 //����������ı�����ɫ
	 private $randBgColor;
	 //�����ַ�����
	 private $textLang;
	 //������ŵ�����
	 private $noisePoint;
	 //�������������
	 private $noiseLine;
	 //�����Ƿ�Ť��
	 private $distortion;
	 //����Ť��ͼƬԴ
	 private $distortionImage;
	 //�����Ƿ��б߿�
	 private $showBorder;
	 //������֤��ͼƬԴ
	 private $image;
 
	 //Constructor ���캯��
	 public function PicCode(){
		 $this->textNum=4;
		 $this->fontSize=12;
		 $this->fontFamily=PHPLIB_ROOT.'font/tahoma.ttf';//�����������壬���Ըĳ�linux��Ŀ¼
		 $this->textLang='en';
		 $this->noisePoint=30;
		 $this->noiseLine=3;
		 $this->distortion=false;
		 $this->showBorder=false;
	 }

	 //����ͼƬ���
	 public function setWidth($w){
	 	$this->width=$w;
	 }
	 
	 //����ͼƬ�߶�
	 public function setHeight($h){
	 	$this->height=$h;
	 }
	 
	 //�����ַ�����
	 public function setTextNumber($textN){
	 	$this->textNum=$textN;
	 }
	 
	 //�����ַ���ɫ
	 public function setFontColor($fc){
	 	$this->fontColor=sscanf($fc,'#%2x%2x%2x');
	 }
	 
	 //�����ֺ�
	 public function setFontSize($n){
	 	$this->fontSize=$n;
	 }
	 
	 //��������
	 public function setFontFamily($ffUrl){
	 	$this->fontFamily=$ffUrl;
	 }
	 
	 //�����ַ�����
	 public function setTextLang($lang){
	 	$this->textLang=$lang;
	 }
	 
	 //����ͼƬ����
	 public function setBgColor($bc){
	 	$this->bgColor=sscanf($bc,'#%2e%2e%2e');
	 }
	 
	 //���ø��ŵ�����
	 public function setNoisePoint($n){
		 $this->noisePoint=$n;
	 }
	 
	 //���ø���������
	 public function setNoiseLine($n){
	 	$this->noiseLine=$n;
	 }
	 
	 //�����Ƿ�Ť��
	 public function setDistortion($b){
	 	$this->distortion=$b;
	 }
	 
	 //�����Ƿ���ʾ�߿�
	 public function setShowBorder($border){
		 $this->showBorder=$border;
	 }
 
	 //��ʼ����֤��ͼƬ
	 public function initImage(){
		 if(empty($this->width)){
		 	$this->width=floor($this->fontSize*1.3)*$this->textNum+10;
		 }
		 if(empty($this->height)){
		 	$this->height=$this->fontSize*2;
		 }
		 $this->image=imagecreatetruecolor($this->width,$this->height);
		 if(empty($this->bgColor)){
		 	$this->randBgColor=imagecolorallocate($this->image,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255));
		 }else{
		 	$this->randBgColor=imagecolorallocate($this->image,$this->bgColor[0],$this->bgColor[1],$this->bgColor[2]);
		 }
		 imagefill($this->image,0,0,$this->randBgColor);
	 }
 
	 //��������ַ�
	 public function randText($type){
		 $string='';
		 switch($type){
			 case 'en':
				 $str='ABCDEFGHJKLMNPQRSTUVWXY3456789';
				 for($i=0;$i<$this->textNum;$i++){
					 $string=$string.','.$str[mt_rand(0,29)];
				 }
				 break;
			 case 'cn':
				 for($i=0;$i<$this->textNum;$i++) {
				 	$string=$string.','.chr(rand(0xB0,0xCC)).chr(rand(0xA1,0xBB));
				 }
				 $string=iconv('GB2312','UTF-8',$string); //ת�����뵽utf8
				 break;
		 }
		 return substr($string,1);
	 }
 
	 //������ֵ���֤��
	 public function createText(){
		 $textArray=explode(',',$this->randText($this->textLang));
		 $this->textContent=join('', $textArray);
		 if(empty($this->fontColor)){
		 	$this->randFontColor=imagecolorallocate($this->image,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));
		 }else{
		 	$this->randFontColor=imagecolorallocate($this->image,$this->fontColor[0],$this->fontColor[1],$this->fontColor[2]);
		 }
		 for($i=0;$i<$this->textNum;$i++){
			 $angle=mt_rand(-1,1)*mt_rand(1,20);
			 imagettftext($this->image,$this->fontSize,$angle,5+$i*floor($this->fontSize*1.3),floor($this->height*0.75),$this->randFontColor,$this->fontFamily,$textArray[$i]);
		 }
	 }
 
	 //���ɸ��ŵ�
	 public function createNoisePoint(){
		 for($i=0;$i<$this->noisePoint;$i++){
			 $pointColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
			 imagesetpixel($this->image,mt_rand(0,$this->width),mt_rand(0,$this->height),$pointColor);
		 }
	 }
	 
	 //����������
	 public function createNoiseLine(){
		 for($i=0;$i<$this->noiseLine;$i++) {
			 $lineColor=imagecolorallocate($this->image,mt_rand(0,255),mt_rand(0,255),20);
			 imageline($this->image,0,mt_rand(0,$this->width),$this->width,mt_rand(0,$this->height),$lineColor);
		 }
	 }
 
	 //Ť������
	 public function distortionText(){
		 $this->distortionImage=imagecreatetruecolor($this->width,$this->height);
		 imagefill($this->distortionImage,0,0,$this->randBgColor);
		 for($x=0;$x<$this->width;$x++){
			 for($y=0;$y<$this->height;$y++){
				 $rgbColor=imagecolorat($this->image,$x,$y);
				 imagesetpixel($this->distortionImage,(int)($x+sin($y/$this->height*2*M_PI-M_PI*0.5)*3),$y,$rgbColor);
			 }
		 }
		 $this->image=$this->distortionImage;
	 }
 
	 //������֤��ͼƬ
	 public function createImage(){
	 	 //��������ͼƬ
		 $this->initImage();
		 //�����֤���ַ�
		 $this->createText();
		  //Ť������
		 if($this->distortion){
		 	$this->distortionText();
		 }
		 //�������ŵ�
		 $this->createNoisePoint();
		 //����������
		 $this->createNoiseLine();
		 if($this->showBorder){
		 	imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$this->randFontColor);
		 }
		 header("Content-type:".image_type_to_mime_type(IMAGETYPE_PNG));
		 imagepng($this->image);
		 imagedestroy($this->image);
		 return $this->textContent;
	}
}

//End Of Script
