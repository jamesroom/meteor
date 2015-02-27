<?php
error_reporting(E_ALL);
setcookie("host",'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],time()+3600,"/","dev.aifang.com");
class UserInfo{
    const client_id = 'WishLun01';
    const client_secret = '24490ade';
    const oauth_url = 'https://auth.corp.anjuke.com';
    private static  $info = null;
    public static function getUserInfo(){
        if(UserInfo::$info){
           return UserInfo::$info;
        }
        $info = UserInfo::login_with_oauthcurl(UserInfo::client_id, UserInfo::client_secret,UserInfo::oauth_url);
        if(!$info){
            return array("code"=>1,"errMsg"=>"登录失败，请重试，若多次尝试失败请联系管理员");
        }

       $info = json_decode($info,true);
       $info = UserInfo::get_info_from_ldap($info['access_token'], UserInfo::oauth_url);
        if(!$info){
            echo "注册失败，读不到此人域信息，请重试，若多次尝试失败请联系管理员";exit;
        }
        $arr_info = json_decode($info);
        var_dump($arr_info);
        setcookie("name",$arr_info->name,time()+3600,"/","dev.aifang.com");
        $strMd5 =md5(($arr_info->name.$arr_info->code."dzv233,123l"));
        setcookie("skey",$strMd5,time()+3600,"/","dev.aifang.com");
        setcookie("uid",$arr_info->user_id,time()+3600,"/","dev.aifang.com");
        header("Location:".$_COOKIE["host"]);
        return ;
    }
    public static function checkLoginOrRedirect(){
        if(empty( $_COOKIE['skey']) || empty( $_COOKIE['name'])){
            self::getUserInfo();
            exit;
        }
    }
    private static  function login_with_oauthcurl($client_id, $client_secret, $oauth_url){
        if(isset($_REQUEST['access_token']) && $_REQUEST['access_token']){
            /*3、用AccessToken,获取info*/
            $access_token = $_REQUEST['access_token'];

            $data = array(
                "oauth_token"=>$access_token,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $oauth_url."/resource.php");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $info = curl_exec($ch);
            if($info) return $info;
            else return false;
            exit();
        }
        header("Content-type: text/html; charset=utf-8");
        /*1、获取临时令牌RequestToken*/
        $array = array(
            "client_id"=>$client_id,
            "response_type"=>"code",/*默认*/
            "curl"=>true,/*使用curl还是使用redirect*/
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oauth_url."/authorize.php");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $info = json_decode(curl_exec($ch),true);

        var_dump($info);

        if($info['code']){
            /*2、用临时令牌，申请访问令牌,回传地址就是申请时的redirect_uri地址*/
            $data = array(
                "client_id"=>$client_id,
                "client_secret"=>$client_secret,
                "grant_type"=>'authorization_code',/*默认*/
                "code"=>$info['code'],/*临时令牌*/
                "custom"=>"refer://fjioa.daij.dao.com/project/list?fa=0&fda=post",/*可选，用户自定义字段，可用于传跳转地址*/
            );
            header("HTTP/1.1 302 Found");
            header("Location: " . $oauth_url.'/token.php?'.http_build_query($data));
            exit();
        }
    }

    public static  function get_info_from_ldap($access_token, $oauth_url){
        $data = array(
            "oauth_token"=>$access_token,
            "getinfo"=>true,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oauth_url."/resource.php");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $info = curl_exec($ch);
        if($info) return $info;
        else return false;
    }
}
UserInfo::checkLoginOrRedirect();

