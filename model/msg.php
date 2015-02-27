<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kathleen
 * Date: 13-9-3
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 */
require_once("db.php");
function page_msg_list(){
    echo "this is msg list";
}
function page_msg_add(){
    $_REQUEST = array(
        "wish_id" =>10,
        "content" => '我已　经完成了这个任务',
        "note_name" => $_COOKIE["name"],
        "insert_row_time" => time(),
        "wish_content" => "大大大大大大大大大大大磊"
    );
    $data = array(
        "wish_id" =>$_REQUEST["wish_id"]+0,
        "content" => $_REQUEST["content"],
        "note_name" => $_COOKIE["name"],
        "insert_row_time" => time(),
        "wish_content" => $_REQUEST["wish_content"]
    );
    $db = new db();
    $db->insert_msg($data);
}