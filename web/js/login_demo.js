/**
 * Created by jaslew on 17-9-1.
 */
var IMG;
var start = 0;
var len;
$(document).ready(function(){
    $.ajax({
        url:'https://api.asilu.com/bg/',
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'JSONP',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            IMG = data;
            len = getL(data.images);
            //设置背景图片第一页
            setImg(IMG.images[start])
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });

    $(".changeMenu .left").click(function(){
        if(start != 0){
            start -= 1;
        }else{
            start = len - 1;
        }
        setImg(IMG.images[start]);
    });

    $(".changeMenu .right").click(function(){
        if(start != len - 1){
            start += 1;
        }else{
            start = 0;
        }
        setImg(IMG.images[start]);
    });

    $("input").focus(function () {
        var TimeOut = setTimeout(function () {
            $(".bg").css({"background":"rgba(0, 0, 0, 0.6)"});
        },100);
    });
    $("input").blur(function () {
        var TimeOut = setTimeout(function () {
            $(".bg").css({"background":"rgba(0, 0, 0, 0)"});
        },100);
    });

    $(".btn").click(function () {
        var id = $("#id").val();
        var password = $("#password").val();
        if(id == ""){
            $("#id").attr("placeholder","请先输入用户名!");
            $(".login-box .form-group:nth-of-type(1)").addClass("has-error");
        }
        if(password == ""){
            $("#password").attr("placeholder","请先输入密码!");
            $(".login-box .form-group:nth-of-type(2)").addClass("has-error");
        }
        if(id != "" && password != ""){
            $.ajax({
                url:'includes/loginProcess.php',
                data:'id='+id+'&password='+password,
                type:'POST', //POST
                async:true,    //或false,是否异步
                timeout:1000,    //超时时间
                dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
                success:function(data,textStatus,jqXHR){
                    if(data != 1){
                        $(".login-box .form-group:nth-of-type(1)," +
                            ".login-box .form-group:nth-of-type(2)").addClass("has-error");
                        $("#password").val("").attr("placeholder","用户名或密码有误!");
                    }else {
                        window.location.href = "main.php";
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
    });
});


function setImg(obj) {
    //移除淡入;淡出
    $("#backgroundIMG").removeClass("fadeIn").addClass("fadeOut");
    //置入新图
    setTimeout(function () {
        $("#backgroundIMG").attr("src",obj.url);
    },1000);
    //图片就绪
    $("#backgroundIMG").load(function () {
        //移除淡出;淡入
        $("#backgroundIMG").removeClass("fadeOut").addClass("fadeIn");
    });
    var date = obj.startdate;
    $(".botMenu span").html(date.slice(0,4)+"/"+date.slice(4,6)+"/"+date.slice(6,8)+" "+obj.copyright+"--From cn.bing.com"+"--江南大学水质监控平台");
}

function getL(jsonObj) {
    var Length = 0;
    for (var item in jsonObj) {
        Length++;
    }
    return Length;
}





