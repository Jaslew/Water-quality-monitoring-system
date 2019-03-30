function mconsole() {
    //待修改名称的li序号
    var readyN = "";
    var toggle = app.toggle.create({
        el: '#console .toggle',
        on: {
            change: function () {
                setSWState();
            }
        }
    });
    var ac1 = app.actions.create({
        buttons: [
            {
                text: '重命名',
                color:'red',
                onClick: function (){
                    //弹出重命名窗口
                    app.dialog.prompt('请输入新的名称：', function (data) {
                        updateButtonName(data);
                    });
                }
            },
            {
                text: '取消'
            }
        ]
    });

    $$('#console .sw li').on('taphold', function () {
        readyN = $$(this).index()
        ac1.open();
    });

    //获取下拉站点选项
    app.request({
        url: 'ajax/graphSelectProcess.php',
        async: false,//这里使用同步
        method: 'POST',
        timeout: 1500,
        success: function (data) {
            if(data){
                data = JSON.parse(data);
                var pnode = "#console .list select[name = 'site']";
                var snode = "";
                for(var i = 0; i < data.length; i++){
                    if(i == 0)
                        snode += "<option value='"+data[i].no+"'>"+data[i].name+" ("+data[i].no+")"+"</option>";
                    else
                        snode += "<option value='"+data[i].no+"'>"+data[i].name+" ("+data[i].no+")"+"</option>";
                }
                $$(pnode).append(snode);
                getButtonName();
                //获取状态
                getSWState();
            }
        },
        error:function () {
            //
        }
    });

    //手动选择站点时获取按钮状态
    $$("#console select").on("change",function () {
        getButtonName();
        getSWState();
    });

    //设置按钮显示状态
    function initButton(data) {
        var bv = data.split("");
        if(bv.length == 8){
            for(var i = 0; i < 8; i++){
                toggle.$inputEl[i].checked = parseInt(bv[i]);
            }
        }
    }
    //获取按钮状态
    function getSWState(){
        var formData = app.form.convertToData('#console-input');
        formData = {type: "get", no: formData.site};
        app.request({
            url: '../../includes/switchProcess.php',
            async: true,
            method: 'POST',
            timeout: 1500,
            data: formData,
            success: function (data) {
                if(data == 0){
                    showTip(0);
                }else if(data == -1){
                    showTip(-1);
                }else if(data == -2){
                    showTip(-2);
                }else if(data == ""){
                    showTip(-3);
                }else{
                    //初始化按钮状态
                    initButton(data);
                }
            },
            error:function () {
                showTip(-4);
            }
        });
    }
    //设置按钮状态，下发到控制端
    function setSWState() {
        var onoff = [];
        for(var i = 0; i < 8; i++){
            onoff[i] = toggle.$inputEl[i].checked == true ? 1 : 0;
        }
        onoff = onoff.join("");
        var site = app.form.convertToData('#console-input').site;
        var formData = {type: "set",no: site,ss: onoff};
        app.request({
            url: '../../includes/switchProcess.php',
            async: true,
            method: 'POST',
            timeout: 1500,
            data: formData,
            success: function (data) {
                if(data == 0){
                    showTip(0);
                }else if(data == -1){
                    showTip(-1);
                }else if(data == -2){
                    showTip(-2);
                }else if(data == ""){
                    showTip(-3);
                }else{
                    initButton(data);
                }
            },
            error:function () {
                showTip(-4);
            }
        });
    }
    //获取按钮名称
    function getButtonName() {
        var formData = app.form.convertToData('#console-input');
        formData = {no: formData.site};
        app.request({
            url: '../../includes/switchGetProcess.php',
            async: true,
            method: 'POST',
            data: formData,
            success: function (data) {
                if(data){
                    var tag;
                    data = JSON.parse(data);
                    for(var i = 1; i <= 8; i++){
                        tag = "#console #console-button ul li:nth-of-type("+i+") .swn";
                        $$(tag).text(data[i-1]);
                    }
                }
            },
            error:function () {
                //
            }
        });
    }
    //重命名按钮
    function updateButtonName(text) {
        if(text != ""){
            var names = new Array(8);
            names[readyN] = text;
            names = names.toString();
            var formData = app.form.convertToData('#console-input');
            formData = {no: formData.site, text: names};
            app.request({
                url: '../../includes/switchUpdateProcess.php',
                async: true,
                method: 'POST',
                timeout: 1500,
                data: formData,
                success: function (data) {
                    if(data == 1){
                        getButtonName();
                        showTip(1)
                    }else{
                        showTip(2)
                    }
                },
                error:function () {
                    //
                }
            });
        }
    }
    //错误分类
    function showTip(i) {
        var mtext;
        switch (i){
            case 0:{
                mtext = "参数错误！";
                break;
            }
            case 1:{
                mtext = "操作成功！";
                break;
            }
            case 2:{
                mtext = "操作失败！";
                break;
            }
            case -1:{
                mtext = "当前站点不在线！";
                break;
            }
            case -2:{
                mtext = "抱歉，您的权限不足，将无法控制终端，但允许您修改相关信息！";
                break;
            }
            case -3:{
                mtext = "响应超时！";
                break;
            }
            case -4:{
                mtext = "服务器没有响应！";
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
}