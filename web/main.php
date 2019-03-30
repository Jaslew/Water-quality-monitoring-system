<?php
require_once "includes/Func.php";
require_once "includes/adminServer.class.php";
checkLogin();
$id = getID();
$adminServer = new AdminServer();
$admin = $adminServer::getAdmin($id);
if(!$admin->getHeader())
    $header = "images/header/header.png";
else
    $header = "\"images/header/".$admin->getHeader()."\"";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>水质监测</title>
    <link type="text/css" rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="css/font-awesome.css">
    <link type="text/css" rel="stylesheet" href="css/animate.min.css">
    <link type="text/css" rel="stylesheet" href="css/index_style.css">
</head>

<body>
<div id="top-bar">
    <img class="img-circle" alt="header" src=<?php echo $header?>>
    <span><?php echo $admin->getName()?></span>
</div>
<div id="left-bar">
    <ul class="nav nav-pills nav-stacked">
        <li class="active"><a onclick="router('htmls/home.php')"><i class="fa fa-home"></i>主页</a></li>
        <li><a onclick="router('htmls/data.php')"><i class="fa fa-list-ol"></i>综合数据</a></li>
        <li><a onclick="router('htmls/chart.php')"><i class="fa fa-line-chart"></i>报表分析</a></li>
        <li><a onclick="router('htmls/spy.php')"><i class="fa fa-video-camera"></i>实时监控</a></li>
        <li><a onclick="router('htmls/site.php')"><i class="fa fa-pencil-square-o"></i>站点管理</a></li>
        <li><a onclick="router('htmls/control.php')"><i class="fa fa-television"></i>控制台</a></li>
        <li><a onclick="router('htmls/key.php')"><i class="fa fa-key"></i>权限中心</a></li>
        <li class="logout"><a data-toggle="modal" href="#logOut"><i class="fa fa-sign-out"></i>退出</a></li>
    </ul>
</div>
<div id="content">
    <iframe name="page" src="htmls/home.php" width="100%" height="100%" frameborder="0"></iframe>
</div>
<!----退出窗口------->
<div class="modal fade" data-backdrop="static" id="logOut">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">温馨提示</h4>
            </div>
            <div class="modal-body">
                <p>您确定要退出吗？</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-dismiss="modal">否</button>
                <button id="logout" class="btn btn-default" data-dismiss="modal">退出</button>
            </div>
        </div>
    </div>
</div>
</body>
<script src="js/jquery.js"></script>
<script src="bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
<script src="js/router.js"></script>
<script>
    $(document).ready(function(){
        //更新菜单栏
        $("li").each(function(index){
            $(this).click(function(){
                $("li").removeClass("active");
                $(this).addClass("active");
            });
        });

        //退出登录
        $("#logout").click(function () {
            $.ajax({
                url:'includes/logoutProcess.php',
                type:'GET', //POST
                async:true,    //或false,是否异步
                timeout:1000,    //超时时间
                dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
                success:function(data,textStatus,jqXHR){
                    window.location.href = "login.html";
                },
                error:function(xhr,textStatus){
                    //
                },
                complete:function(){
                    //
                }
            });
        })
    })
</script>
</html>