function home() {
    $$("#home #home-tool-row1 .col-33:nth-of-type(1)").on("click",function () {
        mainView.router.navigate('/siteadd/');
    });
    $$("#home #home-tool-row1 .col-33:nth-of-type(2)").on("click",function () {
        mainView.router.navigate('/useradd/');
    });
    $$("#home #home-tool-row1 .col-33:nth-of-type(3)").on("click",function () {
        mainView.router.navigate('/notice/');
    });
    $$("#home #home-tool-row2 .col-33").on("click",function () {
        mainView.router.navigate("/usershow/");
    });

    //获取主页面数据
    app.request.post('ajax/homeProcess.php', "", function (data, status) {
        if(status == 200 && data){
            data = JSON.parse(data);
            $$("#home #home-system .col-33:nth-of-type(1) span").text(data.online);
            $$("#home #home-system .col-33:nth-of-type(2) span").text(data.stations);
            $$("#home #home-system .col-33:nth-of-type(3) span").text(data.users);
        }
    });
}