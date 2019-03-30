
var showType;
var pageNow = 1;    //当前页的号码
var pageRow = 9;   //每页展示的记录数
var pageCount = 0;  //总页数
var pageData;

var imgFile = "";
var imgResetAPI;
var imgParam;

$(document).ready(function() {
    //是否隐藏增加用户按钮
    if(r != 1){
        $("#content form .form-group:nth-of-type(2) a").remove();
        $("#add").remove();
    }

    getShowType();
    getData();
    //绑定监听函数
    $("#content").find("select[name = 'showType']").change(function () {
        getShowType();
        getData();
    });

    $("#key-info .close").click(function () {
        $("#key-info").css({"display":"none"});
    });

    $("#key-edite-info .close").click(function () {
        $("#key-edite-info").css({"display":"none"});
    });

    $("#key-add-info .close").click(function () {
        $("#key-edite-info").css({"display":"none"});
    });

    // 实现裁剪
    jQuery(function($) {
        // 创建变量(在这个生命周期)的API和图像大小
        var jcrop_api = null, boundx, boundy,

            // 获取预览窗格相关信息
            $preview = $('#preview-pane'),
            $pcnt = $('#preview-pane .preview-container'),
            $pimg = $('#preview-pane .preview-container img'),

            xsize = $pcnt.width(), ysize = $pcnt.height();

        $('#target').Jcrop({
            // allowResize:false,
            onChange : updatePreview,
            onSelect : updatePreview,
            aspectRatio : xsize / ysize
        }, function() {
            // 使用API来获得真实的图像大小
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[1];
            // jcrop_api变量中存储API
            jcrop_api = this;

            // 预览进入jcrop容器css定位
            $preview.appendTo(jcrop_api.ui.holder);

            //初始化选框
            jcrop_api.animateTo([0,0,200,200]);
            //初始化图片参数
            imgParam = jcrop_api.tellSelect();

            imgResetAPI = jcrop_api;
        });

        //更新预览
        function updatePreview(c) {
            // 设置预览
            if (parseInt(c.w) > 0) {
                var rx = xsize / c.w;
                var ry = ysize / c.h;
                $pimg.css({
                    width : Math.round(rx * boundx) + 'px',
                    height : Math.round(ry * boundy) + 'px',
                    marginLeft : '-' + Math.round(rx * c.x) + 'px',
                    marginTop : '-' + Math.round(ry * c.y) + 'px'
                });
                //设置图片参数
                imgParam = jcrop_api.tellSelect();
            }
        }
    });

    $('#imgFile').change(function(event) {
        var jcrop_api;
        // 根据这个 <input> 获取文件的 HTML5 js对象
        var files = event.target.files, file;
        if (files && files.length > 0) {
            // 获取目前上传的文件
            file = files[0];
            if(file.size < 500000){
                if(/^image\/\w+$/.test(file.type)){
                    // 获取window的 URL工具
                    var URL = window.URL || window.webkitURL;
                    // 通过 file生成目标 url
                    var imgURL = URL.createObjectURL(file);
                    // 用这个URL产生一个 <img> 将其显示出来
                    if (jcrop_api) {
                        jcrop_api.setImage(imgURL);
                    }
                    $('#preview-content img').attr('src', imgURL);

                    imgFile = file;
                }else {
                    alert("请上传正确的图片格式");
                    $("#imgFile").val("");
                }
            }else {
                alert("图片大小需小于500KB");
                $("#imgFile").val("");
            }
        }
    });
});

