<!DOCTYPE html >
<head>
<meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=0.8,minimum-scale=0.8,user-scalable=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link type="text/css" href="web.css" rel="stylesheet" rev="stylesheet" />
    <script type="text/javascript" src="./js/jquery-1.8.3.min.js"></script>
    <title>个人主页</title>
    <script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="./js/index.js"></script>
    <script type="text/javascript" src="./js/star.js"></script>

</head>
<body>
<?php
    error_reporting(null);
    define("WEB_ROOT",dirname(__FILE__));
    require_once(WEB_ROOT.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'home.php');
    if(isset($_REQUEST['submit'])==2){
        page_home_add();
    }
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
            <span class="back"><a href="wish.php">首页 </a> > 许愿池</span>
            <h2 class="wish_title">许愿池</h2>
            <form action="" method="post">
                    <textarea name="content" row="4" cols="50" maxlength="140" id="wishId" class="edit_text"></textarea>
                    <label class="lb">愿望期限<i>(选填)</i>：<input type="date" name="end_time"></label>
                    <p class="wish_bot"><button type="submit" name="submit" class="wish_button" id="wishBut" value="2"></button><em class="tip" id="error">对不起你已经输入140个字了</em></p>

            </form>

        </div>
    </div>
</body>
</html>