<?php
require_once "../includes/Func.php";
require_once "../includes/adminServer.class.php";
require_once "../includes/stationServer.class.php";
require_once dirname(__DIR__)."/../includes/CommonFun.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(2);
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$title."</span>";
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$content."</span>";
    exit();
}

//获取当前id号
$id = getID();
$admin = AdminServer::getAdmin($id);
$belongid = $admin->getBelongid();
$station = StationServer::getStationInfo($belongid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>chart</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/chart_style.css">
    <link type="text/css" rel="stylesheet" href="../css/magic-input.css">
</head>
<body>
<div class="selection">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label>站点选择</label>
            <select class="form-control" name="siteno">
                <?php
                foreach ($station as $s){
                    $no = $s->no;
                    $name = $s->name;
                    echo "<option value='$no'>$name"." (".$no.")"."</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>起始时间</label>
            <input type="date" class="form-control" name="startDate">
        </div>
        <div class="form-group">
            <label>结束时间</label>
            <input type="date" class="form-control" name="endDate">
        </div><br>
        <div class="form-group">
            <label>查询方式</label>
            <select class="form-control" name="queryType">
                <option value="h">按时查询</option>
                <option value="d">按日查询</option>
                <option value="m">按月查询</option>
                <option value="y">按年查询</option>
            </select>
        </div>
        <div class="form-group-lg">
            <label>检测指标</label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="tm" class="mgc mgc-success"> 水温
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="ph" class="mgc mgc-success"> pH
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="ox" class="mgc mgc-success"> 溶解氧
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="el" class="mgc mgc-success"> 电导率
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="nt" class="mgc mgc-success"> 浊度
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="cl" class="mgc mgc-success"> 氯含量
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="po" class="mgc mgc-success"> 总磷
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="nh" class="mgc mgc-success"> 氨氮
            </label>
            <label class="checkbox-inline">
                <input type="radio" name="option" value="ca" class="mgc mgc-success"> 碳含量
            </label>
        </div>
    </form>
</div>
<div class="content">
    <div class="cbox">
        <canvas id="myChart"></canvas>
        <div class="chartInfo">
            <p></p>
            <p></p>
            <p></p>
        </div>
    </div>
</div>
</body>
<script src="../js/jquery.js"></script>
<script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../js/Chart.js"></script>
<script src="../js/getDate.func.js"></script>
<script src="../js/dataChart.js"></script>
</html>