function setInfo() {
    var info = localStorage.getItem("info");
    if(info == "keyEditeSuccess"){
        $("#key-info p").text("用户更新成功！");
        $("#key-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "keyEditeFail"){
        $("#key-info p").text("用户更新失败！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "keyAddSuccess"){
        $("#key-info p").text("用户增加成功！");
        $("#key-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "keyAddFail"){
        $("#key-info p").text("用户增加失败！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "keyRemoveSuccess"){
        $("#key-info p").text("用户移除成功！");
        $("#key-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "keyRemoveFail"){
        $("#key-info p").text("用户移除失败！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "keyFail"){
        $("#key-info p").text("抱歉，您没有此权限！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "header1"){
        $("#key-info p").text("请上传正确的图片格式！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "header2"){
        $("#key-info p").text("图片大小需小于 500 KB！");
        $("#key-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }

    localStorage.removeItem("info");
}

//下一页
function pageNext() {
    if(pageNow < pageCount)
        pageNow++;
    getData();

}

//上一页
function pageBefore() {
    if(pageNow > 1)
        pageNow--;
    getData();

}

//获取当前显示方式
function getShowType(){
    showType =  $("#content").find("select[name = 'showType']").val();
    pageNow = 1;
}

//更新页面数据
function pageUpdate(tag) {
    //清空数据
    $(".table-content table tbody").empty();
    //装填数据
    if(tag == 1 && pageData){
        var len = pageData.length-1;
        pageCount =Math.ceil(pageData[pageData.length-1]/pageRow);
        $(".bottom-bar .bottom-bar-left span").text("共 "+pageData[pageData.length-1]+" 条记录，当前第 "+pageNow+"/"+pageCount+" 页");

        for(i = 0; i < len; i++){
            var Role = pageData[i].roleid == 1 ? "系统管理员":(pageData[i].roleid == 2 ? "普通管理员" : "普通用户");
            var Time = new Date(pageData[i].lasttime * 1000);
            var header = pageData[i].header ? pageData[i].header : "header.png";
            str = "<tr><td> "+"<img src='../images/header/"+header+"?t="+parseInt(Math.random()*10)+"' >"+"</td><td>"+pageData[i].id+"</td>";
            str += "<td>"+pageData[i].name+"</td>";
            str += "<td>"+Role+"</td><td>"+Html5DateFormate2(Time)+"</td>";
            str += "<td><a data-toggle=\"modal\" href=\"#detail\" onclick=\"keyInfo("+i+")\"><i class=\"fa fa-info\"></i></a>|\n" +
                "<a data-toggle=\"modal\" href=\"#edite\" onclick=\"keyEdite("+i+")\" ><i class=\"fa fa-edit\"></i></a>|\n" +
                "<a data-toggle=\"modal\" href=\"#remove\" onclick=\"keyRemove("+i+")\"><i class=\"fa fa-times\"></i></a></td></tr>";
            $(".table-content table tbody").append($(str));
        }
        if(pageNow <= 1){
            $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
        }else{
            $(".pageMenu a:nth-of-type(2)").css({"display":"block"});
        }
        if(pageNow >= pageCount){
            $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
        }else{
            $(".pageMenu a:nth-of-type(1)").css({"display":"block"});
        }
    }else{
        //清空数据
        $(".table-content table tbody").empty();
        $(".bottom-bar .bottom-bar-left span").text("共 0 条记录，当前第 0/0 页");
        //隐藏翻页按钮
        $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
        $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
    }
}

//获取列表数据
function getData() {
    var pdata = "showType="+showType+"&pageNow="+pageNow+"&pageRow="+pageRow;
    $.ajax({
        url:'../includes/keyQueryProcess.php',
        data:pdata,
        type:'POST', //POST
        async:false,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            pageData = JSON.parse(data);
            if(pageData != 0){
                pageUpdate(1);
            }else{
                pageUpdate(0);
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

function keyInfo(no) {
    var Role = pageData[no].roleid == 1 ? "系统管理员":(pageData[no].roleid == 2 ? "普通管理员" : "普通用户");
    var Time = new Date(pageData[no].lasttime * 1000);
    $("#detail table thead tr td:nth-of-type(2)").html(pageData[no].id);
    $("#detail table tbody tr:nth-of-type(1) td:nth-of-type(2)").html(pageData[no].name);
    $("#detail table tbody tr:nth-of-type(2) td:nth-of-type(2)").html(Role);
    $("#detail table tbody tr:nth-of-type(3) td:nth-of-type(2)").html(pageData[no].tel);
    $("#detail table tbody tr:nth-of-type(4) td:nth-of-type(2)").html(pageData[no].email);
    $("#detail table tbody tr:nth-of-type(5) td:nth-of-type(2)").html(Html5DateFormate2(Time));
}

function keyEdite(no) {
    var imgSrc = pageData[no].header ? "../images/header/"+pageData[no].header : "../images/header/header.png";
    imgSrc += "?t="+parseInt(Math.random()*10);
    $("#edite table tbody tr:nth-of-type(1) td input").val(pageData[no].id);
    $("#edite table tbody tr:nth-of-type(2) td input").val(pageData[no].name);
    if(r != 1){
        var text = r == 2 ? "普通管理员" : "普通用户";
        $("#edite table tbody tr:nth-of-type(3) td").css({"display":"table-cell"});
        $("#edite table tbody tr:nth-of-type(4) td").css({"display":"none"});
        $("#edite table tbody tr:nth-of-type(3) td:nth-of-type(2)").text(text);
    }
    if(pageData[no].roleid == 1){
        $("#edite table tbody tr:nth-of-type(3) td").css({"display":"table-cell"});
        $("#edite table tbody tr:nth-of-type(4) td").css({"display":"none"});
        $("#edite table tbody tr:nth-of-type(3) td:nth-of-type(2)").text("系统管理员");
    }else if(r == 1){
        $("#edite table tbody tr:nth-of-type(3) td").css({"display":"none"});
        $("#edite table tbody tr:nth-of-type(4) td").css({"display":"table-cell"});
        $("#edite table tbody tr:nth-of-type(4) td select").val(pageData[no].roleid);
    }
    $("#edite table tbody tr:nth-of-type(5) td input").val(pageData[no].tel);
    $("#edite table tbody tr:nth-of-type(6) td input").val(pageData[no].email);
    $("#edite table tbody tr:nth-of-type(8) td img").attr("src",imgSrc);

    $("a.reset").click(function () {
        //重置选框图片
        imgResetAPI.setImage(imgSrc);
        imgFile = "";
        $("#imgFile").val("");
        $('#preview-content img').attr('src', imgSrc);
        //初始化选框
        imgResetAPI.animateTo([0,0,200,200]);
        //初始化图片参数
        imgParam = imgResetAPI.tellSelect();
    });
}

function keyRemove(no) {
    $("#remove .modal-body p").text("您确定要移除ID为 "+pageData[no].id+" 的用户吗？此操作不可撤销，请慎重操作！");
    $("#remove .modal-body input").val(pageData[no].id);
}

function addOK() {
    var pdata;
    var name = $("#add table tbody tr:nth-of-type(1) td input").val();
    var role = $("#add table tbody tr:nth-of-type(2) td select").val();
    var tel = $("#add table tbody tr:nth-of-type(3) td input").val();
    var email = $("#add table tbody tr:nth-of-type(4) td input").val();
    var password = $("#add table tbody tr:nth-of-type(5) td input").val();

    //过滤
    if(!(name && role && password)){
        $("#add #key-add-info p").text("您的数据有误，请核对后再提交！");
        $("#add #key-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(!(role == 2 || role == 3)){
        $("#add #key-add-info p").text("角色选择有误！");
        $("#add #key-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else{
        pdata = "name="+name+"&roleid="+role+"&tel="+tel+"&email="+email+"&password="+password;
        addKey(pdata);
        $("#add .modal-footer button:nth-of-type(1)").attr("data-dismiss","modal");
        var timeOut = setTimeout(function () {
            $("#add .modal-footer button:nth-of-type(1)").removeAttr("data-dismiss");
            $("#add #key-add-info").css({"display":"none"});
            clearTimeout(timeOut);
        },1000);
    }
}

function editeOK() {
    var formData;
    var id = $("#edite table tbody tr:nth-of-type(1) td input").val();
    var name = $("#edite table tbody tr:nth-of-type(2) td input").val();
    var role = $("#edite table tbody tr:nth-of-type(4) td select").val();
    var tel = $("#edite table tbody tr:nth-of-type(5) td input").val();
    var email = $("#edite table tbody tr:nth-of-type(6) td input").val();
    var password = $("#edite table tbody tr:nth-of-type(7) td input").val();

    if(!(id && name && role)){
        $("#edite #key-edite-info p").text("您的数据有误，请核对后再提交！");
        $("#edite #key-edite-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(!(role == 2 || role == 3)){
        $("#edite #key-edite-info p").text("角色选择有误！");
        $("#edite #key-edite-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else{
        formData = new FormData();
        formData.append("id", id);
        formData.append("name", name);
        formData.append("roleid", role);
        formData.append("tel", tel);
        formData.append("email",email);
        formData.append("imgParam", JSON.stringify(imgParam));
        if(imgFile != ""){
            formData.append("imgFile", imgFile);
        }
        if(password != "")
            formData.append("password", password);
        editeKey(formData);
        $("#edite .modal-footer button:nth-of-type(1)").attr("data-dismiss","modal");
        var timeOut = setTimeout(function () {
            $("#edite .modal-footer button:nth-of-type(1)").removeAttr("data-dismiss");
            $("#edite #key-edite-info").css({"display":"none"});
            clearTimeout(timeOut);
        },1000);
    }
}

function editeKey(pdata) {
    $.ajax({
        url:'../includes/keyEditeProcess.php',
        data:pdata,
        type:'POST', //POST
        async:false,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        success:function(data,textStatus,jqXHR){
            if(data == 0){
                localStorage.setItem("info","keyEditeFail");
            }else if(data == -1){
                localStorage.setItem("info","keyFail");
            }else if(data == 8){
                localStorage.setItem("info","header1");
            }else if(data == 9){
                localStorage.setItem("info","header2");
            }else{
                localStorage.setItem("info","keyEditeSuccess");
            }
            setInfo();
            getData();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function doRemove() {
    var readyId = $("#remove .modal-body input").val()
    $.ajax({
        url:'../includes/keyRemoveProcess.php',
        data:"readyId="+readyId,
        type:'POST', //POST
        async:false,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 0){
                localStorage.setItem("info","keyRemoveFail");
            }else if(data == -1){
                localStorage.setItem("info","keyFail");
            }else{
                localStorage.setItem("info","keyRemoveSuccess");
            }
            setInfo();
            getData();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function addKey(pdata) {
    $.ajax({
        url:'../includes/keyAddProcess.php',
        data:pdata,
        type:'POST', //POST
        async:false,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 0){
                localStorage.setItem("info","keyAddFail");
            }else if(data == -1){
                localStorage.setItem("info","keyFail");
            }else{
                localStorage.setItem("info","keyAddSuccess");
            }
            setInfo();
            getData();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}