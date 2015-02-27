<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kathleen
 * Date: 13-9-3
 * Time: 上午10:08
 * To change this template use File | Settings | File Templates.
 */

error_reporting(E_ALL);
$mod_name = empty($_REQUEST["mod"])?"home":trim($_REQUEST["mod"]);
define("WEB_ROOT",dirname(__FILE__));
require_once(WEB_ROOT.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$mod_name.'.php');
$act = empty($_REQUEST["act"])?"list":trim($_REQUEST["act"]);
$fun_name = 'page_'.$mod_name.'_'.$act;
$ret =$fun_name();
if(is_array($ret)){
   echo json_encode($ret);
}