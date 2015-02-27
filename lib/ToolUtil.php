<?php
/**
 * 封装一些常用的函数
 */


abstract class ToolUtil
{
    /**
     * 错误编码
     */
    public static $errCode = 0;
    /**
     * 错误信息,无错误为''
     */
    public static $errMsg = '';

    /**
     * 客户端IP，一次请求内，这个值不会变
     * @var string
     */
    private static $clientIP = false;

    /**
     * 数据统计页面层级
     * @var int
     */
    private static $yPageLevel = 0;
    /**
     * 数据统计页面ID
     * @var int
     */
    private static $yPageId = 0;

    /**
     * 清除错误信息,在每个函数的开始调用
     */
    private static function clearError() {
        self::$errCode = 0;
        self::$errMsg  = '';
    }

    private static function setERR($code, $msg=''){
        self::$errCode = $code;
        self::$errMsg = $msg;
    }
    /**
     * 判断是否存在火星文
     * 标点符号也被算作是火星文的一部分，如果想提出这块，需要自己根据这个函数再写一个具体的函数
     *
     * @param string $str
     * @return 存在火星文返回true的数，否则返回false
     */
    public static function hasMarsWord($str){
        $b = 0;
        $count = strlen($str);
        $pChar = 0;
        for($i = 0; $i < $count; $i ++){
            $cCode = ord($str[$i]);
            if (($cCode > 47 && $cCode < 58)){ // 0-9
                $b = 0;
            } else if (($cCode > 64 && $cCode < 91)){ // A-Z
                $b = 0;
            } else if (($cCode > 96 && $cCode < 123)){ // a-z
                $b = 0;
            } else if($cCode == 32){ // 空格
                $b = 0;
            } else {
                if($b == 1){ // 第1、3...位
                    if ($pChar > 0xF7){
                        if ($cCode < 0x40 || $cCode > 0xA0 || $cCode == 0x7F) return true;
                    } else {
                        if($cCode < 0x40 || $cCode > 0xFE || $cCode == 0x7F) return true;
                    }
                } else { // 第0、2、4...位
                    if($cCode < 0x81 || $cCode > 0xFE || ($cCode >= 0xA1 && $cCode <= 0xA9)) return true; // A1到A9是一些乱七八糟的符号
                    $pChar = $cCode;
                }

                $b = $b == 0 ? 1 : 0;
            }
        }

        return false;
    }

    /**
     * 验证qq号码的合法性
     * 32 bit systems have a maximum signed integer range of -2147483648 to 2147483647
     * 64 bit systems have a maximum signed integer range of -9223372036854775808
     * to 9223372036854775807
     * So we can use intval() safely to convert qq number
     *
     * @author		ericfu
     *
     * @param		string	$uin, 用户qq号码
     *
     * @return 		bool 正确返回true, 错误返回false
     */
    public static function checkUin($uin) {
        self::clearError();

        if ( !self::checkInt($uin) ) {
            self::$errCode = 10601;
            self::$errMsg = 'uin is not integer';

            return false;
        }

        if ( $uin > 10000 ) {
            return true;
        }

        self::$errCode = 10601;
        self::$errMsg = 'uin is not integer';

        return false;
    }

    /**
     * 判断是为中文
     * GBK一个汉字包含两个字节，其中：第一个字节                 第二个字节
     *		GBK      |  x81-0xFE（129-254）    |   0x40-0xFE（64-254）
     * 低位不包含127，其他相关请参考http://ff.163.com/newflyff/gbk-list/
     * @param string $str
     * @return
     */
    public static function IsChineseWord($str)
    {
        $str = trim($str);
        $count = strlen($str);
        $pChar = 0;
        for($i = 0; $i < $count; $i ++){
            $cCode = ord($str[$i]);
            if($i & 0x1){ // 第1、3...位
                if ($pChar > 0xF7){
                    if ($cCode < 0x40 || $cCode > 0xA0 || $cCode == 0x7F) return false;
                } else {
                    if($cCode < 0x40 || $cCode > 0xFE || $cCode == 0x7F) return false;
                }
            } else { // 第0、2、4...位
                if($cCode < 0x81 || $cCode > 0xFE || ($cCode >= 0xA1 && $cCode <= 0xA9)) return false; // A1到A9是一些乱七八糟的符号
                $pChar = $cCode;
            }
        }

        return true;
    }

    /**
     * 判断地址的合法性，不做长度校验
     * 符合条件：中文，数字，英文，一部分符号
     * 符号包括：. , () - _
     * @param string $str
     * @return
     */
    public static function checkAddress($str)
    {
        $str = trim($str);
        $b = 0;
        $count = strlen($str);
        $pChar = 0;
        for($i = 0; $i < $count; $i ++){
            $cCode = ord($str[$i]);
            if (($cCode > 47 && $cCode < 58)){ // 0-9
                $b = 0;
            } else if (($cCode > 64 && $cCode < 91)){ // A-Z
                $b = 0;
            } else if (($cCode > 96 && $cCode < 123)){ // a-z
                $b = 0;
            } else if($cCode == 32 || $cCode == 46 || $cCode == 40 || $cCode == 41 || $cCode == 45){ // 空格.()-
                $b = 0;
            } else {
                if($b == 1){ // 第1、3...位
                    if ($pChar > 0xF7){
                        if ($cCode < 0x40 || $cCode > 0xA0 || $cCode == 0x7F) return false;
                    } else {
                        if($cCode < 0x40 || $cCode > 0xFE || $cCode == 0x7F) return false;
                    }
                } else { // 第0、2、4...位
                    if($cCode < 0x81 || $cCode > 0xFE || ($cCode >= 0xA1 && $cCode <= 0xA9)) return false; // A1到A9是一些乱七八糟的符号
                    $pChar = $cCode;
                }

                $b = $b == 0 ? 1 : 0;
            }
        }

        return true;
    }

