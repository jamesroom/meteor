<?php
date_default_timezone_set("Asia/Shanghai");
/**
 * 处理整个项目中的配置文件信息
 *
 */
require_once(PHPLIB_ROOT . "/ext/inecfg.inc.php");
 
require_once('DB.php');


class Config
{
	/**
	 * 错误编码
	 *
	 * @var int
	 */
	public static $errCode = 0;

	/**
	 * 错误信息
	 *
	 * @var string
	 */
	public static $errMsg = '';

	/**
	 * 保存项目中DB的句柄
	 *
	 */
	private static $DB;

	/**
	 * DB的配置
	 *
	 * @var array
	 */
	private static $DBCfg;
	
	/*
	 * 初始化配置变量
	 */
	private static function init()
	{
		global $_DB_CFG;
		
		// DB 配置
		if (empty(self::$DBCfg)) {
			if(isset($_DB_CFG)){
				self::$DBCfg = &$_DB_CFG;
			} else {
				self::$DBCfg = '';
			}
		}

	}

	/**
	 * 清除错误标识，在每个函数调用前调用
	 */
	private static function clearERR()
	{
		self::$errCode = 0;
		self::$errMsg  = '';
	}

	/**
	 * 获得不分 set 的 memcache 对象
	 *
	 * @param	key		资源的key
	 * @return	Memcache		memcache 对象, 出错 false
	 */
	public static function getCache($key)
	{
		self::init();
		self::clearERR();

		// 如果在前面已创建该 cache 资源，则直接返回
		if (isset(self::$CacheResMap[$key]))
		{
			return self::$CacheResMap[$key];
		}

		// 判断参数
		if (!isset(self::$CacheCfg[$key]))
		{
			self::$errCode = 20000;
			self::$errMsg = "no cache config info for key {$key}";
			return false;
		}

		// cache 配置
		$cfg = self::$CacheCfg[$key];

		// 自动判断是单节点还是多节点 memcache 连接(一级 key 中有 host)
		$MemCache = new Memcache;
		if (isset($cfg['IP'])) {
			// 单节点连接
			$MemCache->connect($cfg['IP'], $cfg['PORT']);
		} else {
			// 多节点连接
			foreach ($cfg['servers'] as $server){
				$MemCache->addServer($server['IP'], $server['PORT'], 0);
			}
			if ($MemCache === false){
				self::$errCode = 20001;
				self::$errMsg = "add memcache server failed";
				return false;
			}
		}
		// 保存到类属性中
		self::$CacheResMap[$key] = $MemCache;
		return 	self::$CacheResMap[$key];
	}

	/**
	 * 获得 DB 对象
	 *
	 * 由于数据库不同于一般的 server ip/port ，这里不支持 $node 参数指定节点，以免出现不必要的问题。
	 *
	 * @param	string	$key		返回 DB 对象
	 * @param	int		$node
	 * @return	DB	DB 对象, 出错 false
	 */
	public static function getDB()
	{
		self::init();
		self::clearERR();
		// 如果在前面已创建该 DB 资源，则直接返回
		if (isset(self::$DB)){
			return self::$DB;
		}

		$cfg = self::$DBCfg;
		// 创建 DB 对象
		$DB = new DB($cfg['IP'], $cfg['PORT'], $cfg['DB'], $cfg['USER'], $cfg['PASSWD']);
//		$DB = new OracleDb($cfg['USER'], $cfg['PASSWD'], $cfg['DB']);
		if (empty($DB) || $DB->errCode > 0) {
			self::$errCode = 20001;
			self::$errMsg = "create DB connnect failed: " . $DB->errCode . " " . $DB->errMsg;
			return false;
		}
		// 保存到类属性中
		self::$DB = $DB;
		return self::$DB;
	}
}

