<?php
require_once "../includes/Func.php";
require_once "../includes/adminServer.class.php";

if(getSession() == 0){
    require_once dirname(__DIR__)."/../configs/poem.php";
    $poem = new Poem(5);
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$title."</span>";
    echo "<span style='display: block;width: 100%;text-align: center'>".$poem::$content."</span>";
    exit();
}

//获取当前账号角色
$roleid = AdminServer::getRole(getID());

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>key</title>
    <link type="text/css" rel="stylesheet" href="../bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="../css/key_style.css">
    <link type="text/css" rel="stylesheet" href="../css/font-awesome.css">
    <link type="text/css" rel="stylesheet" href="../css/magic-input.css">
    <link type="text/css" rel="stylesheet" href="../css/animate.min.css">
    <link type="text/css" rel="stylesheet" href="../css/jquery.Jcrop.css">
</head>
<body>
<div id="key-info" role="alert">
    <button class="close" type="button" >&times;</button>
    <p></p>
</div>
<div id="content">
    <div class="top-bar">
        <form class="form-inline">
            <div class="form-group">
                <label>显示：
                    <select class="form-control" name="showType">
                        <option value="0">全部</option>
                        <option value="1">系统管理员</option>
                        <option value="2">普通管理员</option>
                        <option value="3">普通用户</option>
                    </select>
                </label>
            </div>
            <div class="form-group">
                <label><a data-toggle="modal" href="#add" class="btn btn-success"><i class="fa fa-plus"></i> 新增</a></label>
            </div>
        </form>
    </div>
    <div class="table-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>角色</th>
                    <th>最近登录</th>
                    <th>操作</th>
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
</div>


<!----用户详情------->
<div class="modal fade" data-backdrop="static" id="detail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <td>ID</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>用户名</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>角色</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>电话</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>邮箱</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>上次登录</td>
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


<!----修改用户------->
<div class="modal fade" data-backdrop="static" id="edite">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="key-edite-info" role="alert">
                    <button class="close" type="button" >&times;</button>
                    <p></p>
                </div>
                <form>
                    <table class="table table-condensed table-hover">
                        <tbody>
                        <tr>
                            <td>ID</td>
                            <td><input class="form-control" type="text" value="" disabled="disabled"></td>
                        </tr>
                        <tr>
                            <td>用户名</td>
                            <td><input class="form-control" type="text" value=""></td>
                        </tr>
                        <tr>
                            <td>角色</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>角色</td>
                            <td><select class="form-control">
                                    <option value="2">普通管理员</option>
                                    <option value="3">普通用户</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>电话</td>
                            <td><input class="form-control" type="number" value=""></td>
                        </tr>
                        <tr>
                            <td>邮箱</td>
                            <td><input class="form-control" type="email" value=""></td>
                        </tr>
                        <tr>
                            <td>重置密码</td>
                            <td><input class="form-control" type="password" value="" placeholder="重置当前用户登录密码"></td>
                        </tr>
                        <tr><td>头像</td>
                            <td>
                                <a class="file"><input id="imgFile" type="file" name="imgFile" accept="image/*">选择图片</a>
                                <a class="reset">复位</a>
                                <div id="preview-content">
                                    <img src="../images/header/header.png" id="target">
                                    <div id="preview-pane">
                                        <div class="preview-container">
                                            <img src="../images/header/header.png" class="jcrop-preview" alt="Preview">
                                        </div>
                                    </div>
                                </div>
                            </td>
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

<!----新增用户------->
<div class="modal fade" data-backdrop="static" id="add">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div id="key-add-info" role="alert">
                    <button class="close"  type="button" >&times;</button>
                    <p></p>
                </div>
                <form>
                    <table class="table table-condensed table-hover">
                        <tbody>
                        <tr>
                            <td>用户名</td>
                            <td><input class="form-control" type="text"></td>
                        </tr>
                        <tr>
                            <td>角色</td>
                            <td><select class="form-control">
                                    <option value="2">普通管理员</option>
                                    <option value="3" selected="selected">普通用户</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td>电话</td>
                            <td><input class="form-control" type="number"></td>
                        </tr>
                        <tr>
                            <td>邮箱</td>
                            <td><input class="form-control" type="email"></td>
                        </tr>
                        <tr>
                            <td>登录密码</td>
                            <td><input class="form-control" type="password"></td>
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

<!----移除用户--------->
<div class="modal fade" data-backdrop="static" id="remove">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">温馨提示</h4>
            </div>
            <div class="modal-body">
                <p></p>
                <input type="hidden">
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
<script src="../js/jquery.Jcrop.js"></script>
<script src="../js/getDate.func.js"></script>
<?php echo "<script>r = $roleid</script>"?>
<script src="../js/keyDemo.js"></script>
</html>