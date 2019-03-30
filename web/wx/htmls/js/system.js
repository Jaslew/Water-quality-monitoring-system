
function msystem() {
    var readySID, readyUID;
    //默认进入站点页面
    if(mainView.router.url == "/system/user/"){
        getUserList();
        $$("#system .fab a").attr("href", "/useradd/");
    }else{
        getSiteList();
        $$("#system .fab a").attr("href", "/siteadd/");
    }
    //绑定界面切换事件
    $$(".navbar .subnavbar a").on("click",function () {
        if(mainView.router.url == "/system/user/"){
            getUserList();
            $$("#system .fab a").attr("href", "/useradd/");
        }
        else{
            getSiteList();
            $$("#system .fab a").attr("href", "/siteadd/");
        }
    });
    //获取站点列表
    function getSiteList() {
        app.request({
            url: 'ajax/siteListProcess.php',
            async: false,
            method: 'POST',
            success: function (data) {
                if(data != 0){
                    data = JSON.parse(data);
                    setSiteView(data);
                }
            },
            error:function () {
                //
            }
        });
    }
    //获取用户列表
    function getUserList() {
        app.request({
            url: 'ajax/userListProcess.php',
            async: false,
            method: 'POST',
            success: function (data) {
                if(data != 0){
                    data = JSON.parse(data);
                    setUserView(data);
                }
            },
            error:function () {
                //
            }
        });
    }
    //更新站点视图
    function setSiteView(data) {
        $$("#system #site").empty();
        var siteNode = "";
        var sid, state;
        for(var i = 0; i < data.length; i++){
            sid = "sid"+data[i].no;
            state = data[i].state == 1 ? "<span style='color:green'>在线</span>" : "<span style='color:red'>离线</span>";
            siteNode += "<div id='"+sid+"' class='card site-card'><div class='card-content'>" +
                "<div class='site-avatar'><span class=\"fa-stack fa-lg\">" +
                "<i class=\"fa fa-circle fa-stack-2x\"></i><i class=\"fa fa-location-arrow fa-stack-1x fa-inverse\"></i></span></div>" +
                "<div class='site-name'>"+data[i].name+" ("+data[i].no+")"+"</div>" +
                "<div class='site-date'>"+state+"</div></div></div>"
        }
        $$("#system #site").append(siteNode);
        sviewBind();
    }
    //更新站点视图
    function setUserView(data) {
        $$("#system #user").empty();
        var userNode = "";
        var uid, header, lasttime;
        for(var i = 0; i < data.length; i++){
            uid = "uid"+data[i].id;
            header = data[i].header ? data[i].header : "header.png";
            header = "<img src='../../images/header/"+header+"'>";
            lasttime = "最近登录："+ Html5DateFormate2(new Date(data[i].lasttime * 1000));
            userNode += "<div id='"+uid+"' class='card user-card'><div class='card-content'>" +
                "<div class='user-avatar'>"+header+"</div>" +
                "<div class='user-name'>"+data[i].name+" (ID:"+data[i].id+")</div>" +
                "<div class='user-date'>"+lasttime+"</div></div></div>"
        }
        $$("#system #user").append(userNode);
        uviewBind();
    }
    //站点界面交互事件绑定
    function sviewBind() {
        var site = app.actions.create({
            buttons: [
                {
                    text: '修改',
                    color:'blue',
                    onClick: function (){
                        mainView.router.navigate('/siteupdate/?sid='+readySID);
                    }
                },
                {
                    text: '删除',
                    color:'red',
                    onClick: function (){
                        app.dialog.confirm('您确定要删除站点 '+readySID+' 吗?此操作将不可撤销，请慎重！', function () {
                            app.request({
                                url: '../../includes/stationRemoveProcess.php',
                                async: true,
                                method: 'POST',
                                data: {t: 1 ,no0 : readySID},
                                success: function (data) {
                                    if(data == 1){
                                        mainView.router.refreshPage();
                                        showTip(1);
                                    }else{
                                        showTip(0);
                                    }
                                },
                                error:function () {
                                    showTip(-1);
                                }
                            });
                        });
                    }
                }
            ]
        });
        //站点长按
        $$('#system #site .card').on('taphold', function () {
            //获取长按的站点号
            readySID = $$(this).attr("id").substr(3);
            site.open();
        });
        //站点单击
        $$('#system #site .card').on('click', function () {
            var sid = $$(this).attr("id").substr(3);
            mainView.router.navigate('/siteshow/?sid='+sid);
        });
    }
    //用户界面交互事件绑定
    function uviewBind() {
        var user = app.actions.create({
            buttons: [
                {
                    text: '修改',
                    color:'blue',
                    onClick: function (){
                        mainView.router.navigate('/userupdate/?uid='+readyUID);
                    }
                },
                {
                    text: '删除',
                    color:'red',
                    onClick: function (){
                        app.dialog.confirm('您确定要删除用户 '+readyUID+' 吗?此操作将不可撤销，请慎重！', function () {
                            app.request({
                                url: '../../includes/keyRemoveProcess.php',
                                async: true,
                                method: 'POST',
                                data: {readyId : readyUID},
                                success: function (data) {
                                    if(data == 0){
                                        //操作失败
                                        showTip(0)
                                    }else if(data == -1){
                                        //没有权限
                                        showTip(-5);
                                    }else{
                                        mainView.router.navigate('/system/user/',{
                                            ignoreCache : true
                                        });
                                        showTip(1);
                                    }
                                },
                                error:function () {
                                    showTip(-1);
                                }
                            });
                        });
                    }
                }
            ]
        });
        //用户长按
        $$('#system #user .card').on('taphold', function () {
            //获取长按的用户ID
            readyUID = $$(this).attr("id").substr(3);
            user.open();
        });
        //用户单击
        $$('#system #user .card').on('click', function () {
            var uid = $$(this).attr("id").substr(3);
            mainView.router.navigate('/usershow/?uid='+uid);
        });
    }
}

