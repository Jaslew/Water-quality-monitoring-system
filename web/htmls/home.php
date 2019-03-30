<?php
require_once "../includes/Func.php";
require_once "../includes/adminServer.class.php";
require_once dirname(__DIR__)."/../includes/CommonFun.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(4);
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$title."</span>";
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$content."</span>";
    exit();
}

$id = getID();
$admin = AdminServer::getAdmin($id);

//站点在线信息
$noList = getStationState($admin->getBelongid());
$online = 0;
if($noList){
    foreach ($noList as $s => $v){
        if($v == 1)
            $online++;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>home</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/home_style.css">
    <link type="text/css" rel="stylesheet" href="../css/animate.min.css">
    <link type="text/css" rel="stylesheet" href="../css/font-awesome.css">
</head>
<body>
    <!---------登陆提示----------->
    <div id="loginInfo" class="alert alert-success" role="alert">
        <button class="close"  data-dismiss="alert" type="button" >&times;</button>
        <p><?php echo "您上次登录时间是 ".date("Y年n月j日 H:i:s",AdminServer::setTime($id));?></p>
    </div>

    <div id="dayShow">
        <div class="date">
            <div>
                <p></p>
                <p></p>
            </div>
            <div>
               <p></p>
            </div>
        </div>

        <div class="weather">
            <div data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-trigger="hover"
                 data-content="">
                <span>今日</span>
                <p>
                    <img src="" height="55">
                    <span></span>
                </p>
                <span></span>
            </div>
            <div data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="hover"
                 data-content="">
                <span></span>
                <img src="" height="50">
                <span></span>
            </div>
            <div data-container="body" data-toggle="popover" data-placement="bottom" data-html="true" data-trigger="hover"
                 data-content="">
                <span></span>
                <img src="" height="50">
                <span></span>
            </div>
            <div data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-trigger="hover"
                 data-content="">
                <span></span>
                <img src="" height="50">
                <span></span>
            </div>
        </div>
    </div>

    <div id="commonShow">
        <div class="cbox">
            <div>
                <i class="fa fa-cubes fa-4x"></i>
            </div>
            <div>
                <p><?php echo count($noList)?></p>
                <p>所有站点</p>
            </div>
        </div>
        <div class="cbox">
            <div>
                <i class="fa fa-check-circle-o fa-5x"></i>
            </div>
            <div>
                <p><?php echo $online?></p>
                <p>在线站点</p>
            </div>
        </div>
        <div class="cbox">
            <div>
                <i class="fa fa-warning fa-4x"></i>
            </div>
            <div>
                <p>0</p>
                <p>未达标站点</p>
            </div>
        </div>
    </div>

    <div id="notice">
        <div class="n-header">
            <span class="hleft"> <i class="fa fa-chevron-left"></i> </span>
            <span class="hcenter"> <span style="font-size: 15px;"> 系统通知</span></span>
            <span class="hright"> <i class="fa fa-chevron-right"></i> </span>
        </div>
        <div class="n-content">
            <ul>

            </ul>
        </div>
    </div>
<!--    弹出窗   -->
    <div class="modal fade" data-backdrop="static" id="detail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center">
                    <h5>系统通知</h5>
                </div>
                <div class="modal-body">
                    <div class="notice-text"></div>
                    <div class="notice-time"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal">删除</button>
                    <button class="btn btn-primary" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="../js/jquery.js"></script>
<script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../js/getDate.func.js"></script>
<script src="../js/Chart.js"></script>
<script src="../js/weatherGet.js"></script>
<script src="../js/homeServer.js"></script>
</html>