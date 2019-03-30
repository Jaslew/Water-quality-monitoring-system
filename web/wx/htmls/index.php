<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>江南大学水质监控平台</title>
    <link rel="stylesheet" href="css/framework7.css">
    <link rel="stylesheet" href="css/framework7-icons.css">
    <link rel="stylesheet" href="../../css/font-awesome.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/system.css">
    <link rel="stylesheet" href="css/notice.css">
</head>
<body>
<div id="app">
    <div class="view view-main view-init">
        <!-- Tabbar for switching views-tabs -->
        <div class="toolbar tabbar-labels toolbar-bottom-md">
            <div class="toolbar-inner">
                <a href="/home/" class="tab-link mactive">
                    <i class="icon icon-fill f7-icons">home</i>
                    <span class="tabbar-label">主页</span>
                </a>
                <a href="/graph/" class="tab-link">
                    <i class="icon f7-icons">graph_round</i>
                    <span class="tabbar-label">数据报表</span>
                </a>
                <a href="/console/" class="tab-link">
                    <i class="icon f7-icons">filter-fill</i>
                    <span class="tabbar-label">控制台</span>
                </a>
                <a href="/system/" class="tab-link">
                    <i class="icon f7-icons">compose</i>
                    <span class="tabbar-label">系统管理</span>
                </a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/framework7.js"></script>
<script type="text/javascript" src="../../js/Chart.js"></script>
<script type="text/javascript" src="js/home.js"></script>
<script type="text/javascript" src="js/graph.js"></script>
<script type="text/javascript" src="js/system.js"></script>
<script type="text/javascript" src="js/notice.js"></script>
<script type="text/javascript" src="js/console.js"></script>
<script type="text/javascript" src="js/index.js"></script>
</body>
</html>