//站点详细
function siteShow() {
    var no = mainView.router.currentRoute.query.sid;
    app.request({
        url: 'ajax/siteDetailProcess.php',
        async: true,
        method: 'POST',
        data: {no: no},
        success: function (data) {
            if(data != 0){
                data = JSON.parse(data);
                $$("#siteshow table tr:nth-of-type(1) td:nth-of-type(2)").text(data.no);
                $$("#siteshow table tr:nth-of-type(2) td:nth-of-type(2)").text(data.name);
                $$("#siteshow table tr:nth-of-type(3) td:nth-of-type(2)").text(data.pos);
                $$("#siteshow table tr:nth-of-type(4) td:nth-of-type(2)").text(data.charge);
                $$("#siteshow table tr:nth-of-type(5) td:nth-of-type(2)").text(data.tel);
                $$("#siteshow table tr:nth-of-type(6) td:nth-of-type(2)").text(data.email);
                $$("#siteshow table tr:nth-of-type(7) td:nth-of-type(2)").text(data.hz);
                $$("#siteshow table tr:nth-of-type(8) td:nth-of-type(2)").text(data.ip);
                $$("#siteshow table tr:nth-of-type(9) td:nth-of-type(2)").text(data.port);
                $$("#siteshow table tr:nth-of-type(10) td:nth-of-type(2)").text(Html5DateFormate2(new Date(data.ctime * 1000)));
                $$("#siteshow table tr:nth-of-type(11) td:nth-of-type(2)").text(Html5DateFormate2(new Date(data.atime * 1000)));
            }
        },
        error:function () {
            //
        }
    });
}
//用户详细
function userShow() {
    var uid = mainView.router.currentRoute.query.uid;
    app.request({
        url: 'ajax/userDetailProcess.php',
        async: true,
        method: 'POST',
        data: {id: uid},
        success: function (data) {
            if(data && data != 0){
                data = JSON.parse(data);
                var header = data.header ? data.header : "header.png";
                var headerNode = "<img src='../../images/header/"+header+"?t="+parseInt(Math.random()*10)+"'>";
                var role = data.roleid == 1 ? "系统管理员" : (data.roleid == 2 ? "普通管理员" : "普通用户");
                $$("#usershow table tr:nth-of-type(1) td:nth-of-type(2)").html(headerNode);
                $$("#usershow table tr:nth-of-type(2) td:nth-of-type(2)").text(data.id);
                $$("#usershow table tr:nth-of-type(3) td:nth-of-type(2)").text(data.name);
                $$("#usershow table tr:nth-of-type(4) td:nth-of-type(2)").text(role);
                $$("#usershow table tr:nth-of-type(5) td:nth-of-type(2)").text(data.email);
                $$("#usershow table tr:nth-of-type(6) td:nth-of-type(2)").text(data.tel);
                $$("#usershow table tr:nth-of-type(7) td:nth-of-type(2)").text(Html5DateFormate2(new Date(data.lasttime * 1000)));
            }
        },
        error:function () {
            //
        }
    });
}
//更新站点
function siteUpdateData() {
    var no = mainView.router.currentRoute.query.sid;
    app.request({
        url: 'ajax/siteDetailProcess.php',
        async: true,
        method: 'POST',
        data: {no: no},
        success: function (data) {
            if(data != 0 && data){
                data = JSON.parse(data);
                var formData = {
                    "no" : data.no,
                    "name" : data.name,
                    "pos" : data.pos,
                    "charge" : data.charge,
                    "tel" : data.tel,
                    "email" : data.email,
                    "hz" : data.hz
                };
                app.form.fillFromData('#siteupdate-form', formData);
            }
        },
        error:function () {
            //
        }
    });

    $$("#siteUpdateDo").on("click",function () {
        var formData = app.form.convertToData('#siteupdate-form');
        //数据简单过滤
        var isno = /^\d{3}$/.test(formData.no);
        var ishz = /^\d+$/.test(formData.hz);
        if(isno && ishz && formData.name != ""){
            app.request({
                url: '../../includes/stationUpdateProcess.php',
                async: true,
                method: 'POST',
                data: formData,
                success: function (data) {
                    if(data == 1){
                        mainView.router.navigate('/system/');
                        showTip(1);
                    }else{
                        showTip(0);
                    }
                },
                error:function () {
                    showTip(-1);
                }
            });
        }else{
            showTip(-2);
        }
    });
}
//更新用户
function userUpdateData() {
    var uid = mainView.router.currentRoute.query.uid;
    app.request({
        url: 'ajax/userDetailProcess.php',
        async: true,
        method: 'POST',
        data: {id: uid},
        success: function (data) {
            if(data != 0 && data){
                data = JSON.parse(data);
                var formData = {
                    "id" : data.id,
                    "name" : data.name,
                    "tel" : data.tel,
                    "email" : data.email,
                };
                if(data.roleid != 1)
                    formData.roleid = data.roleid;
                else{
                    $$("#userupdate #userupdate-form li:nth-of-type(6)").empty();
                    $$("#userupdate #userupdate-form li:nth-of-type(6)").append(
                        "<div class=\"item-content item-input\">\n" +
                        "<div class=\"item-inner\">" +
                        "<div class=\"item-title item-label\">角色</div>" +
                        "<div class=\"item-input-wrap\">" +
                        "<label class=\"item-radio item-content\">" +
                        "<input type=\"radio\" name=\"roleid\" value=\"1\" checked readonly/>" +
                        "<i class=\"icon icon-radio\"></i>" +
                        "<div class=\"item-inner\">" +
                        "<div class=\"item-title\">系统管理员</div>" +
                        "</div></label></div></div></div>"
                    );
                }
                app.form.fillFromData('#userupdate-form', formData);
            }
        },
        error:function () {
            //
        }
    });
    $$("#userupdateDo").on("click",function () {
        var formData = app.form.convertToData('#userupdate-form');
        //数据简单过滤
        var isid = /^\d{7}$/.test(formData.id);
        var isroleid = formData.roleid == 2 || 3;
        if(isid && isroleid && formData.name != ""){
            app.request({
                url: 'ajax/userUpdateProcess.php',
                async: true,
                method: 'POST',
                data: formData,
                success: function (data) {
                    if(data == 1){
                        mainView.router.navigate('/system/user/');
                        showTip(1);
                    }else{
                        showTip(0);
                    }
                },
                error:function () {
                    showTip(-1);
                }
            });
        }else{
            showTip(-2);
        }
    });
}
//增加站点
function siteAddData() {
    $$("#siteAddDo").on("click",function () {
        var formData = app.form.convertToData('#siteadd-form');
        var isno = /^\d{3}$/.test(formData.no);
        var ishz = /^\d+$/.test(formData.hz);
        if(!(formData.no && formData.name && formData.hz)){
            showTip(-2);
        }else if(!isno){
            showTip(-3);
        }else if(!ishz){
            showTip(-4);
        }else{
            app.request({
                url: '../../includes/stationAddProcess.php',
                async: true,
                method: 'POST',
                data: formData,
                success: function (data) {
                    if(data == ""){
                        showTip(-2);
                    }else if(data == 1){
                        //操作成功
                        mainView.router.navigate('/system/');
                        showTip(1);
                    }else{
                        showTip(0);
                    }
                },
                error:function () {
                    showTip(-1);
                }
            });
        }
    });
}
//增加用户
function userAddData() {
    $$("#userAddDo").on("click",function () {
        var formData = app.form.convertToData('#useradd-form');
        //数据简单过滤
        var isroleid = formData.roleid == 2 || 3;
        if(isroleid && formData.name != "" && formData.password != ""){
            app.request({
                url: '../../includes/keyAddProcess.php',
                async: true,
                method: 'POST',
                data: formData,
                success: function (data) {
                    if(data == 0){
                        //操作失败
                        showTip(0)
                    }else if(data == -1){
                        //没有权限
                        showTip(-5)
                    }else{
                        //操作成功
                        mainView.router.navigate('/system/user/');
                        showTip(1);
                    }
                },
                error:function () {
                    showTip(-1);
                }
            });
        }else{
            showTip(-2);
        }
    });
}

