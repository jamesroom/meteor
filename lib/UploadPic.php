<?php
/**
 * 处理上传的文件
 * 
 * @author Jimhuang
 */

class UploadPic
{
	/**
	 * 错误编码
	 * @var int
	 */
	public static $errCode = 0;

	/**
	 * 错误信息
	 * @var string
	 */
	public static $errMsg = '';
	
	private static $typeArr = array( 
		1 => 'GIF',
		2 => 'JPG', 
		3 => 'PNG', 
		4 => 'SWF', 
		5 => 'PSD', 
		6 => 'BMP', 
		7 => 'TIFF', //intel byte order
		8 => 'TIFF', //motorola byte order
		9 => 'JPC',
		10 => 'JP2', 
		11 => 'JPX',
		12 => 'JB2',
		13 => 'SWC',
		14 => 'IFF',
		15 => 'WBMP',
		16 => 'XBM',
	);
	
	/**
	 * 允许的文件后缀名
	 */
	private static $allowedFileExt = array( 'gif', 'jpeg', 'jpg', 'png' );
	
	/**
	 * 允许的图片后缀名
	 */
	private static $imageExt	= array( 'gif', 'jpeg', 'jpg', 'png' );
	
	/**
	 * 清除错误标识，在每个函数调用前调用
	 */
	private static function clearERR()
	{
		self::$errCode = 0;
		self::$errMsg  = '';
	}
	
	
	/**
	 * 处理上传的图片信息，并在指定目录生成指定文件名的文件
	 *
	 * @param	array	$uploadInfo		文件信息数组
	 * @param	bool	$makeScriptSafe	是否过滤非法字符串	
	 * @return	array	生成文件后的相关图片信息数组
	 */
	