    public static function checkValidCharset($str){
        $str = trim($str);
        $b = 0;

        $count = strlen($str);
        $pChar = 0;
        for($i = 0; $i < $count; $i ++){
            $cCode = ord($str[$i]);
            if (($cCode >= 0 && $cCode < 8) || ($cCode >= 11 && $cCode <= 12) || ($cCode >= 14 && $cCode <= 31) || $cCode == 127){
                return false;
            } else if($cCode < 128){
                $b = 0;
            } else {
                if($b == 1){ // 第1、3...位
                    if ($pChar > 0xF7){
                        if ($cCode < 0x40 || $cCode > 0xA0 || $cCode == 0x7F) return false;
                    } else {
                        if($cCode < 0x40 || $cCode > 0xFE || $cCode == 0x7F) return false;
                    }
                } else { // 第0、2、4...位
                    if($cCode < 0x81 || $cCode > 0xFE) return false;
                    $pChar = $cCode;
                }

                $b = $b == 0 ? 1 : 0;
            }
        }

        return true;
    }
    /**
     * 判断n是否是整数
     *
     * @author		ericfu
     *
     * @param		int		$n
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkInt($n) {
        self::clearError();

        if (!is_numeric($n)) {
            self::$errCode	= 10624;
            self::$errMsg	= 'param $n must be a number str. $n=' . $n;
            return false;
        }
        $n = trim($n);

        if ( preg_match('/^[1-9][0-9]*$/', $n) === 1 ) {
            return true;
        }

        self::$errCode = 10600;
        self::$errMsg = 'data is not int';

        return false;
    }

    /**
     * 判断日期的格式是否是正确的
     * 日期的格式为:YYYY-MM-DD
     *  		or:YYYYMMDD
     * @author		ericfu
     *
     * @param		string	$d, 时间字符串
     *
     * @return		bool	正确返回true, 错误返回false
     *
     * @ preg_match modified by clydechang
     */
    public static function checkDate($d) {
        self::clearError();

        $d = trim($d);
        $regs = array();

        //if ( preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $d, $regs) !== 1 )
        if ( preg_match('/^([0-9]{4})[-]*([0-9]{1,2})[-]*([0-9]{1,2})$/', $d, $regs) !== 1 )
        {
            self::$errCode = 10603;
            self::$errMsg = 'date is invalid';

            return false;
        }

        if ( checkdate($regs[2], $regs[3], $regs[1]) ) {
            return true;
        }

        self::$errCode = 10603;
        self::$errMsg = 'date is invalid';

        return false;
    }

    /**
     * 判断日期时间的格式是否是正确的
     * 日期的格式为:YYYY-MM-DD HH:mm:ss
     *
     * @author		ericfu
     *
     * @param		string	$t, 时间字符串
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkDateTime($t) {
        self::clearError();

        $t = trim($t);
        $regs = array();

        if ( preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/', $t, $regs) !== 1 ) {
            self::$errCode = 10604;
            self::$errMsg = 'date time is invalid';

            return false;
        }

        if ( checkdate($regs[2], $regs[3], $regs[1]) === false ) {
            self::$errCode = 10604;
            self::$errMsg = 'date time is invalid';

            return false;
        }

        if ( $regs[4] < 0 || $regs[4] > 23 || $regs[5] < 0 || $regs[5] > 59 || $regs[6] < 0 || $regs[6] > 59 ) {
            self::$errCode = 10604;
            self::$errMsg = 'date time is invalid';

            return false;
        }

        return true;
    }


    /**
     * 根据出生月日计算星座
     *
     * @param		int		$birth_month, 出生月
     * @param		int		$birth_day, 出生日
     *
     * @return		int		$astro, 星座对应的数字(1至12), 出错返回0
     */
    public static function genAstro($birth_month, $birth_day) {
        $astro_start_array = array(
            1 => 19, 2 => 18, 3 => 20, 4 => 20, 5 => 20, 6 => 21,
            7 => 22, 8 => 22, 9 => 22, 10 => 22, 11 => 21, 12 => 21
        );

        $birth_month = intval($birth_month);
        if ($birth_month < 1 || $birth_month > 12) {
            return 0;
        }

        $birth_day = intval($birth_day);
        if ($birth_day < 1 || $birth_day > 31) {
            return 0;
        }

        if ($birth_day > $astro_start_array[$birth_month]) {
            $astro = $birth_month - 2;
        } else {
            $astro = $birth_month - 3;
        }

        $astro = $astro > 0 ? $astro : $astro + 12;

        return $astro;
    }

    /**
     * 转换天数格式
     *
     * @param int $date:	代表天数的数字
     * @return array($id=>$detail)
     */

    public static function changeDate($date)
    {
        $date	= intval($date);
        $j		= 1;
        $weekArray	= array('一', '二', '三', '四', '五', '六', '天');
        $days		= array();
        for ($i=0; $i<7; $i=$i+1)
        {
            if ($date & $j) {
                $days[$i+1]	= '星期' . $weekArray[$i];
            }
            $j	= $j * 2;
        }
        return $days;
    }

    /**
     * 验证用户的email信息, 正确返回true, 否则返回false
     *
     * @author		ericfu
     * @modifier	peterdu
     *
     * @param		string	$email, 用户email
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkEmail($email) {
        self::clearError();

        $email = trim($email);
        $pt = '/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/i';

        if ( preg_match($pt, $email) === 1 ) {
            return true;
        }

        self::$errCode = 10623;
        self::$errMsg = 'email is invalid';

        return false;
    }

    /**
     * 验证邮编
     *
     * @author		ericfu
     *
     * @param		string	$zip, 验证邮编
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkZip($zip) {
        self::clearError();

        if ( preg_match('/^[1-9]\d{5}$/', trim($zip)) ) {
            return true;
        }

        self::$errCode = 10605;
        self::$errMsg = 'zip code is invalid';

        return false;
    }

    /**
     * 验证身份证号码
     *
     * @author		ericfu
     *
     * @param		string	$id, 身份证号码
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkIDCard($id) {
        self::clearError();

        if ( preg_match('/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i', trim($id)) ) {
            return true;
        }

        self::$errCode = 10606;
        self::$errMsg = 'IDCard number is invalid';

        return false;
    }

    /**
     * 验证手机号码
     *
     * @author		ericfu
     *
     * @param		phone	$phone, 电话号码
     * @param 		string		$type, CHN中国大陆电话号码, INT国际电话号码
     *
     * @return		bool	正确返回true, 错误返回false
     */
    public static function checkMobilePhone($phone, $type = 'CHN')
    {
        self::clearError();
        $ret = false;
        switch($type){
            case "CHN":
                $ret = (preg_match("/^((\(\d{3}\))|(\d{3}\-))?1\d{10}$/", trim($phone)) ? true : false);
                break;
            case "INT":
                $ret = (preg_match("/^((\(\d{3}\))|(\d{3}\-))?\d{6,20}$/", trim($phone)) ? true : false);
                break;
        }

        if ($ret === false) {
            self::$errCode = 10607;
            self::$errMsg  = 'Mobile Phone is not illege.';
            return false;
        }

        return true;
    }

