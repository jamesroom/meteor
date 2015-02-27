<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8,minimum-scale=0.8,user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="web.css" rel="stylesheet" rev="stylesheet" />
<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="./js/index.js"></script>
    <script type="text/javascript" src="./js/star.js"></script>
<title>个人主页</title>
</head>
<body>
<?php
error_reporting(null);
define("WEB_ROOT",dirname(__FILE__));
require_once(WEB_ROOT.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'user.php');
require_once('login.php');
UserInfo::checkLoginOrRedirect();

if(isset($_REQUEST['butt'])==2){
    sure_wish();
}
if(isset($_REQUEST['uid'])==2){
    enjoy_part();

}
//?uid='.$_COOKIE["uid"].'&skey='.$_REQUEST["skey"].'


?>
<div class="container">
    <div>
        <canvas id="canvas"  style="position: absolute;top:0;z-index:1;"></canvas>
        <canvas id="canvas1" style="position: absolute;top:0;z-index:1;"></canvas>
    </div>
    <div class="header">
        <a  href="wishpool.php" class="mywish_button"></a>
        <?php
        echo '<a href="web.php?skey='.$_COOKIE['skey'].'" class="geren"></a>';
        ?>

    </div>
  <div class="content">
      <span class="back"><a href="wish.php">首页 </a> > 个人主页</span>
    <div class="nav_tab">
      <ul>
          <?php
               if($_REQUEST["skey"]==$_COOKIE["skey"]){
                   echo '<li><a href="javascript:void(0);" class="on">我的愿望</a></li>';
                   echo '<li><a href="mylove.php?skey='.$_REQUEST["skey"].'">我的爱心</a></li>';
               }else{
                   echo '<li><a href="javascript:void(0);" class="on">Ta的愿望</a></li>';
                   echo '<li><a href="mylove.php?skey='.$_REQUEST["skey"].'">Ta的爱心</a></li>';
               }
          ?>
      </ul>
    </div>
    <div class="infor_cont">
      <ul>
        <li class="infor_head">
          <div class="list01">&nbsp;</div>
          <div class="list02">愿望(<?php echo count(page_user_list()); ?>)</div>
          <div class="list03">帮助实现的人</div>
          <div class="list04"></div>
        </li>
        <?php
            foreach(page_user_list() as $row){
                echo '<li>';
                echo '<div class="list01">';
                //实现中
                if($row['status']==1 && $row['end_time'] !=0 && time()<($row['end_time'])){
                    echo  '<span class="status02"></span>';
                }else if($row['status']==1 && $row['end_time'] ==0){
                    echo  '<span class="status02"></span>';
                }
                //已实现
                else if($row['status']==2){
                    echo  '<span class="status01"></span>';
                }
                //已过期
                else if(time()>($row['end_time']) && $row['status']==0 ){
                    echo  '<span class="status04"></span>';
                }
                //待实现
                else if($row['status']==0 ){
                    echo  '<span class="status03"></span>';
                }
                //未实现
                else if($row['status']==1 && time()>($row['end_time'])){
                    echo  '<span class="status05"></span>';
                }else{
                    echo  '<span class="status01"></span>';
                }
                echo   '</div>';
                echo '<div class="list02">&nbsp;'.$row['content'].'</div>';
                echo '<div class="list03"><a href="web.php?skey='.$row['accept_key'].'">&nbsp;'.$row['accect_name'].'</a></div>';
                echo '<div class="list04">
                       <form action="" method="post">
                            <a href="?uid='.$_COOKIE["uid"].'&skey='.$_REQUEST["skey"].'" class="enjoy_button">欣赏Ta</a>
                            <input type="text" name="id" value='.$row['id'].'>';
                if($row['status']!=2 && $_REQUEST["skey"]==$_COOKIE["skey"]){
                    echo  '<button type="submit" value="2" name="butt" class="enjoy_button">愿望完成</button>';
                }
              echo  '</form>
                       </div>';
               echo '</li>';
            }
         ?>
      </ul>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
</script>
</html>
