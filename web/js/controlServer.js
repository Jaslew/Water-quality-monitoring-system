var SS = "";        //开关量
var no = "";        //站点号
$(document).ready(function () {
    $('.control-switch input').bootstrapSwitch({
        onText:'打开',
        offText:'关闭',
        onColor:'success',
        onSwitchChange : function(event, state) {
            if(state == true)
                $(this).val("1");
            else
                $(this).val("0");
            //获取按钮开关状态
            getButValue();
            //重置终端开关状态
            setSWState();
        }
    });
    //获取当前站点按钮状态
    tmp = $(".control-select .form-group select option:nth-of-type(1)").val();
    if(tmp){
        no = tmp;
        getButtonName();
        getSWState();
    }
    $(".control-select .form-group").find("select[name = 'no']").change(function () {
        no = $(this).val();
        getButtonName();
        getSWState();
    });
    $("#swInfo .close").click(function () {
        $("#swInfo").css({"display":"none"});
    });
});

//将按钮的value值重置为从服务器获取到的值，并更改显示状态
function initButton(data) {
    SS = data
    var bv = data.split("");
    var tag;
    if(bv.length == 8){
        $('input[name="sw"]').each(function(i){
            $(this).val(bv[i]);
            tag = "#button" + (i+1);
            $(tag).bootstrapSwitch("state", parseInt(bv[i]));
        });
    }
}

//获取按钮的value
function getButValue() {
    //tmp为八位开关量
    var tmp =[];//定义一个数组
    $('input[name="sw"]').each(function(){
       tmp.push($(this).val());
    });
    SS = tmp.join("");
}
//设置错误报告
function setInfo() {
    var info = localStorage.getItem("info");
    if(info == 0){
        $("#swInfo p").text("参数错误！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == -1){
        $("#swInfo p").text("当前站点不在线！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == -2){
        $("#swInfo p").text("抱歉，您的权限不足，将无法控制终端，但允许您修改相关信息！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == -3){
        $("#swInfo p").text("响应超时！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == -4){
        $("#swInfo p").text("服务器没有响应！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == 1){
        $("#swInfo p").text("修改成功！");
        $("#swInfo").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == -5){
        $("#swInfo p").text("修改失败！");
        $("#swInfo").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }
    localStorage.removeItem("info");
}

function getUpdateName() {
    var names = [];
    $("#update tbody input").each(function (i, obj) {
        names[i] = obj.value;
    });
    updateButtonName(names);
}

function getSWState() {
    $.ajax({
        url:'../includes/switchProcess.php',
        data:"type=get&no="+no,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:2000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 0){
                localStorage.setItem("info",0);
            }else if(data == -1){
                localStorage.setItem("info",-1);
            }else if(data == -2){
                localStorage.setItem("info",-2);
            }else if(data == ""){
                localStorage.setItem("info",-3);
            }else{
                //初始化按钮状态
                initButton(data);
            }
            setInfo();
        },
        error:function(xhr,textStatus){
            //localStorage.setItem("info",-4);
            //初始化按钮状态
            //setInfo();
        },
        complete:function(){
            //
        }
    });
}

function setSWState() {
    $.ajax({
        url:'../includes/switchProcess.php',
        data:"type=set&no="+no+"&ss="+SS,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:2000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 0){
                localStorage.setItem("info",0);
            }else if(data == -1){
                localStorage.setItem("info",-1);
            }else if(data == -2){
                localStorage.setItem("info",-2);
            }else if(data == ""){
                localStorage.setItem("info",-3);
            }else{
                //初始化按钮状态
                initButton(data);
            }
            setInfo();
        },
        error:function(xhr,textStatus){
            //localStorage.setItem("info",-4);
            //setInfo();
        },
        complete:function(){
            //
        }
    });
}

function getButtonName() {
    $.ajax({
        url:'../includes/switchGetProcess.php',
        data:"no="+no,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            data = JSON.parse(data);
            var tag;
            //替换界面按钮名称
            for(var i = 0; i < 8; i++){
                if(i < 4)
                    tag = "#u1 li:nth-of-type("+(i+1)+") label";
                else
                    tag = "#u2 li:nth-of-type("+(i-3)+") label";
                $(tag).text(data[i]);
            }
            //替换更新按钮名称
            for(var i = 0; i < 8; i++){
                tag = "#update tbody tr:nth-of-type("+(i+1)+") td:nth-of-type(1)";
                $(tag).text(data[i]);
                tag = "#update tbody tr:nth-of-type("+(i+1)+") td input";
                $(tag).attr("placeholder",data[i]);
            }
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}


function updateButtonName(names) {
    $.ajax({
        url:'../includes/switchUpdateProcess.php',
        data:"no="+no+"&text="+names,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 1){
                localStorage.setItem("info",1);
            }else {
                localStorage.setItem("info",-5);
            }
            getButtonName();
            setInfo();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}