	public static function uploadProcess($uploadInfo, $makeScriptSafe=true)
	{
		self::clearERR();
		$picInfo			= self::initPicInfo($uploadInfo);
		
		$uploadFormField	= $picInfo['uploadFromField'];
		
		if ($_FILES[$uploadFormField ]['error'] > 0) {
			self::$errCode	= 1000 + $_FILES[$uploadFormField ]['error'];
			
			return false;
		}

		//------------------------------------------
		// Naughty Mozilla likes to use "none" to indicate an empty upload field.
		// I love universal languages that aren't universal.
		//------------------------------------------
		
		if ($_FILES[$uploadFormField]['name'] == "" || !$_FILES[$uploadFormField]['name']
			|| !$_FILES[$uploadFormField]['size']	|| ($_FILES[$uploadFormField]['name'] == "none") ) {
			self::$errCode	= 101;
			self::$errMsg	= 'No file upload';
			return false;
		}
		
		//------------------------------------------
		// Set up some variables to stop carpals developing
		//------------------------------------------
		
		$fileName	= $_FILES[$uploadFormField]['name'];
		$fileSize	= $_FILES[$uploadFormField]['size'];
		$fileTemp	= $_FILES[$uploadFormField]['tmp_name'];

		//------------------------------------------
		// Check the file size
		//------------------------------------------
		$maxFileSize	= $picInfo['maxFileSize'];
		if ( !empty($maxFileSize) && ($fileSize>$maxFileSize) ) {
			self::$errCode	= 102;
			self::$errMsg	= 'Upload file too big';
			return false;
		}
		
		//------------------------------------------
		// Get file extension
		//------------------------------------------
		
		$fileExtension	= strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		if (empty($fileExtension)) {
			self::$errCode	= 103;
			self::$errMsg	= 'upload type is null';
			return false;
		}
		
		//------------------------------------------
		// Valid extension?
		//------------------------------------------
		$allowedFileExtArr	= $picInfo['allowedFileExt'];
		if ( !in_array( $fileExtension, $allowedFileExtArr ) ) {
			self::$errCode	= 104;
			self::$errMsg	= 'Upload file too big';
			return false;
		}

		//------------------------------------------
		// Is it an image?
		//------------------------------------------
		$imageExt	= $picInfo['imageExt'];
		$isImage	= 0;
		if ( is_array($imageExt) && !empty($imageExt) ) {
			if ( in_array( $fileExtension, $imageExt ) ) { 
				$isImage = 1;
			}
		}
		
		if (! $isImage) {
			$fileType	= $_FILES[$uploadFormField]['type'];
		} else {
			//only upload image file,so...this will be safety!
			$fileInfo	= @getimagesize($_FILES[$uploadFormField]['tmp_name']);

			if ($fileInfo === false) {
				self::$errCode	= 105;
				self::$errMsg	= "getimagesize uploadFormField-{$uploadFormField},tmp_name-{$_FILES[$uploadFormField]['tmp_name']} fail";
				return false;
			}

			$fileType		= $fileInfo['mime'];
			
			$fileExtension	= strtolower(self::$typeArr[$fileInfo[2]]);
			
			if ( ! in_array( $fileExtension, $allowedFileExtArr ) ) {
				self::$errCode	= 106;
				self::$errMsg	= "fileExtension-{$fileExtension} is illegal.";
				return false;
			}
			
			$imageWidth		= $fileInfo[0];
			$imageHeight	= $fileInfo[1];
		}

		//------------------------------------------
		// Naughty Opera adds the filename on the end of the
		// mime type - we don't want this.
		//------------------------------------------
		
		$fileType	= preg_replace( "/^(.+?);.*$/", "\\1", $fileType );
		
		//------------------------------------------
		// Convert file name?
		// In any case, file name is WITHOUT extension
		//------------------------------------------
		$outFileName	= $picInfo['outFileName'];
		if ( !empty($outFileName) ) {
			$parsedFileName	= $outFileName;
		} else {
			$parsedFileName	= str_replace('.'.$fileExtension, '', $fileName);
		}
	
		//------------------------------------------
		// Make safe?
		//------------------------------------------
		
		if ( $makeScriptSafe ) {
			if ( preg_match( "/\.(cgi|pl|js|asp|php|shtml|html|htm|jsp|jar)/", $fileName ) ) {
				$fileType		= 'text/plain';
				$fileExtension	= 'txt';
			}
		}
		
		//------------------------------------------
		// Add on the extension...
		//------------------------------------------
		$forceDataExt	= $picInfo['forceDataExt'];
		if ( !empty($forceDataExt) && !empty($isImage) ) {
			$fileExtension	= str_replace( ".", "", $forceDataExt ); 
		}
		
		$parsedFileName	.= '.' . $fileExtension;
		
		//------------------------------------------
		// Copy the upload to the uploads directory
		//------------------------------------------
		$outFileDir	= $picInfo['outFileDir'];
		if(!is_dir($outFileDir)) {
			if (! @mkdir($outFileDir, 0777, true) ) {
				self::$errCode = 107;
				self::$errMsg	= 'Cannot create upload dir' . $outFileDir;
				return false;
			}
		}

		if (! is_writeable($outFileDir) ) {
			@chmod( $outFileDir, 0777 );
		}

		$savedUploadFile	= $outFileDir.'/'.$parsedFileName;

		if (@is_uploaded_file($fileTemp)) {		
			if (!@move_uploaded_file($fileTemp, $savedUploadFile)) {
				self::$errCode	= 113;
				self::$errMsg	= 'Cannot move uploaded file';
				return false;
			} else {
				@chmod($savedUploadFile, 0777);
			}
		} else {
			self::$errCode	= 114;
			self::$errMsg	= 'Invalid uploaded file';
			return false;
		}
		
		$picInfomation	= array(
			'isImage'	=> $isImage,		// 是否是图片
			'width'		=> $imageWidth,		// 图片宽度
			'height'	=> $imageHeight,	// 图片高度
			'type'		=> $fileExtension,	// 图片类型
			'fileDir'	=> $outFileDir,		// 图片存放的目录
			'fileName'	=> $outFileName,	// 图片存放的文件名
			'parsedFileName'	=> $parsedFileName,	//带后缀的图片存放文件名
			'saveUploadFile'	=> $savedUploadFile,//图片存放的全路径
		);
		
		return $picInfomation;
	}
	
	/**
	 * 初始化上传图片信息数组
	 *
	 * @param	array	$data
	 * @return	array	$picInfo
	 */
	private static function initPicInfo($data)
	{
		self::clearERR();
		
		$imageIndexArr	= array(
			'uploadFromField'	=> 'FILE_UPLOAD',	// 图片上传的文件名称
			'outFileDir'		=> './',			// 生成的文件存放的目录
			'outFileName'		=> '',				// 生成文件的名称
			'allowedFileExt'	=> self::$allowedFileExt,	// 允许的文件扩展名
			'imageExt'			=> self::$imageExt,	// 允许的图片扩展名
			'maxFileSize'		=> 0,				// 文件大小限制
			'forceDataExt'		=> '',		// 除后缀外文件名的附加部分（一般用于标识文件属于哪个功能块）
		);
		
		$picInfo	= array_merge($imageIndexArr, $data);

		return $picInfo;
	}
}
	
//End of script
	