    /**
     * 验证电话号码
     *
     * @author		ericfu
     *
     * @param		string		$phone, 电话号码
     * @param 		string		$type, CHN中国大陆电话号码, INT国际电话号码
     *
     * @return		bool		正确返回true, 错误返回false
     */
    public static function checkPhone($phone, $type = 'CHN') {
        self::clearError();

        $ret = false;

        switch ($type) {
            case 'CHN':
                $ret = preg_match('/^([0-9]{3}|0[0-9]{3})-[1-9][0-9]{6,7}(-[0-9]{1,6})?$/', trim($phone)) ? true : false;
                break;
            case 'INT':
                $ret = preg_match('/^[0-9]{4}-([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/', trim($phone)) ? true : false;
        }

        if ( $ret === false ) {
            self::$errCode = 10608;
            self::$errMsg = 'Phone is invalid';

            return false;
        }

        return true;
    }

    /**
     * 验证ip地址
     *
     * @author		ericfu
     * @modifier	peterdu
     *
     * @param		string	$ip, ip地址
     *
     * @return		bool	正确返回true, 否则返回false
     */
    public static function checkIP($ip) {
        self::clearError();

        $ip = trim($ip);
        $pt = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

        if ( preg_match($pt, $ip) === 1 ) {
            return true;
        }

        self::$errCode = 10609;
        self::$errMsg = 'IP is invalid.';

        return false;
    }

    /**
     * 验证url地址
     *
     * @author		ericfu
     * @modifier	peterdu
     *
     * @param		string	$url, url地址
     * @return		bool	正确返回true, 否则返回false
     */
    public static function checkURL($url) {
        self::clearError();

        $pt = '/^(https?:\/\/)?([a-z]([a-z0-9\-]*\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&amp;\/]*)?)?(#[a-z][a-z0-9_]*)?$/';

        if ( preg_match($pt, $url) === 1 ) {
            return true;
        }

        self::$errCode = 10610;
        self::$errMsg = 'url is invalid';

        return false;
    }

    public static function checkLoginOrRedirect(){
        $uid = IUser::getLoginUid();

        if( $uid === false || $uid == 0 ){
            $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            self::redirect('/admin/index.php?mod=login&act=page&url='.urlencode($currentUrl));
            return 0;
        }

        return $uid;
    }

    public static function checkWebLoginOrRedirect($direct){
        $uid = IUser::getwebLoginUid();
        if( $uid === false || $uid == 0 ){
            $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            self::redirect('http://www.rongxinwealth.com/login.html&url='.urlencode($currentUrl));
            return 0;
        }

        return $uid;
    }

    /*public static function checkWebLoginOrRedirect(){
        $uid = IUser::getLoginUid();

        if( $uid === false || $uid == 0 ){
            $currentUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            self::redirect('http://www.rongxinwealth.com/json.php?mod=registerfill&act=&url='.urlencode($currentUrl));
            return 0;
        }

        return $uid;
    }*/

    /**
     * 重定向页面，防止意外缓存
     * @param string $url 目标url
     */
    public static function redirect($url){
        self::noCacheHeader();
        header("location: " . $url);
        exit;
    }

    /**
     * 强制无缓存
     * @param string $url 目标url
     */
    public static function noCacheHeader(){
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: max-age=0' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
    }
    /**
     * 判断字符串是否只包含英文字符
     *
     * @author		ericfu
     *
     * @param		string	$s, 字符串
     *
     * @return		bool
     */
    public static function isAlpha($s) {
        self::clearError();

        if ( preg_match('/^[A-Za-z]+$/', $s) ) {
            return true;
        }

        self::$errCode = 10611;
        self::$errMsg = 'analphabetic character is detected';

        return false;
    }

    /**
     * 获取客户端IP
     *
     * @author		mangoguo
     * @modifier	kulin
     *
     * @return		string	$ip, IP地址串
     */
    public static function getClientIP($recalc = false) {
        self::clearError();

        if (!$recalc && self::$clientIP !== false) {
            return self::$clientIP;
        }

        if ( isset($_SERVER['HTTP_QVIA']) ) {
            $ip = self::qvia2ip($_SERVER['HTTP_QVIA']);

            if ( $ip ) {
                return self::$clientIP = $ip;
            }
        }

        if ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
            return self::$clientIP = self::checkIP($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '0.0.0.0';
        }

        if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
            $ip = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
            do {
                $tmpIp = explode('.', $ip);
                //-------------------
                // skip private ip ranges
                //-------------------
                // 10.0.0.0 - 10.255.255.255
                // 172.16.0.0 - 172.31.255.255
                // 192.168.0.0 - 192.168.255.255
                // 127.0.0.1, 255.255.255.255, 0.0.0.0
                //-------------------
                if(is_array($tmpIp) && count($tmpIp) == 4){
                    if (($tmpIp[0] != 10) && ($tmpIp[0] != 172) && ($tmpIp[0] != 192) && ($tmpIp[0] != 127) && ($tmpIp[0] != 255) && ($tmpIp[0] != 0) ){
                        return self::$clientIP = $ip;
                    }
                    if(($tmpIp[0] == 172) && ($tmpIp[1] < 16 || $tmpIp[1] > 31)){
                        return self::$clientIP = $ip;
                    }
                    if(($tmpIp[0] == 192) && ($tmpIp[1] != 168)){
                        return self::$clientIP = $ip;
                    }
                    if (($tmpIp[0] == 127) && ($ip != '127.0.0.1')){
                        return self::$clientIP = $ip;
                    }
                    if ($tmpIp[0] == 255 && ($ip != '255.255.255.255'))	{
                        return self::$clientIP = $ip;
                    }
                    if ($tmpIp[0] == 0 && ($ip != '0.0.0.0')){
                        return self::$clientIP = $ip;
                    }
                }
            } while ( $ip = strtok(',') );
        }

