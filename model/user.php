<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kathleen
 * Date: 13-9-3
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 */
require_once("db.php");
function page_user_list(){
    $condition = isset($_REQUEST["skey"])? $_REQUEST["skey"]:false;
    $condition = $condition ? "where skey= '$condition'" :'';
    $sql = "SELECT * FROM wish $condition order by insert_row_time desc";
    $db = new db();
    $data= $db->get_Rows($sql);
   return $data;
}
function page_user_love(){
    $condition = isset($_REQUEST["skey"])? $_REQUEST["skey"]:false;
    $sql = "SELECT * FROM `wish` WHERE accept_key='$condition' and status in (1,2) order by update_row_time";
    $db = new db();
    //var_dump($sql);
    //SELECT * FROM `wish` WHERE accept_key='ac93561c62c0dd791ce3e0568b716982' and status in (1,2) order by update_row_time
    $data= $db->get_Rows($sql);
    return $data;
}
function sure_wish(){
    $sql="update wish set status=2 where id=".$_REQUEST['id'];
    $db = new db();
    $db->get_Rows($sql);

}
function enjoy_part(){
    $url='http://home.dev.anjuke.com/ajax/appreciate/';
    $data = array(
        "hash"=>'05b3106b0a7aa2af2df7c39d4339d616',
        "send_uid"=>$_COOKIE['uid'],
        "receive_uid"=>$_REQUEST['uid']
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $info =json_decode(curl_exec($ch));
    //{"status":"ok","data":{"photo":"http:\/\/home.dev.anjuke.com\/headpic\/default_new.jpg","person":"\u5f20\u7d20\u6c99","reply":"\u6b23\u8d4f\u4e86\u5f20\u7d20\u6c99\u4e00\u4e0b","time":"2013-09-06 22:49","url":"http:\/\/home.dev.anjuke.com\/info\/?uid=6116"}}
    if($info){
        echo '<script type="text/javascript">
        alert("欣赏成功！");
//        $(".enjoy_button").each(function(i,v){
//            $(v).bind("click",function(){
//                $(v).html("欣赏成功！");
//            });
//        });

        </script>';
    }

}
