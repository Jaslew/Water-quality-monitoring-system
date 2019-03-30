<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-4-17
 * Time: 下午6:31
 */
require_once "../includes/Func.php";
require_once "../includes/adminServer.class.php";
require_once "../includes/stationServer.class.php";
require_once dirname(__DIR__)."/../includes/CommonFun.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(7);
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
    <title>control</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap-switch.min.css">
    <link type="text/css" rel="stylesheet" href="../css/control_style.css">
    <link type="text/css" rel="stylesheet" href="../css/animate.min.css">
    <link type="text/css" rel="stylesheet" href="../css/font-awesome.css">
</head>
<body>
    <div id="swInfo" class="alert alert-danger" role="alert">
        <button class="close"  type="button" >&times;</button>
        <p></p>
    </div>
    <div class="control-select">
        <div class="form-group form-inline">
            <label for="station">选择站点: </label>
            <select name="no" id="station" class="form-control" style="width: 200px">
                <?php
                foreach ($station as $s){
                    $no = $s->no;
                    $name = $s->name;
                    echo "<option value='$no'>$name"." (".$no.")"."</option>";
                }
                ?>
            </select>
            <span data-toggle="modal" href="#update"><i class="fa fa-cog"></i></span>
        </div>
    </div>
    <div class="control-switch form-group form-inline">
        <ul id="u1">
            <li>
                <label for="button1">开关1</label>
                <input id="button1" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button2">开关2</label>
                <input id="button2" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button3">开关3</label>
                <input id="button3" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button4">开关4</label>
                <input id="button4" type="checkbox" name="sw" value="0"/>
            </li>
        </ul>
    </div>
    <div class="control-switch form-group form-inline">
        <ul id="u2">
            <li>
                <label for="button5">开关5</label>
                <input id="button5" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button6">开关6</label>
                <input id="button6" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button7">开关7</label>
                <input id="button7" type="checkbox" name="sw" value="0"/>
            </li>
            <li>
                <label for="button8">开关8</label>
                <input id="button8" type="checkbox" name="sw" value="0"/>
            </li>
        </ul>
    </div>
    <!----修改名称--------->
    <div class="modal fade" data-backdrop="static" id="update">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <table class="table table-condensed table-hover">
                        <tbody>
                        <tr>
                            <td>开关1</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关2</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关3</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关4</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关5</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关6</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关7</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>开关8</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button onclick="getUpdateName()" class="btn btn-danger" data-dismiss="modal">重命名</button>
                    <button class="btn btn-success" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../js/jquery.js"></script>
<script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../bootstrap-3.3.7-dist/js/bootstrap-switch.min.js"></script>
<script src="../js/controlServer.js"></script>
</html>
