<?php
require_once "../includes/Func.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(1);
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$title."</span>";
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$content."</span>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>site</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/site_style.css">
    <link type="text/css" rel="stylesheet" href="../css/font-awesome.css">
    <link type="text/css" rel="stylesheet" href="../css/magic-input.css">
</head>
<body>
<div id="site-info" role="alert">
    <button class="close" type="button" >&times;</button>
    <p></p>
</div>
<div class="menu">
    <a class="btn btn-success" data-toggle="modal" href="#add"><i class="fa fa-plus"></i> 新增</a>
    <a class="btn btn-danger" onclick="readyRemove()"><i class="fa fa-trash"></i> 移除</a>
</div>
<div class="table-content">
    <table class="main table animated bounceInLeft">
        <thead>
        <tr>
            <th><input type="checkbox" name="selects" class="mgc mgc-success"></th>
            <th>站点号</th>
            <th>站点名</th>
            <th>状态</th>
            <th>地理位置</th>
            <th>负责人</th>
            <th>联系电话</th>
            <th>邮箱</th>
            <th>详细</th>
            <th>修改</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<div class="bottom-bar">
    <div class="bottom-bar-left">
        <span></span>
    </div>
    <div class="pageMenu">
        <a onclick="pageNext()" class="btn btn-success">下一页</a>
        <a onclick="pageBefore()" class="btn btn-success">上一页</a>
    </div>
</div>
<!----站点详情------->
<div class="modal fade" data-backdrop="static" id="detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td>站点号</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>站点名</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>地理位置</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>负责人</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>联系电话</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>邮箱</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>采样频率 (s/次)</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>IP地址</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>端口号</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>创建时间</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>最近修改</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!----修改站点------->
<div class="modal fade" data-backdrop="static" id="edite">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="site-edite-info" role="alert">
                    <button class="close" type="button" >&times;</button>
                    <p></p>
                </div>
                <form>
                <table class="table table-condensed table-hover">
                    <tbody>
                    <tr>
                        <td>站点号</td>
                        <td><input class="form-control" type="text" value="" disabled="disabled"></td>
                    </tr>
                    <tr>
                        <td>站点名</td>
                        <td><input class="form-control" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td>地理位置</td>
                        <td><input class="form-control" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td>负责人</td>
                        <td><input class="form-control" type="text" value=""></td>
                    </tr>
                    <tr>
                        <td>联系电话</td>
                        <td><input class="form-control" type="number" value=""></td>
                    </tr>
                    <tr>
                        <td>邮箱</td>
                        <td><input class="form-control" type="email" value=""></td>
                    </tr>
                    <tr>
                        <td>采样频率 (s/次)</td>
                        <td><input class="form-control" type="number" value=""></td>
                    </tr>
                    </tbody>
                </table>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="editeOK()" class="btn btn-danger">确定</button>
                <button class="btn btn-success" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<!----新增站点------->
<div class="modal fade" data-backdrop="static" id="add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="site-add-info" role="alert">
                    <button class="close" type="button" >&times;</button>
                    <p></p>
                </div>
                <form>
                    <table class="table table-condensed table-hover">
                        <tbody>
                        <tr>
                            <td>站点号(必填)</td>
                            <td><input class="form-control" type="number"></td>
                        </tr>
                        <tr>
                            <td>站点名(必填)</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>地理位置</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>负责人</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>联系电话</td>
                            <td><input class="form-control" type="number"></td>
                        </tr>
                        <tr>
                            <td>邮箱</td>
                            <td><input class="form-control" type="email"></td>
                        </tr>
                        <tr>
                            <td>采样频率 (s/次)(必填)</td>
                            <td><input class="form-control" type="number"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="addOK()" class="btn btn-danger">添加</button>
                <button class="btn btn-success" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<!----移除站点--------->
<div class="modal fade" data-backdrop="static" id="remove">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">温馨提示</h4>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button onclick="doRemove()" class="btn btn-danger" data-dismiss="modal">确定</button>
                <button class="btn btn-success" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

</body>
<script src="../js/jquery.js"></script>
<script src="../bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="../js/common.js"></script>
<script src="../js/siteDemo.js"></script>
</html>