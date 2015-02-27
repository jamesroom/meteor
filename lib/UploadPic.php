<?php
/**
 * �����ϴ����ļ�
 * 
 * @author Jimhuang
 */

class UploadPic
{
	/**
	 * �������
	 * @var int
	 */
	public static $errCode = 0;

	/**
	 * ������Ϣ
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
	 * ������ļ���׺��
	 */
	private static $allowedFileExt = array( 'gif', 'jpeg', 'jpg', 'png' );
	
	/**
	 * �����ͼƬ��׺��
	 */
	private static $imageExt	= array( 'gif', 'jpeg', 'jpg', 'png' );
	
	/**
	 * ��������ʶ����ÿ����������ǰ����
	 */
	private static function clearERR()
	{
		self::$errCode = 0;
		self::$errMsg  = '';
	}
	
	
	/**
	 * �����ϴ���ͼƬ��Ϣ������ָ��Ŀ¼����ָ���ļ������ļ�
	 *
	 * @param	array	$uploadInfo		�ļ���Ϣ����
	 * @param	bool	$makeScriptSafe	�Ƿ���˷Ƿ��ַ���	
	 * @return	array	�����ļ�������ͼƬ��Ϣ����
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
			'isImage'	=> $isImage,		// �Ƿ���ͼƬ
			'width'		=> $imageWidth,		// ͼƬ���
			'height'	=> $imageHeight,	// ͼƬ�߶�
			'type'		=> $fileExtension,	// ͼƬ����
			'fileDir'	=> $outFileDir,		// ͼƬ��ŵ�Ŀ¼
			'fileName'	=> $outFileName,	// ͼƬ��ŵ��ļ���
			'parsedFileName'	=> $parsedFileName,	//����׺��ͼƬ����ļ���
			'saveUploadFile'	=> $savedUploadFile,//ͼƬ��ŵ�ȫ·��
		);
		
		return $picInfomation;
	}
	
	/**
	 * ��ʼ���ϴ�ͼƬ��Ϣ����
	 *
	 * @param	array	$data
	 * @return	array	$picInfo
	 */
	private static function initPicInfo($data)
	{
		self::clearERR();
		
		$imageIndexArr	= array(
			'uploadFromField'	=> 'FILE_UPLOAD',	// ͼƬ�ϴ����ļ�����
			'outFileDir'		=> './',			// ���ɵ��ļ���ŵ�Ŀ¼
			'outFileName'		=> '',				// �����ļ�������
			'allowedFileExt'	=> self::$allowedFileExt,	// ������ļ���չ��
			'imageExt'			=> self::$imageExt,	// �����ͼƬ��չ��
			'maxFileSize'		=> 0,				// �ļ���С����
			'forceDataExt'		=> '',		// ����׺���ļ����ĸ��Ӳ��֣�һ�����ڱ�ʶ�ļ������ĸ����ܿ飩
		);
		
		$picInfo	= array_merge($imageIndexArr, $data);

		return $picInfo;
	}
}
	
//End of script
	
