<?php
require_once "../includes/Func.php";
require_once "../includes/adminServer.class.php";
require_once "../includes/stationServer.class.php";
require_once dirname(__DIR__)."/../includes/CommonFun.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(3);
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$title."</span>";
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$content."</span>";
    exit();
}

//获取当前id号
$id = getID();
$adminServer = new AdminServer();
$stationServer = new StationServer();
$admin = $adminServer::getAdmin($id);
$belongid = $admin->getBelongid();
$station = $stationServer::getStationInfo($belongid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>data</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/data_style.css">
    <link type="text/css" rel="stylesheet" href="../css/magic-input.css">
    <link type="text/css" rel="stylesheet" href="../css/animate.min.css">
</head>
<?php echo "<script>var belongid = '$belongid'</script>"?>
<body>
    <div id="dataShow">
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
                    <input type="date" name="startDate" class="form-control">
                </div>
                <div class="form-group">
                    <label>结束时间</label>
                    <input type="date" name="endDate" class="form-control">
                </div>
                <div class="form-group-lg">
                    <label>检测指标</label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">水温
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">pH
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">溶解氧
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">电导率
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">浊度
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">氯含量
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">总磷
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">氨氮
                    </label>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="option" class="mgc mgc-success">碳含量
                    </label>
                </div>
            </form>
            <button id="export" class="btn btn-success">导出</button>
        </div>
            <div class="exportMenu animated">
            <ul>
                <li title="export to Excel"><img src="../images/fileType/xlsx_mac.png" width="24" height="24"> Excel</li>
                <li title="export to Word"><img src="../images/fileType/docx_win.png" width="24" height="24"> Word</li>
            </ul>
        </div>
        <div class="content">
            <table class="table table-hover table-bordered table-condensed" id="exportData">
                <caption></caption>
                <thead>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="pageMenu">
                <a onclick="pageEnd()" class="btn btn-success">末页</a>
                <a onclick="pageNext()" class="btn btn-success">下一页</a>
                <a onclick="pageBefore()" class="btn btn-success">上一页</a>
                <a onclick="pageStart()" class="btn btn-success">首页</a>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/tableExport.js"></script>
<script type="text/javascript" src="../js/mybase64.js"></script>
<script type="text/javascript" src="../js/getDate.func.js"></script>
<script type="text/javascript" src="../js/dataServer.js"></script>
</html>