<?php
date_default_timezone_set("Asia/Shanghai");
/**
 * ����������Ŀ�е������ļ���Ϣ
 *
 */
require_once(PHPLIB_ROOT . "/ext/inecfg.inc.php");
 
require_once('DB.php');


class Config
{
	/**
	 * �������
	 *
	 * @var int
	 */
	public static $errCode = 0;

	/**
	 * ������Ϣ
	 *
	 * @var string
	 */
	public static $errMsg = '';

	/**
	 * ������Ŀ��DB�ľ��
	 *
	 */
	private static $DB;

	/**
	 * DB������
	 *
	 * @var array
	 */
	private static $DBCfg;
	
	/*
	 * ��ʼ�����ñ���
	 */
	private static function init()
	{
		global $_DB_CFG;
		
		// DB ����
		if (empty(self::$DBCfg)) {
			if(isset($_DB_CFG)){
				self::$DBCfg = &$_DB_CFG;
			} else {
				self::$DBCfg = '';
			}
		}

	}

	/**
	 * ��������ʶ����ÿ����������ǰ����
	 */
	private static function clearERR()
	{
		self::$errCode = 0;
		self::$errMsg  = '';
	}

	/**
	 * ��ò��� set �� memcache ����
	 *
	 * @param	key		��Դ��key
	 * @return	Memcache		memcache ����, ���� false
	 */
	public static function getCache($key)
	{
		self::init();
		self::clearERR();

		// �����ǰ���Ѵ����� cache ��Դ����ֱ�ӷ���
		if (isset(self::$CacheResMap[$key]))
		{
			return self::$CacheResMap[$key];
		}

		// �жϲ���
		if (!isset(self::$CacheCfg[$key]))
		{
			self::$errCode = 20000;
			self::$errMsg = "no cache config info for key {$key}";
			return false;
		}

		// cache ����
		$cfg = self::$CacheCfg[$key];

		// �Զ��ж��ǵ��ڵ㻹�Ƕ�ڵ� memcache ����(һ�� key ���� host)
		$MemCache = new Memcache;
		if (isset($cfg['IP'])) {
			// ���ڵ�����
			$MemCache->connect($cfg['IP'], $cfg['PORT']);
		} else {
			// ��ڵ�����
			foreach ($cfg['servers'] as $server){
				$MemCache->addServer($server['IP'], $server['PORT'], 0);
			}
			if ($MemCache === false){
				self::$errCode = 20001;
				self::$errMsg = "add memcache server failed";
				return false;
			}
		}
		// ���浽��������
		self::$CacheResMap[$key] = $MemCache;
		return 	self::$CacheResMap[$key];
	}

	/**
	 * ��� DB ����
	 *
	 * �������ݿⲻͬ��һ��� server ip/port �����ﲻ֧�� $node ����ָ���ڵ㣬������ֲ���Ҫ�����⡣
	 *
	 * @param	string	$key		���� DB ����
	 * @param	int		$node
	 * @return	DB	DB ����, ���� false
	 */
	public static function getDB()
	{
		self::init();
		self::clearERR();
		// �����ǰ���Ѵ����� DB ��Դ����ֱ�ӷ���
		if (isset(self::$DB)){
			return self::$DB;
		}

		$cfg = self::$DBCfg;
		// ���� DB ����
		$DB = new DB($cfg['IP'], $cfg['PORT'], $cfg['DB'], $cfg['USER'], $cfg['PASSWD']);
//		$DB = new OracleDb($cfg['USER'], $cfg['PASSWD'], $cfg['DB']);
		if (empty($DB) || $DB->errCode > 0) {
			self::$errCode = 20001;
			self::$errMsg = "create DB connnect failed: " . $DB->errCode . " " . $DB->errMsg;
			return false;
		}
		// ���浽��������
		self::$DB = $DB;
		return self::$DB;
	}
}