//错误分类
function showTip(i) {
    var mtext;
    switch (i){
        case 0:{
            mtext = "操作失败！";
            break;
        }
        case 1:{
            mtext = "操作成功！";
            break;
        }
        case -1:{
            mtext = "请求超时！";
            break;
        }
        case -2:{
            mtext = "您的数据有误，请核对后再提交！";
            break;
        }
        case -3:{
            mtext = "站点号输入有误，必须是000~999的三位数字！";
            break;
        }
        case -4:{
            mtext = "请输入正确的采样频率！";
            break;
        }
        case -5:{
            mtext = "抱歉，您没有权限！";
            break;
        }
    }
    var tc = app.toast.create({
        text: mtext,
        position: 'center',
        closeTimeout: 2000
    });
    tc.open();
}
//时间转化
function Html5DateFormate2(date) {
    var Reg = /^\d{2}$/;
    var Month = date.getMonth()+1;
    var Day = date.getDate();
    var Hour = date.getHours();
    var Min = date.getMinutes();
    var Sec = date.getSeconds();
    Month = Reg.exec(Month) ? Month : '0'+Month;
    Day = Reg.exec(Day) ? Day : '0'+Day;
    Hour = Reg.exec(Hour) ? Hour : '0'+Hour;
    Min = Reg.exec(Min) ? Min : '0'+Min;
    Sec = Reg.exec(Sec) ? Sec : '0'+Sec;
    return date.getFullYear()+"-"+Month+"-"+Day+" "+Hour+":"+Min+":"+Sec;
}