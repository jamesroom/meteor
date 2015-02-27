<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8,minimum-scale=0.8,user-scalable=no">
<link type="text/css" href="index.css" rel="stylesheet" rev="stylesheet" />
<script type="text/javascript" src="./js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="./js/index.js"></script>
    <script type="text/javascript" src="./js/star.js"></script>
    <title>心语心愿</title>

</head>
<body>
<?php
    error_reporting(null);
    define("WEB_ROOT",dirname(__FILE__));
    require_once(WEB_ROOT.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'home.php');
    require_once('login.php');
    $i=1;$j=1;
    if(isset($_REQUEST['submit'])==1){
        page_msg_add();
    }

    /*五条心愿*/
    $json1=json_data();

?>

<div class="container">
<div>
    <canvas id="canvas"   style="position: absolute;top:0;z-index:1;"></canvas>
    <canvas id="canvas1"  style="position: absolute;top:0;z-index:1;"></canvas>
</div>
<div class="head">
    <a  href="wishpool.php" class="mywish_button"></a>
    <?php
        echo '<a href="web.php?skey='.$_COOKIE['skey'].'" class="geren"></a>';
    ?>

</div>
<div class="content">
    <div class="left">
        <ul class="infor_cont">
            <li class="nav">
                <h2 class="l">心愿单</h2>
                <div>
                    <?php
                        if(isset($_REQUEST['type'])){
                            switch($_REQUEST['type']){
                                case 2:
                                    echo '<a href="wish.php"><span class="all"></span>全部</a>';
                                    echo '<a class="on" href="?type=2"><span class="status01"></span>已实现</a>';
                                    echo '<a href="?type=1"><span class="status02"></span>实现中</a>';
                                    echo '<a href="?type=0"><span class="status03"></span>待实现</a>';
                                    echo '<a href="?type=3"><span class="status04"></span>已过期</a>';
                                    echo '<a href="?type=4"><span class="status05"></span>未实现</a>';
                                    break;
                                case 1:
                                    echo '<a href="wish.php"><span class="all"></span>全部</a>';
                                    echo '<a href="?type=2"><span class="status01"></span>已实现</a>';
                                    echo '<a class="on" href="?type=1"><span class="status02"></span>实现中</a>';
                                    echo '<a href="?type=0"><span class="status03"></span>待实现</a>';
                                    echo '<a href="?type=3"><span class="status04"></span>已过期</a>';
                                    echo '<a href="?type=4"><span class="status05"></span>未实现</a>';
                                    break;
                                case 0:
                                    echo '<a href="wish.php"><span class="all"></span>全部</a>';
                                    echo '<a href="?type=2"><span class="status01"></span>已实现</a>';
                                    echo '<a href="?type=1"><span class="status02"></span>实现中</a>';
                                    echo '<a class="on" href="?type=0"><span class="status03"></span>待实现</a>';
                                    echo '<a href="?type=3"><span class="status04"></span>已过期</a>';
                                    echo '<a href="?type=4"><span class="status05"></span>未实现</a>';
                                    break;
                                case 3:
                                    echo '<a href="wish.php"><span class="all"></span>全部</a>';
                                    echo '<a href="?type=2"><span class="status01"></span>已实现</a>';
                                    echo '<a href="?type=1"><span class="status02"></span>实现中</a>';
                                    echo '<a href="?type=0"><span class="status03"></span>待实现</a>';
                                    echo '<a class="on" href="?type=3"><span class="status04"></span>已过期</a>';
                                    echo '<a href="?type=4"><span class="status05"></span>未实现</a>';
                                    break;
                                case 4:
                                    echo '<a href="wish.php"><span class="all"></span>全部</a>';
                                    echo '<a href="?type=2"><span class="status01"></span>已实现</a>';
                                    echo '<a href="?type=1"><span class="status02"></span>实现中</a>';
                                    echo '<a href="?type=0"><span class="status03"></span>待实现</a>';
                                    echo '<a href="?type=3"><span class="status04"></span>已过期</a>';
                                    echo '<a class="on" href="?type=4"><span class="status05"></span>未实现</a>';
                                    break;
                                default:

                                    break;
                            }
                        }else{
                            echo '<a class="on" href="wish.php"><span class="all"></span>全部</a>';
                            echo '<a href="?type=2"><span class="status01"></span>已实现</a>';
                            echo '<a href="?type=1"><span class="status02"></span>实现中</a>';
                            echo '<a href="?type=0"><span class="status03"></span>待实现</a>';
                            echo '<a href="?type=3"><span class="status04"></span>已过期</a>';
                            echo '<a href="?type=4"><span class="status05"></span>未实现</a>';
                        }

                    ?>
                </div>
            </li>
            <?php

                foreach(page_home_list() as $row){
                    //var_dump($row);
                    echo '<li>';
                    echo '<span class="time"><em></em>';
                    if(date('Y-m-d')==date('Y-m-d',$row['insert_row_time'])){
                        echo '<i>今天'.date('H:i',$row['insert_row_time']).'</i>';
                    }else if(date('Y-m-d', mktime('0','0','0',date('m'),date('d')-1,date('Y')))==date('Y-m-d',$row['insert_row_time'])){
                        echo '<i>昨天'.date('H:i',$row['insert_row_time']).'</i>';
                    }else{
                        echo '<i>昨天'.date('Y-m-d H:i',$row['insert_row_time']).'</i>';
                    }
                    echo '</span>';
                    if($row['status']==1 && ($row['end_time']==0 || time()<$row['end_time'])){
                        echo '<span class="title"><em class="status02"></em>
                        <a href="web.php?skey='.$row['accept_key'].'">'.$row['accect_name'].'</a>
                        承诺帮&nbsp;<a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a>
                        实现愿望<i>"'.$row['content'].'"</i>
                        </span>';
                        if(!empty($row['comment'])){
                            echo '<div class="comment">承诺：'.$row['comment'].'</div>';
                        }

                    }else if($row['status']==2){
                        echo '<span class="title"><em class="status01"></em>
                        <a href="web.php?skey='.$row['accept_key'].'">'.$row['accect_name'].'</a>
                        实现了&nbsp;<a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a>
                        的愿望<i>"'.$row['content'].'"</i>
                        </span>';
                        if(!empty($row['comment'])){
                            echo '<div class="comment">承诺：'.$row['comment'].'</div>';
                        }

                    }else if( $row['status']==0 && ($row['end_time'] != 0 && time() > $row['end_time']) ){
                        echo '<span class="title"><em class="status04"></em>
                        <a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a>
                        的愿望<i>"'.$row['content'].'"</i>已到期
                        </span>';
                    }else if($row['status']==1){
                        echo '<span class="title"><em class="status05"></em>
                        <a href="web.php?skey='.$row['accept_key'].'">'.$row['accect_name'].'</a>
                        没有帮&nbsp;<a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a>
                        实现愿望<i>"'.$row['content'].'"</i>
                        </span>';
                        if(!empty($row['comment'])){
                            echo '<div class="comment">承诺：'.$row['comment'].'</div>';
                        }
                    }else{
                        echo '<span class="title"><em class="status03"></em>
                     <a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a>
                    许了个愿望<i>"'.$row['content'].'"</i>
                    </span>';
                        if($row['end_time']>0 && $row['end_time']<time()){
                            echo '<a href="javascript:void(0);" class="achieve_button">帮Ta实现</a>';
                        }else{
                            echo '<a href="javascript:void(0);" class="achieve_button">帮Ta实现</a>';
                        }

                    }
                    echo '<form class="edit_cont" action="" method="post">
                    <div>
                    <span><em class=""></em></span>
                    <textarea name="content" row="4" cols="50" maxlength="140"></textarea>
                    <input type="text" name="wish_id" value="'.$row['id'].'"/></div>
                    <p><button type="submit" name="submit" value="1" class="save_button">承诺实现</button><em class="tip">对不起你已经输入140个字了</em></p>
                    </form>';
                    echo '</li>';
                }
            ?>
            <li class="page_button">
                <?php
                   $pa=!empty($_GET['page'])?$_GET['page']:'1';
                   foreach(page_count() as $row){
                       $count=ceil($row['total']/10);
                   }

                   if($pa==1){
                       if(isset($_REQUEST['type'])!='0'){
                           echo '<a href="wish.php?page=1&type='.$_REQUEST['type'].'">第一页</a>';
                       }else{
                           echo '<a href="wish.php?page=1">第一页</a>';
                       }

                   }else{
                       if(isset($_REQUEST['type'])!='0'){
                           echo '<a href="wish.php?page='.($pa-1).'&type='.$_REQUEST['type'].'">上一页</a>';
                       }else{
                           echo '<a href="wish.php?page='.($pa-1).'">上一页</a>';
                       }

                   }

                   echo (!empty($_GET['page'])?$_GET['page']:'1').'/';
                   echo $count?$count.'页':'1'.'页';
                   if($pa< $count){
                       if(isset($_REQUEST['type'])!='0'){
                           echo '<a href="wish.php?page='.($pa+1).'&type='.$_REQUEST['type'].'">下一页</a>';
                       }else{
                           echo '<a href="wish.php?page='.($pa+1).'">下一页</a>';
                       }

                   }else{
                       if(isset($_REQUEST['type'])!='0'){
                           echo '<a href="wish.php?page='.($pa).'&type='.$_REQUEST['type'].'">最后一页</a>';
                       }else{
                           echo '<a href="wish.php?page='.($pa).'">最后一页</a>';
                       }

                   }
                ?>

            </li>
            </ul>
    </div>
    <div class="right">
        <div class="bar_cont">
            <ul>
                <li class="bar_title"><label class="angel">当月天使榜</label></li>
                <?php
                //print_r(get_angle_rank());
                    foreach(get_angle_rank() as $row){
                        if($row['accect_name']){
                            $kk=$i++;
                            echo '<li>';
                            echo '<div><em class="num0'.$kk.'">'.$kk.'</em><a href="mylove.php?skey='.$row['accept_key'].'">'.$row['accect_name'].'</a></div>';
                            echo '<span>实现了'.$row['total'].'次</span>';
                            echo '</li>';
                        }
                    }
                ?>
            </ul>
        </div>
        <div class="bar_cont">
            <ul>
                <li  class="bar_title"><label class="wish">当月许愿榜</label></li>
                <?php
                foreach(get_love_rank() as $row){
                    $ii=$j++;
                    echo '<li>';
                    echo '<div><em class="num0'.$ii.'">'.$ii.'</em><a href="web.php?skey='.$row['skey'].'">'.$row['name'].'</a></div>';
                    echo '<span>许愿了'.$row['total'].'次</span>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</div>
</div>
</body>
</html>