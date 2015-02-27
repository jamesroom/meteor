<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kathleen
 * Date: 13-9-3
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 */
require_once("db.php");
function page_home_list(){
    $pageSize = 10;
    $start =!empty($_REQUEST["page"])?($_REQUEST["page"]-1)*$pageSize:0;
    if(isset($_REQUEST["type"])!=''){
        $condition =$_REQUEST["type"];
    }else{
        $condition='';
    }
    // type = 0待实现，１，实现中，２，已实现　，３，已过期，４，未实现
    if($condition == '0' || $condition == '1' || $condition == '2'){
        $time = time();
        $condition = "where status = $condition and (end_time = 0 or  $time< end_time) ";
        //$condition = "where status = $condition";
    }else if($condition == '3' ||$condition == '4'){
        $time = time();
        $condition= $condition==3 ?"where status = 0 and end_time>0 and end_time < $time ":"where status = 1 and end_time != 0 and ($time > end_time)";
    }
    $sort_type = !$condition? "update_row_time" :"insert_row_time";
    $sql = "SELECT * FROM wish $condition order by $sort_type desc limit $start,$pageSize";
    //print_r($sql);
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}
//添加愿忘　
function page_home_add(){
    $data = array(
        "content" =>  htmlspecialchars($_REQUEST["content"], ENT_QUOTES),
        "insert_row_time" => time(),
        "update_row_time" => time(),
        "name" =>$_COOKIE["name"],
        "skey" => $_COOKIE["skey"],
        "status" => 0,
        "end_time" => isset($_REQUEST["end_time"])?strtotime($_REQUEST["end_time"]):''
    );
    $db = new db();
    $db->insert_wish($data);
    header("Location:wish.php");
}

//添加评论  更改方法 todo
function page_msg_add(){
    $data = array(
        "accect_name" => $_COOKIE["name"],
        "comment" => $_REQUEST['content'],
        "update_row_time" => time(),
        "accept_key" => $_COOKIE["skey"],
        "status"=>1
    );
    $con=array(
        "id" =>$_REQUEST['wish_id']
    );
    $db = new db();
    $db->update_wish($data,$con);
}
/*天使榜*/
function get_angle_rank(){
    $sql = "SELECT * , COUNT(`accept_key`) AS total FROM  `wish` GROUP BY  `accept_key` ORDER BY COUNT(`accept_key`) DESC LIMIT 0 , 7";
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}
/*许愿榜*/
function get_love_rank(){
    $sql = "SELECT * , COUNT(`skey`) AS total FROM  `wish` GROUP BY  `skey` ORDER BY COUNT(`skey`) DESC LIMIT 0 , 7";
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}
//已实现的望　
function page_home_over(){
    $sql = "SELECT * FROM wish order by love_count desc limit 0,7";
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}

/*总条数*/
function page_count(){
    if(isset($_REQUEST["type"])!=''){
        $condition =$_REQUEST["type"];
    }else{
        $condition='';
    }
    // type = 0待实现，１，实现中，２，已实现　，３，已过期，４，未实现
    if($condition == '0' || $condition == '1' || $condition == '2'){
        $condition = "where status = $condition";
    }else if($condition == '3' ||$condition == '4'){
        $time = time();
        $condition= $condition==3 ?"where status = 0 and end_time is not null and end_time < $time ":"where status = 1 and end_time is not null and end_time < $time";
    }
    $sql = "SELECT count(*) as total FROM wish $condition";
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}

/*前五条数据*/
function json_data(){
    $sql='select * from wish order by update_row_time desc limit 0,5';
    $db = new db();
    $data= $db->get_Rows($sql);
    return $data;
}