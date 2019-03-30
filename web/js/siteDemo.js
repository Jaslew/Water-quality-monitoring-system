/**
 * Created by Jaslew on 2017/8/2.
 */

var pageNow = 1;    //当前页的号码
var pageRow = 10;   //每页展示的记录数
var pageCount = 0;  //总页数
var pageData;

$(document).ready(function(){
    getStation();
    $("#site-info .close").click(function () {
        $("#site-info").css({"display":"none"});
    });
    $("#site-edite-info .close").click(function () {
        $("#site-edite-info").css({"display":"none"});
    });
    $("#site-add-info .close").click(function () {
        $("#site-add-info").css({"display":"none"});
    });
});

function setInfo() {
    var info = localStorage.getItem("info");
    if(info == "updateSuccess"){
        $("#site-info p").text("站点更新成功！");
        $("#site-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "updateFail"){
        $("#site-info p").text("站点更新失败！");
        $("#site-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "addSuccess"){
        $("#site-info p").text("站点增加成功！");
        $("#site-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "addFail"){
        $("#site-info p").text("站点增加失败！");
        $("#site-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "removeSuccess"){
        $("#site-info p").text("站点移除成功！");
        $("#site-info").removeClass().addClass("alert alert-success").css({"display":"block"});
    }else if(info == "removeFail"){
        $("#site-info p").text("站点移除失败！");
        $("#site-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }else if(info == "Fail"){
        $("#site-info p").text("抱歉，您没有此权限！");
        $("#site-info").removeClass().addClass("alert alert-danger").css({"display":"block"});
    }

    localStorage.removeItem("info");
}

//下一页
function pageNext() {
    if(pageNow < pageCount)
        pageNow++;
    getStation();

}

//上一页
function pageBefore() {
    if(pageNow > 1)
        pageNow--;
    getStation();

}

function pageUpdate(tag) {
    //清空数据
    $(".main tbody").empty();
    //装填数据
    if(tag == 1 && pageData){
        var len = pageData.length-1;
        var state;
        pageCount =Math.ceil(pageData[pageData.length-1]/pageRow);
        $(".bottom-bar .bottom-bar-left span").text("共 "+pageData[pageData.length-1]+" 条记录，当前第 "+pageNow+"/"+pageCount+" 页");
        for(i = 0; i < len; i++){
            state = (Date.parse(new Date())/1000 -pageData[i].time) < 300 ? "<td style='color: #5cb85c'>在线</td>":"<td style='color: red'>离线</td>";
            str = "<tr><td><input type=\"checkbox\" name=\"selects\" class=\"mgc mgc-success\"></td>";
            str += "<td>"+pageData[i].no+"</td><td>"+pageData[i].name+"</td>"+state;
            str += "<td>"+pageData[i].pos+"</td><td>"+pageData[i].charge+"</td><td>"+pageData[i].tel+"</td>";
            str += "<td>"+pageData[i].email+"</td>";
            str += "<td><a data-toggle=\"modal\" href=\"#detail\" onclick=\"changeDetail("+i+")\"><i class=\"fa fa-info\"></i></a></td>";
            str += "<td><a data-toggle=\"modal\" href=\"#edite\" onclick=\"changeEdite("+i+")\"><i class=\"fa fa-edit\"></i></a></td></tr>";
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

        $(".table-content .main thead").find("input[name = 'selects']").change(function(){
            theadChange('.main ')
        });
        $(".table-content .main tbody").find("input[name = 'selects']").change(function(){
            tbodyChange('.main ')
        });
    }else{
        //清空数据
        $(".main tbody").empty();
        $(".bottom-bar .bottom-bar-left span").text("共 0 条记录，当前第 0/0 页");
        //隐藏翻页按钮
        $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
        $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
    }
}

//获取站点信息
function getStation() {
    var pdata = "pageNow="+pageNow+"&pageRow="+pageRow;
    $.ajax({
        url:'../includes/stationQueryProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
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

function changeDetail(i) {
    var ctime = new Date(pageData[i].ctime * 1000);
    var atime = new Date(pageData[i].atime * 1000);
    $("#detail thead tr:nth-of-type(1) td:nth-of-type(2)").text(pageData[i].no);
    $("#detail tbody tr:nth-of-type(1) td:nth-of-type(2)").text(pageData[i].name);
    $("#detail tbody tr:nth-of-type(2) td:nth-of-type(2)").text(pageData[i].pos);
    $("#detail tbody tr:nth-of-type(3) td:nth-of-type(2)").text(pageData[i].charge);
    $("#detail tbody tr:nth-of-type(4) td:nth-of-type(2)").text(pageData[i].tel);
    $("#detail tbody tr:nth-of-type(5) td:nth-of-type(2)").text(pageData[i].email);
    $("#detail tbody tr:nth-of-type(6) td:nth-of-type(2)").text(pageData[i].hz);
    $("#detail tbody tr:nth-of-type(7) td:nth-of-type(2)").text(pageData[i].ip);
    $("#detail tbody tr:nth-of-type(8) td:nth-of-type(2)").text(pageData[i].port);
    $("#detail tbody tr:nth-of-type(9) td:nth-of-type(2)").
    text(ctime.getFullYear()+"/"+(ctime.getMonth()+1)+"/"+ctime.getDate()
    +" "+ctime.getHours()+":"+ctime.getMinutes()+":"+ctime.getSeconds());
    $("#detail tbody tr:nth-of-type(10) td:nth-of-type(2)").
    text(atime.getFullYear()+"/"+(atime.getMonth()+1)+"/"+atime.getDate()
    +" "+atime.getHours()+":"+atime.getMinutes()+":"+atime.getSeconds());
}

function changeEdite(i) {
    $("#edite tbody tr:nth-of-type(1) td:nth-of-type(2) input").val(pageData[i].no);
    $("#edite tbody tr:nth-of-type(2) td:nth-of-type(2) input").val(pageData[i].name);
    $("#edite tbody tr:nth-of-type(3) td:nth-of-type(2) input").val(pageData[i].pos);
    $("#edite tbody tr:nth-of-type(4) td:nth-of-type(2) input").val(pageData[i].charge);
    $("#edite tbody tr:nth-of-type(5) td:nth-of-type(2) input").val(pageData[i].tel);
    $("#edite tbody tr:nth-of-type(6) td:nth-of-type(2) input").val(pageData[i].email);
    $("#edite tbody tr:nth-of-type(7) td:nth-of-type(2) input").val(pageData[i].hz);
    $("#edite tbody tr:nth-of-type(8) td:nth-of-type(2) input").val("");
    $("#edite tbody tr:nth-of-type(9) td:nth-of-type(2) input").val("");
}

function editeOK() {
    var pdata;
    var no = $("#edite tbody tr:nth-of-type(1) td:nth-of-type(2) input").val();
    var name = $("#edite tbody tr:nth-of-type(2) td:nth-of-type(2) input").val();
    var pos = $("#edite tbody tr:nth-of-type(3) td:nth-of-type(2) input").val();
    var charge = $("#edite tbody tr:nth-of-type(4) td:nth-of-type(2) input").val();
    var tel = $("#edite tbody tr:nth-of-type(5) td:nth-of-type(2) input").val();
    var email = $("#edite tbody tr:nth-of-type(6) td:nth-of-type(2) input").val();
    var hz = $("#edite tbody tr:nth-of-type(7) td:nth-of-type(2) input").val();

    //过滤,检查no号，检查采样频率
    var i = 0;
    var ishz = /^\d+$/.test(hz);
    while(pageData[i] && pageData[i].hasOwnProperty("no") && pageData[i].no != no)
        i++;
    if(!(no && name && pos && charge && tel && email)){
        $("#edite #site-edite-info p").text("您还有选项未填！");
        $("#edite #site-edite-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(pageData[i] && !pageData[i].hasOwnProperty("no")){
        $("#edite #site-edite-info p").text("您输入的站点号有误！");
        $("#edite #site-edite-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(!ishz){
        $("#edite #site-edite-info p").text("请输入正确的采样频率！");
        $("#edite #site-edite-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else{
        pdata = "no="+no+"&name="+name+"&pos="+pos+"&charge="+
            charge+"&tel="+tel+"&email="+email+"&hz="+hz;
        stationUpdate(pdata);
        $("#edite .modal-footer button:nth-of-type(1)").attr("data-dismiss","modal");
        var timeOut = setTimeout(function () {
            $("#edite .modal-footer button:nth-of-type(1)").removeAttr("data-dismiss");
            $("#edite #site-edite-info").css({"display":"none"});
            clearTimeout(timeOut);
        },1000);
    }

}

function addOK() {
    var pdata;
    var no = $("#add tbody tr:nth-of-type(1) td:nth-of-type(2) input").val();
    var name = $("#add tbody tr:nth-of-type(2) td:nth-of-type(2) input").val();
    var pos = $("#add tbody tr:nth-of-type(3) td:nth-of-type(2) input").val();
    var charge = $("#add tbody tr:nth-of-type(4) td:nth-of-type(2) input").val();
    var tel = $("#add tbody tr:nth-of-type(5) td:nth-of-type(2) input").val();
    var email = $("#add tbody tr:nth-of-type(6) td:nth-of-type(2) input").val();
    var hz = $("#add tbody tr:nth-of-type(7) td:nth-of-type(2) input").val();

    //过滤,检查no号，检查采样频率
    var i = 0;
    var isno = /^\d{3}$/.test(no);
    var ishz = /^\d+$/.test(hz);
    while(pageData[i] && pageData[i].hasOwnProperty("no") && pageData[i].no != no)
        i++;
    if(!(no && name && hz)){
        $("#add #site-add-info p").text("您的数据有误，请核对后再提交！");
        $("#add #site-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(!isno){
        $("#add #site-add-info p").text("站点号输入有误，必须是000~999的三位数字！");
        $("#add #site-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(pageData[i] && pageData[i].hasOwnProperty("no")){
        $("#add #site-add-info p").text("当前站点已存在！");
        $("#add #site-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else if(!ishz){
        $("#add #site-add-info p").text("请输入正确的采样频率！");
        $("#add #site-add-info").removeClass().addClass("alert alert-warning").css({"display":"block"});
    }else{
        pdata = "no="+no+"&name="+name+"&pos="+pos+"&charge="+
            charge+"&tel="+tel+"&email="+email+"&hz="+hz;
        stationAdd(pdata);
        $("#add .modal-footer button:nth-of-type(1)").attr("data-dismiss","modal");
        var timeOut = setTimeout(function () {
            $("#add .modal-footer button:nth-of-type(1)").removeAttr("data-dismiss");
            $("#add #site-add-info").css({"display":"none"});
            clearTimeout(timeOut);
        },1000);
    }

}

function getChecked() {
    var len = $(".main tbody").find("input[name = 'selects']").length;
    var i = 0, j = 0, temp;
    var check = new Array();
    for(i; i < len; i++){
        temp = $(".main tbody").find("input[name = 'selects']")[i].checked;
        if(temp)
            check[j++] = i;
    }
    return check;
}

function readyRemove() {
    /*
        check数组键名为数字，长度为已选个数，值为在列表中的序号
     */
    var check = getChecked();
    if(check.length == 0){
        $("#remove .modal-body p").html("您选择了 0 个站点，请先选择要移除的站点");
    }else{
        $("#remove .modal-body p").html("您确定要移除所选的 "+check.length+" 个站点吗？<br>该操作将无法撤销,请慎重操作!");
    }
    $("#remove").modal();
}

function doRemove() {
    var check = getChecked();
    if(check.length > 0){
        var i,j = 0;
        var noList = new Array(check);
        var str = "";
        var pdata = "a=1";
        for(i = 0; i < check.length; i++){
            str = ".main tbody tr:nth-of-type("+(check[i]+1)+") td:nth-of-type(2)";
            noList[i] = $(str).text();
        }
        //noList为待删除站点号(数组)
        //初步检查站点号是否合法
        var isin = true;
        for(i = 0; i < noList.length && isin; i++){
            isin = false;
            for(j = 0; j < pageData.length-1; j++){
                if(noList[i] == pageData[j].no){
                    isin = true;
                    //拼接POST格式数据包
                    pdata += "&no"+i+"="+noList[i];
                }
            }
        }
        if(isin){
            //站点都合法
            stationRemove(pdata);
        }else {
            //有站点不合法
            localStorage.setItem("info","removeFail");
            location = location;
        }
    }
}


function stationUpdate(pdata) {
    $.ajax({
        url:'../includes/stationUpdateProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 1){
                localStorage.setItem("info","updateSuccess");
            }else if(data == 2){
                localStorage.setItem("info","Fail");
            }else{
                localStorage.setItem("info","updateFail");
            }
            setInfo();
            getStation();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}


function stationAdd(pdata) {
    $.ajax({
        url:'../includes/stationAddProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 1){
                localStorage.setItem("info","addSuccess");
            }else if(data == 2){
                localStorage.setItem("info","Fail");
            }else{
                localStorage.setItem("info","addFail");
            }
            setInfo();
            getStation();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function stationRemove(pdata) {
    $.ajax({
        url:'../includes/stationRemoveProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data == 1){
                localStorage.setItem("info","removeSuccess");
            }else if(data == 2){
                localStorage.setItem("info","Fail");
            }else{
                localStorage.setItem("info","removeFail");
            }
            setInfo();
            getStation();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}