        if ( isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER']) ) {
            return self::$clientIP = self::checkIP($_SERVER['HTTP_PROXY_USER']) ? $_SERVER['HTTP_PROXY_USER'] : '0.0.0.0';
        }

        if ( isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) ) {
            return self::$clientIP = self::checkIP($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        } else {
            return self::$clientIP = '0.0.0.0';
        }
    }

    /**
     * 获取网通代理或教育网代理带过来的客户端IP
     *
     * @author		kulin
     *
     * @return		string/flase	IP串或false
     */
    public static function qvia2ip($qvia) {
        self::clearError();

        if ( strlen($qvia) != 40 ) {
            return false;
        }

        $ips = array(hexdec(substr($qvia,0,2)), hexdec(substr($qvia,2,2)), hexdec(substr($qvia,4,2)), hexdec(substr($qvia,6,2)));
        $ipbin = pack('CCCC', $ips[0], $ips[1], $ips[2], $ips[3]);
        $m = md5('QV^10#Prefix'.$ipbin.'QV10$Suffix%');

        if ( $m == substr($qvia, 8) ) {
            return implode('.', $ips);
        } else {
            return false;
        }
    }

    public static function gbJsonDecode( $data ){
        $data = str_replace("\r\n", "", $data);
        $data = str_replace("\t", "", $data);
        $data = mb_convert_encoding($data, 'UTF-8', 'GBK');
        $data = json_decode($data, true);
        return empty($data) ? "" : self::_gbJsonDecode( $data );
    }
    private static function _gbJsonDecode( $data ){
        if( is_array( $data) ){
            $res = array();

            foreach( $data as $key => $value)
                $res[$key] = self::_gbJsonDecode( $value );
            return $res;
        }
        else
            return mb_convert_encoding($data, 'GBK', 'UTF-8');
    }

    /**
     * 自定义输出 php 数据成 json 的函数(支持 gb2312, utf-8 请用 php 内置 json_encode)
     * 注：原有的方法处理如“蔡昞”的中文会有问题
     * @author		ericfu
     *
     * @param		mix		$data		需要转换的数组
     */
    public static function gbJsonEncode($data) {
        self::clearError();

        if(is_object($data)){
            $data = get_object_vars($data);
        }

        if(is_array($data)){
           // $data = self::_gbkToUtf8($data);
            $data = json_encode($data);
        } else if(is_string($data)) {
            $data = json_encode($data);
        }else
        {
            return json_encode($data);
        }
        $data = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $data);
        return $data;//edit by jianglun;add
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return iconv("UCS-2BE", "GBK//IGNORE", pack("H*", $matches[1]));'
            ),
            $data);
    }

    private static function _gbkToUtf8($data){
        if(is_object($data)){
            $data = get_object_vars($data);
        }

        if(is_array($data)){
            $res = array();

            foreach($data as $key => $val){
                $key = iconv('GBK', 'UTF-8', $key);
                $res[$key] = self::_gbkToUtf8($val);
            }

            return $res;
        } else if(is_string($data)){
            return iconv('GBK', 'UTF-8', $data);
        } else {
            return $data;
        }
    }

    /**
     * utf-8 截取函数
     *
     * @author		peterdu
     *
     * @param		string		$str, 源字符串
     * @param		int			$len, 截取的视觉长度: 英文算半个, 中文算一个; 截取后结尾为半个中文时直接去掉
     *
     * @return		string		$tmp_str, 截取后的字符串
     */
    public static function utfSubstr($str, $len) {
        self::clearError();

        $len = intval($len);

        if ( $len < 1) {
            return $str;
        }

        $o_len = mb_strlen($str,'utf8');

        if ( $o_len <= $len ) {
            return $str;
        }

        $temp_len = 0;
        $tmp_str = '';

        for ( $i = 0; $i < $o_len && $temp_len < $len; $i++ ) {
            $char = mb_substr($str, $i, 1, 'utf8');
            $temp_len += (ord($char[0]) > 127) ? 1 : 0.5;
            $tmp_str .= $char;
        }

        if ( $temp_len > $len ) {
            $tmp_str = rtrim($tmp_str, $char);
        }

        return $tmp_str;
    }

    /**
     * xml数据 gb 编码转 utf8
     *
     * @author		bennylin
     *
     * @param		string		$xmldata
     * @return		string		$xmldata
     */
    public static function xmlGB2UTF($xmldata) {
        self::clearError();

        if ( substr($xmldata, 0, 6) == '<?xml ' ) {
            $p = strpos($xmldata, "\n");
            if ( $p === FALSE ) {
                return $xmldata;
            }
            $xml_header = substr($xmldata, 0, $p);
            if (stripos($xml_header, 'gbk') ) {
                $s = str_ireplace('gbk', 'utf-8', $xml_header );
            } else {
                $s = str_ireplace('gb2312', 'utf-8', $xml_header );
            }
            return iconv('gb18030', 'utf-8//IGNORE', $s . substr($xmldata, $p));
        }

        return $xmldata;
    }

    /**
     * 过滤用户输入字符中的不合法字符
     *
     * 目前主要直接把一些特殊字符替换成空格，并去掉前后空格
     *
     * @param	string		$str	用户输入的字符
     * @return	string		过滤后输出的字符
     */
    public static function filterInput($str)
    {
        if(empty($str)) return '';
        // 需要替换的特殊字符
        $specialStr = array('\\', '\'', '"', '`', '&', '/', '<', '>');
        $str = str_replace($specialStr, '', $str);

        // 超过一定字符集范围的也需要替换成空格
        $str = trim($str);
        $asciiCode = '/[\x00-\x1f\x7f]/is';
        $str = preg_replace($asciiCode, '', $str);

        return $str;
    }

    /**
     * 过滤不可见字符,主要是XML认为无效的字符
     *
     * @param	string		$str, 源字符串
     *
     * @return	string		$str, 过滤结果
     */
    public static function filterUnvisibleChar($str)
    {
        if ( !strlen($str) ) {
            return '';
        }
        return preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/', '', $str);
    }

    /**
     * 判断字符串是否合法
     *
     * @param string $str		:输入的字符串
     * @param int	 $length 	:字符串的最大允许长度
     * @param Boolean $filter	:是否过滤特殊字符
     * @param Boolean $dirty	:是检查该字符串是否包括脏字还是对脏字进行替换(false:替换，true:检查)
     * @return Boolean|String false:不合法；其他：脏话过滤的结果
     */
    public static function checkInput($str, $length, $filter=true, $dirty=false)
    {
        self::clearError();
        if (! is_numeric ( $length )) {
            self::$errCode = 10616;
            self::$errMsg = 'the length err';
            return false;
        }

        if (empty ( $str ) || ! is_string ( $str ) || strlen ( $str ) > $length) {
            self::$errCode = 10617;
            self::$errMsg = 'the input string is empty or too long';
            return false;
        }

        if($filter){
            $str = self::filterInput ( $str );
        }
        if (empty($str)) {
            self::$errCode = 10620;
            self::$errMsg = 'the string is empty after filterInput';
            return false;
        }

        require_once PHPLIB_ROOT . 'lib/Dirty.php';

        if ($dirty) {
            $isDirty	= Dirty::hasDirty($str);
            if ($isDirty) {
                self::$errCode	= 10619;
                self::$errMsg	= 'the string has dirty. dirty word is ' . $isDirty;
                return false;
            }
            return $str;
        }
        $str = Dirty::replaceDirty ( $str );
        if (empty ( $str )) {
            self::$errCode = 10618;
            self::$errMsg = 'the string is empty after dirtyreplace';
            return false;
        }
        return $str;
    }

    /**
     * 根据传入的时间戳计算出此时间戳所在的年月日等详细信息
     * 格式如下:
     * array( 'year'	=> 2009,
     * 		  'month'	=> 1,
     * 		  'day'		=> 12,
     * 		  'hour'	=> 12,
     * 		  'minute'	=> 2,
     * 		  'sec' 	=> 59,
     * 		  'week' 	=> 1
     * )
     *
     * @param int t 需要解析的时间
     *
     * @return array
     */
    public static function getDetailDateTime($t){
        $data = array();

        $data['year']	= date('Y', $t);
        $data['month']	= date('m', $t);
        $data['day']	= date('d', $t);
        $data['hour']	= date('H', $t);
        $data['minute'] = date('i', $t);
        $data['sec']	= date('s', $t);
        $data['week']	= date('N', $t);
        return $data;
    }



    /**
     * 过滤UBB编码，会把UBB中的一些标签去掉，也会把一些是文本的内容去掉
     * 如:em|video|flash|audio|vphoto|quote|ffg|url|marque|email
     * 使用此函数的时候要注意，此函数只用于分析UBB的文本内容，不能用于UBB的
     * 转换与反转换
     *
     * @param string $content
     * @return string
     */
    public static function filterUBB($content){
        $tmp=htmlspecialchars($content);
        $tmp=stripslashes($tmp);

        $tmp=ereg_replace("\r\n","",$tmp);
        $tmp=ereg_replace("<br/>","",$tmp);
        $tmp=ereg_replace("<br>","",$tmp);
        $tmp=ereg_replace("\r","",$tmp);
        $tmp=preg_replace("/\\t/is","",$tmp);
        $tmp=preg_replace("/\[ft([^\[\]]*?)\]\[\/ft\]/is"," ",$tmp);
        while (preg_match("/\[ft([^\[\]]*?)\](.*?)\[\/ft\]/i",$tmp)){
            $tmp=preg_replace("/\[ft([^\[\]]*?)\](.*?)\[\/ft\]/is","\\2",$tmp);
        }
        $tmp=preg_replace("/\[ft([^\[\]]*?)\](.*?)\[\/ft\]/is","\\2",$tmp);
        $tmp=preg_replace("/\[ffg([^\[\]]*?)\](.*?)\[\/ffg\]/is","\\2",$tmp);
        $tmp=preg_replace("/\[U\](.*?)\[\/U\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[U\]\[\/U\]/is","",$tmp);
        $tmp=preg_replace("/\[M\](.*?)\[\/M\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[M\]\[\/M\]/is","",$tmp);
        $tmp=preg_replace("/\[R\](.*?)\[\/R\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[R\]\[\/R\]/is","",$tmp);
        $tmp=preg_replace("/\[B\](.*?)\[\/B\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[B\]\[\/B\]/is","",$tmp);
        $tmp=preg_replace("/\[I\](.*?)\[\/I\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[I\]\[\/I\]/is","",$tmp);
        $tmp=preg_replace("/\[em\](.*?)\[\/em\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[em\]\[\/em\]/is","",$tmp);
        $tmp=preg_replace("/\[img([^\[\]]*?)\](.*?)\[\/img\]/is","",$tmp);
        $tmp=preg_replace("/\[card([^\[\]]+?)\](.*?)\[\/card\]/is","",$tmp);
        $tmp=preg_replace("/\[qqshow([^\[\]]+?)\](.*?)\[\/qqshow\]/is","",$tmp);
        $tmp=preg_replace("/\[quote([^\[\]]+?)\](.*?)\[\/quote\]/is","",$tmp);
        $tmp=preg_replace("/\[qqVideo([^\[\]\/]+?)\](.*?)\[\/qqVideo\]/is","",$tmp);
        $tmp=preg_replace("/\[video([^\[\]]+?)\](.*?)\[\/video\]/is","",$tmp);
        $tmp=preg_replace("/\[flash([^\[\]]+?)\](.*?)\[\/flash\]/is","",$tmp);
        $tmp=preg_replace("/\[audio([^\[\]]+?)\](.*?)\[\/audio\]/is","",$tmp);
        $tmp=preg_replace("/\[vphoto([^\[\]]+?)\](.*?)\[\/vphoto\]/is","",$tmp);
        $tmp=preg_replace("/\[marque([^\[\]]+?)\](.*?)\[\/marque\]/is","",$tmp);
        $tmp=preg_replace("/\[ppk_url\](http:\/\/.+?)\[\/ppk_url\]/is","",$tmp);
        $tmp=preg_replace("/\[url\](.*?)\[\/url\]/is","",$tmp);
        $tmp=preg_replace("/\[url=([^\[\]]*?)\](.*?)\[\/url\]/is","\\2",$tmp);
        $tmp=preg_replace("/\[email\](.*?)\[\/email\]/is","\\1",$tmp);
        $tmp=preg_replace("/\[email\]\[\/email\]/is","",$tmp);
        $tmp=ereg_replace("\[hr\]","",$tmp);
        return $tmp;
    }

    /**
     * 生成校验串的通用hash函数
     *
     * @param	mix		$seed1, 生成校验串的种子变量(支持数组,对象等类型), 可接受多个seed, 最多支持10个
     * @param	mix		$seed2, 生成校验串的种子变量(可选参数)
     *
     * @return	string		$hash
     */
    public static function commHash($seed)
    {
        $enckey = '!@#123QWe';
        $numargs = func_num_args();
        $arg_list = func_get_args();
        $numargs = $numargs > 10 ? 10 : $numargs;
        $ostr = $enckey;
        for ($i = 0; $i < $numargs; $i++) {
            $temp = print_r($arg_list[$i], true);
            $ostr .= $temp;
        }
        $encstr = md5($ostr);
        return $encstr;
    }


    /**
     * 转换比较容易产品xss的几个编码
     * '&' (ampersand) becomes '&amp;'
     * '"' (double quote) becomes '&quot;' when ENT_NOQUOTES is not set.
     * ''' (single quote) becomes '&#039;' only when ENT_QUOTES is set.
     * '<' (less than) becomes '&lt;'
     * '>' (greater than) becomes '&gt;'
     * @param string $str
     * @return string
     */
    public static function transXSSContent($str){
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }


    /**
     * 反转换比较容易产品xss的几个编码
     * '&' (ampersand) becomes '&amp;'
     * '"' (double quote) becomes '&quot;' when ENT_NOQUOTES is not set.
     * ''' (single quote) becomes '&#039;' only when ENT_QUOTES is set.
     * '<' (less than) becomes '&lt;'
     * '>' (greater than) becomes '&gt;'
     * @param string $str
     * @return string
     */
    public static function unTransXSSContent($str){
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        return $str;
    }
    /**
     * 二维数组排序
     * @param Array $multi_array 待排序数组
     * @param Array $sort_key  排序字段
     * @return string $sort 排序类型：SORT_ASC / SORT_DESC
     */
    public static function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC){
        if(is_array($multi_array) and !empty($multi_array)){
            foreach ($multi_array as $row_array){
                if(is_array($row_array)){
                    $key_array[] = $row_array[$sort_key];
                }else{
                    return -1;
                }
            }
            array_multisort($key_array,$sort,$multi_array);
        }

        return $multi_array;
    }
    /**
     * 生成分页html
     *
     * @param string $url url模板,  例如: page.html?page={page}
     * @param Number 当前页码
     * @param Number $pageCount 页数
     * $param Number neighborLength 显示相邻页码的个数
     */
    public static function getpageHTML($url, $currentPage, $pageCount, $neighborLength = 3){

        if($pageCount < 2 )
            return "";

        $start = $currentPage  - $neighborLength;
        $end = $currentPage + $neighborLength;

        if($start <= 4){
            $start = 2;
        }

        $start = $start > 1 ? $start : 2;

        $end = 2* $neighborLength - ($start < $currentPage? ($currentPage - $start ) : 0 ) + $currentPage;
        $end = $end < $pageCount ? $end : ( $pageCount - 1 );

        $str = "";
        //上一页
        $str.= $currentPage == 1 ? '<span class="page-start"><b>&lt;</b>上一页</span>' : '<a href="' . str_replace("{page}", $currentPage - 1, $url) . '" class="page-prev"><b>&lt;</b>上一页</a>';

        //第一页
        $str.= $currentPage == 1 ? "" : '<a href="' . str_replace("{page}", 1, $url) . '">1</a>';

        //左邻居
        if( $start != 2 )
            $str.= '<span class="page-break">...</span>';
        for($i = $start; $i < $currentPage; $i++){
            $str.=  '<a href="' . str_replace("{page}", $i, $url) . '">' . $i . '</a>';
        }

        //当前页
        $str.= '<span class="page-this">' . $currentPage . '</span>';

        //右邻居
        for($i = $currentPage + 1; $i < $end + 1; $i++){
            $str.=  '<a href="' . str_replace("{page}", $i, $url) . '">' . $i . '</a>';
        }

        if( $end != $pageCount - 1 )
            $str.= '<span class="page-break">...</span>';

        //最后一页
        $str.= $currentPage != $pageCount  ? '<a href="' . str_replace("{page}", $pageCount, $url) . '">' . $pageCount . '</a>' : '';

        //下一页
        $str.= $currentPage != $pageCount  ? '<a href="' .  str_replace("{page}", $currentPage + 1, $url) . '" class="page-next">下一页<b>&gt;</b></a>' : '<span class="page-end">下一页<b>&gt;</b></span>';

        //输入框跳转
        $str.= '<span class="page-skip"> 到第<input type="text" value="' . $currentPage . '" maxlength="3">页<button value="go" onclick="var a=parseInt($(this).parent().find(\'input[type=text]\').val(),10);a=(!!a&&a>0&&a<='. $pageCount .')?a:1;window.location.href=\''.str_replace("{page}", '\'+a+\'', $url).'\'" class="button">确定</button></span>';

        return $str;
    }

    public static function escapeTransStr($str_)
    {
        $search  = array(":"  , "&", "\n", "\r");
        $replace = array("%8A", "%8B", "%8C", "%8D");

        return str_replace($search, $replace, $str_);
    }

    public static function writeSynExceptionLog($reSyn_items_, $file_path_, $file_name_)
    {
        //check the file
        if (!file_exists("$file_path_"))
        {
            self::$errMsg = "no path : $file_path_";
            return;
        }
        else
        {
            $cols = "";
            $fp = fopen("$file_path_/$file_name_",'w');
            // serialize the exception log
            foreach($reSyn_items_ AS $wh_id => $reSyn_items)
            {
                foreach ($reSyn_items AS $name => $value)
                {
                    if (4000 < strlen($value))
                    {
                        $splits = explode(',', $value);
                        $value = "";
                        foreach ($splits AS $split)
                        {
                            if (4000 < strlen($value))
                            {
                                $cols .= $wh_id . " " . "$name" . " " . "$value" . "\n";
                                $value = "";
                            }

                            if ("" != $split)
                            {
                                $value .= $split . ',';
                            }
                        }
                    }
                    else if (strlen($value) == 0)
                    {
                        continue;
                    }

                    $cols .= $wh_id . " " . "$name" . " " . "$value" . "\n";
                }
            }

            fwrite($fp, $cols, strlen($cols));
            fclose($fp);
        }

        return true;
    }

    public static function readSynExceptionLog($file_path_, $file_name_)
    {
        //check the file
        if (!file_exists("$file_path_/$file_name_"))
        {
            self::$errMsg = "not exsit file : $file_path_/$file_name_";
            return false;
        }

        // open file
        if (!$fp = fopen("$file_path_/$file_name_",'r'))
        {
            self::$errMsg = "can't open file : $file_path_/$file_name_";
            return false;
        }

        $reSyn_items = array();
        // get file content
        while (!feof($fp))
        {
            $items[] = fgets($fp,4096);
        }

        // file is null
        if (false === $items[0])
        {
            return $reSyn_items;
        }

        foreach($items AS $item)
        {
            if (false === $item)
            {
                continue;
            }

            $content = explode(' ', $item);
            // every item must be three parts, "no name value"
            if (count($content) != 3)
            {
                continue;
            }

            $wh_id = $content[0];
            $name  = $content[1];
            $ids   = str_replace(array("\n","\r"),'',$content[2]);

            if (isset($reSyn_items[$wh_id][$name]))
            {
                $reSyn_items[$wh_id][$name] .= $ids;
            }
            else
            {
                $reSyn_items[$wh_id][$name] = $ids;
            }
        }
        fclose($fp);

        return $reSyn_items;
    }

    public static function getPayTypeInfo($payTypeId, $whId){
        global $_PAY_MODE;
        if(!isset($_PAY_MODE[$whId])) return false;

        if(!isset($_PAY_MODE[$whId][$payTypeId])) return false;
        return $_PAY_MODE[$whId][$payTypeId];
    }


    public static function getShipTypeInfo($shipTypeId, $whId){
        global $_LGT_MODE;
        if(!isset($_LGT_MODE[$whId])) return false;

        if(!isset($_LGT_MODE[$whId][$shipTypeId])) return false;
        return $_LGT_MODE[$whId][$shipTypeId];
    }

    public static function getSynTime($key)
    {
        $mysql = Config::getDB('icson_admin');
        if (!$mysql)
        {
            Logger::info(Config::$errMsg);
            return false;
        }

        $sql = "SELECT `key`, `value` FROM t_config WHERE `key` = '{$key}'";
        $result = $mysql->getRows($sql);
        if (false === $result)
        {
            Logger::err('get data from sql fails' . $mysql->errMsg);
            return false;
        }
        else if (empty($result) || '' === $result[0]['value'])
        {
            $sql = "insert into t_config (`key`, `value`) values ('{$key}', '0')";
            $result = $mysql->execSql($sql);
            $time = date("Y-m-d H:i:s", 0);
        }
        else
        {
            $time = $result[0]['value'];
        }

        return $time;
    }

    public static function updateSynTime($key, $timestamp)
    {
        $mysql = Config::getDB('icson_admin');
        if (!$mysql)
        {
            Logger::info(Config::$errMsg);
            return false;
        }

        $now = date("Y-m-d H:i:s", $timestamp);
        $sql = "UPDATE t_config SET `value` = '{$now}' WHERE `key` = '{$key}'";
        $result = $mysql->execSql($sql);
        if (false === $result)
        {
            return false;
        }

        return true;
    }

    public static function getDBTableIndex($uid)
    {
        $dbtab = self::getDBTables();
        $tabNum = $dbtab['tabnum'];
        $dbNum = $dbtab['dbnum'];
        return  array('db'=>intval($uid / $tabNum) % $dbNum, 'table'=>$uid % $tabNum);
    }

    public static function TTCStr2Hash($str)
    {
        return TTCStringHash($str, 1, -1);
    }

    public static function getUserDBTableIndex($hash)
    {
        $dbtab = self::getDBTables();
        $tabNum = $dbtab['tabnum'];
        $dbNum = $dbtab['dbnum'];
        return  array('table'=>intval($hash / $tabNum) % $dbNum, 'db'=>$hash % $tabNum);
    }

    /*
        获得分库分表的索引，默认是百库百表
    */
    public static function getDeployTableIndex($hash,$dbNum=100,$tabNum=100)
    {
        return array('table'=>intval($hash / $tabNum) % $dbNum, 'db'=>$hash % $tabNum);
    }

    public static function getCouponDBTableIndex($hash)
    {
        $dbtab = self::getDBTables();
        $tabNum = $dbtab['tabnum'];
        $dbNum = $dbtab['dbnum'];
        return  array('table'=>intval($hash / $tabNum) % $dbNum, 'db'=>$hash % $tabNum);
    }

    public static  function getDBTables()
    {
        return array('tabnum'=>100, 'dbnum'=>100);
    }

    public static function getDBNumAndTableNum($name)
    {
        global $_DB_TABLE_CFG;
        return array('tabnum'=>$_DB_TABLE_CFG[$name]['TABLE_NUM'], 'dbnum'=>$_DB_TABLE_CFG[$name]['DB_NUM']);
    }

    public static function getDBObj($name,$i=-1)
    {
        global $_DB_TABLE_CFG;
        global $_DB_SERVER_CFG;
        global $_DB_CFG;
        $ipKey = $_DB_TABLE_CFG[$name]['IP'];
        $db = $_DB_TABLE_CFG[$name]['DB'];
        $config = $_DB_SERVER_CFG['online'][$ipKey];

        if (-1 == $i) {
            $config['DB'] = $db;
            $cfgKey = $name;
        }
        else {
            $config['DB'] = $db."_".$i;
            $cfgKey = $name . '_' . $i;
        }

        $_DB_CFG[$cfgKey] = $config;
        $db = Config::getDB($cfgKey);
        if( $db === false )
        {
            return false;
        }
        return $db;
    }

    /**
     * 通过城市名查询基本信息
     * @param string $cityName
     * @return mixed array 查询成功; false 失败
     */
    public static function getLocInfoByCityName($cityName) {
        if (empty($cityName)) {
            return false;
        }
        global $_District, $_City, $_Province;

        $cityId = false;
        foreach ($_City as $idx => &$item) {
            if (false !== strpos($item['name'], $cityName)) {
                $cityId = $item['id'];
                break;
            }
        }

        return (false === $cityId) ? false : self::getLocInfo($cityId);
    }

    public static function getLocInfo($id){
        global $_District, $_City, $_Province;

        if(!isset($_District[$id])){
            if(!isset($_City[$id])){
                if(!isset($_Province[$id])){
                    return false;
                }

                // 是一个省的名字
                return array(
                    "province_id"	=> $id,
                    "province_name"	=> $_Province[$id],
                    "full_name"	=> $_Province[$id],
                );
            }

            $prov_id = $_City[$id]['province_id'];
            if(!isset($_Province[$prov_id])){
                return false;
            }

            return array(
                "province_id"	=> $prov_id,
                "province_name"	=> $_Province[$prov_id],
                "city_id"	=> $id,
                "city_name"	=> $_City[$id]['name'],
                "full_name"	=> ($_Province[$prov_id] == $_City[$id]['name'] ? "" : $_Province[$prov_id]) . $_City[$id]['name'],
            );
        }

        $addr = $_District[$id];
        if(!isset($_City[$addr['city_id']])){ // 市级无效
            return false;
        }

        $addr['city_name'] = $_City[$addr['city_id']]['name'];

        if(!isset($_Province[$addr['province_id']])){ // 省级无效
            return false;
        }

        $addr['province_name'] = $_Province[$addr['province_id']];
        $addr['full_name'] = ($addr['province_name'] == $addr['city_name'] ? "" : $addr['province_name']) . $addr['city_name'] . $addr['name'];
        return $addr;
    }

    /**
     * PHP 版本escape，CPS功能时引入
     * @param string $str
     */
    public static function escape($str) {
        preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r);
        $ar = $r[0];
        foreach($ar as $k=>$v) {
            if (ord($v[0]) < 128) {
                $ar[$k] = rawurlencode($v);
            }
            else {
                $ar[$k] = "%u" . bin2hex(mb_convert_encoding($v, 'UCS-2', 'GBK'));
            }
        }
        return join('', $ar);
    }

    /**
     *
     * @param $str
     * @param $start
     * @param $len
     */
    public static function msubstr($str, $start, $len) {
        $tmpstr = "";
        $strlen = $start + $len;
        for($i = 0; $i < $strlen; $i++) {
            if(ord(substr($str, $i, 1)) > 0xa0) {
                $tmpstr .= substr($str, $i, 2);
                $i++;
            } else
                $tmpstr .= substr($str, $i, 1);
        }
        return $tmpstr;
    }

    //计算一个字符串的hash整数值，取值返回为[0-5614657)
    public static function  string2IntHash($str)
    {
        $hash = 0;
        $n = strlen($str);
        for ($i = 0; $i <$n; $i++) {
            $hash ^= (ord($str[$i]) <<($i & 0x0f));
        }
        return $hash % 5614657;
    }


    public static function isRequestFromMobile(){
        if( !isset($_SERVER['HTTP_USER_AGENT']))	return false;

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $AGENTSCONFIG = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
        foreach ($AGENTSCONFIG as $device) {
            if (stristr($userAgent, $device)) {
                return true;
            }
        }

        return false;
    }

    public static function isRequestFromAndroid(){
        if( !isset($_SERVER['HTTP_USER_AGENT']))	return false;

        return stristr($_SERVER['HTTP_USER_AGENT'], "android") ? true : false;
    }

    public static function makePageIdFromURL($pageType = PAGE_TYPE_COMMON){
        $urlWithoutQueryStr = "http://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $qSplit = strpos($urlWithoutQueryStr, "?");
        if (false !== $qSplit){
            $urlWithoutQueryStr = substr($urlWithoutQueryStr, 0, $qSplit);
        }

        return ITag::getPageId($urlWithoutQueryStr, $pageType);
    }
    /**
     * 设置数据统计的pageid和pagelevel
     * @param int $yPageId
     * @param int $yPageLevel
     */
    public static function setCurrentPageId($yPageLevel, $yPageId = false, $pageType = PAGE_TYPE_COMMON){
        self::$yPageLevel = $yPageLevel;

        if ($yPageId === false){
            $yPageId = self::makePageIdFromURL($pageType);
        }
        self::$yPageId = $yPageId;
    }

    /**
     * 获取数据统计的pageid和pagelevel
     * @return array
     */
    public static function getCurrentPageId(){
        return array(
            'yPageId'	=> self::$yPageId,
            'yPageLevel'	=> self::$yPageLevel
        );
    }

    /**
     * 字符串截取
     * @param string $str 原始字符串
     * @param int    $len 截取长度（中文/全角符号默认为 2 个单位，英文/数字为 1。
     *                    例如：长度 12 表示 6 个中文或全角字符或 12 个英文或数字）
     * @param bool   $dot 是否加点（若字符串超过 $len 长度，则后面加 "..."）
     * @return string
     */
    public static function g_substr($str, $len = 12, $dot = true) {
        $i = 0;
        $l = 0;
        $c = 0;
        $a = array();
        while ($l < $len) {
            $t = substr($str, $i, 1);
            if (ord($t) >= 224) {
                $c = 3;
                $t = substr($str, $i, $c);
                $l += 2;
            } elseif (ord($t) >= 192) {
                $c = 2;
                $t = substr($str, $i, $c);
                $l += 2;
            } else {
                $c = 1;
                $l++;
            }
            // $t = substr($str, $i, $c);
            $i += $c;
            if ($l > $len) break;
            $a[] = $t;
        }
        $re = implode('', $a);
        if (substr($str, $i, 1) !== false) {
            array_pop($a);
            ($c == 1) and array_pop($a);
            $re = implode('', $a);
            $dot and $re .= '...';
        }
        return $re;
    }

    /**
     * 字符串截取,GBK,UTF-8
     * @param unknown_type $str 原始字符串
     * @param unknown_type $start 从哪里开始
     * @param unknown_type $len 截取长度
     */
    public static function substring($str, $start, $len) {
        $tmpstr = "";
        $strlen = $start + $len;
        for($i = 0; $i < $strlen; $i++) {
            if(ord(substr($str, $i, 1)) > 0xa0) {
                $tmpstr .= substr($str, $i, 2);
                $i++;
            } else
                $tmpstr .= substr($str, $i, 1);
        }
        return $tmpstr;
    }
}

//End of script
