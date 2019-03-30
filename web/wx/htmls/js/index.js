// Dom7
var $$ = Dom7;

// Framework7 App main instance
var app  = new Framework7({
    root: '#app', // App root element
    id: 'io.framework7.water', // App bundle ID
    name: '江南大学水质监控平台', // App name
    theme: 'ios', //theme detection
    touch: {
        tapHold: true //enable tap hold events
    },
    routes: [
        {
            name: 'login',
            path: '/',
            url: 'component/login.html'
        },
        {
            name: 'home',
            path: '/home/',
            url: 'component/home.html'
        },
        {
            name: 'graph',
            path: '/graph/',
            url: 'component/graph.html'
        },
        {
            name: 'console',
            path: '/console/',
            url: 'component/console.html'
        },
        {
            name: 'system',
            path: '/system/',
            url: 'component/system.html',
            tabs: [
                {
                    path : '/',
                    id : 'site'
                },
                {
                    path : '/user/',
                    id : 'user'
                }
            ]
        },
        {
            name: 'notice',
            path: '/notice/',
            url: 'component/notice.html'
        },
        {
            name: 'siteshow',
            path: '/siteshow/',
            url: 'component/siteshow.html'
        },
        {
            name: 'siteupdate',
            path: '/siteupdate/',
            url: 'component/siteupdate.html'
        },
        {
            name: 'siteadd',
            path: '/siteadd/',
            url: 'component/siteadd.html'
        },
        {
            name: 'usershow',
            path: '/usershow/',
            url: 'component/usershow.html'
        },
        {
            name: 'userupdate',
            path: '/userupdate/',
            url: 'component/userupdate.html'
        },
        {
            name: 'useradd',
            path: '/useradd/',
            url: 'component/useradd.html'
        }
    ]
});

var mainView = app.views.create('.view-main',{
    url: '/',
    on: {
        pageInit : function (obj) {
            var name = obj.route.name;
            if(name == "login"){
                //检查是否保存了 cookie
                app.request({
                    url: 'ajax/statusGetProcess.php',
                    async: true,
                    method: 'POST',
                    data: {t : "cookie"},
                    success: function (data) {
                        if(data != 0){
                            //存在cookie
                            data = JSON.parse(data);
                            var formData = {
                                "id" : data.id,
                                "password" : data.password,
                            };
                            app.form.fillFromData('#login-form', formData);
                        }
                    }
                });
                //用户点击登录按钮后登录跳转
                $$("#login .list .button").on("click",function () {
                    var formData = app.form.convertToData('#login-form');
                    app.request({
                        url: 'ajax/loginProcess.php',
                        async: true,
                        method: 'POST',
                        data: formData,
                        success: function (data) {
                            if(data == 1){
                                mainView.router.navigate('/home/');
                            }else{
                                var tc = app.toast.create({
                                    icon: app.theme === 'ios' ? '<i class="f7-icons">close</i>' : '<i class="material-icons">close</i>',
                                    text: "用户名或密码错误！",
                                    position: 'center',
                                    closeTimeout: 2000
                                });
                                tc.open();
                            }
                        }
                    });
                });
            }else if(name == "home"){
                home();
            }else if(name == "graph"){
                graph();
            }else if(name == "console"){
                mconsole();
            }else if(name == "system"){
                msystem();
                $$(".toolbar .mactive").removeClass("mactive");
                $$(".toolbar a:nth-of-type(4)").addClass("mactive");
            }else if(name == "notice"){
                notice();
            }else if(name == "siteshow"){
                siteShow();
            }else if(name == "siteupdate"){
                siteUpdateData();
            }else if(name == "siteadd"){
                siteAddData();
            }else if(name == "usershow"){
                userShow();
            }else if(name == "userupdate"){
                userUpdateData();
            }else if(name == "useradd"){
                userAddData();
            }
        }
    }
});

//检查是否存有session,如果有就直接跳到主界面
app.request({
    url: 'ajax/statusGetProcess.php',
    async: true,
    method: 'POST',
    data: {t : "session"},
    success: function (data) {
        if(data == 1){
            //存在 session,免登录
            mainView.router.navigate('/home/');
        }
    }
});

$$(".toolbar a").each(function(index){
    $$(this).click(function(){
        $$(".toolbar .mactive").removeClass("mactive");
        $$(this).addClass("mactive");
    });
});