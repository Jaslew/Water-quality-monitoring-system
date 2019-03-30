/**
 * Created by Jaslew on 2017/9/3.
 */
var pageNow = 1;    //当前页的号码
var pageRow = 10;   //每页展示的记录数
var pageCount = 0;  //总页数

//检测指标
var options = new Array("水温( ℃)","pH","溶解氧 (mg/L)","电导率 (μS/cm)",
    "浊度 (NTU)","氯含量","总磷 (mg/L)","氨氮 (mg/L)","碳含量");

//当前页数据
var pageData = 0;
var start,end,no;
var reg = /^2\d{3}$/;

$(document).ready(function(){
    $("#export").click(function(){
        if($('.exportMenu').css("display") == "none"){
            $('.exportMenu').css("display","block");
        }
        if($('.exportMenu').hasClass("flipInY") == true){
            $('.exportMenu').removeClass("flipInY").addClass("flipOutY");
        }else{
            $('.exportMenu').removeClass("flipOutY").addClass("flipInY");
        }
    });
    $(".exportMenu ul li:nth-of-type(1)").click(function(){
        $('#exportData').tableExport({type:'excel',escape:'false'});
        $('.exportMenu').removeClass("flipInY").addClass("flipOutY");
    });
    $(".exportMenu ul li:nth-of-type(2)").click(function(){
        $('#exportData').tableExport({type:'doc',escape:'false'});
        $('.exportMenu').removeClass("flipInY").addClass("flipOutY");
    });

    //初始化日期选择框
    var date = Html5DateFormate(new Date());
    $("#dataShow").find("input[name = 'startDate']").val(date);
    $("#dataShow").find("input[name = 'endDate']").val(date);
    $("#dataShow").find("input[name = 'option']").attr("checked","checked");

    //初始化一次报表视图
    initParam();
    getData();

    //绑定监听函数
    $("#dataShow").find("select[name = 'siteno']").change(function () {
        initParam();
        getData();
    });
    $("#dataShow").find("input[name = 'option']").change(function () {
        pageUpdate(1);
    });
    $("#dataShow").find("input[name = 'startDate']").blur(function () {
        var date = $("#dataShow").find("input[name = 'startDate']").val();
        if(reg.test(date.slice(0,4))){
            initParam();
            getData();
        }
    });
    $("#dataShow").find("input[name = 'endDate']").blur(function () {
        var date = $("#dataShow").find("input[name = 'endDate']").val();
        if(reg.test(date.slice(0,4))){
            initParam();
            getData();
        }
    });
});

//初始化分页参数
function initParam() {
    start =  $("#dataShow").find("input[name = 'startDate']").val();
    end = $("#dataShow").find("input[name = 'endDate']").val();
    no = $("#dataShow").find("select[name = 'siteno']").val();
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

//首页
function pageStart() {
    if(pageNow != 1)
        pageNow = 1;
    getData();

}

//末页
function pageEnd() {
    if(pageNow != pageCount)
        pageNow = pageCount;
    getData();

}

//更新当前页数为第 pn 页
function pageUpdate(tag) {
    var str = "<tr><th> 时间</th>";
    var Time,headNode;
    for (var i = 0; i < options.length; i++){
        if($("#dataShow").find("input[name = 'option']")[i].checked){
            str += "<th> "+options[i]+"</th>";
        }
    }
    str += "</tr>";
    headNode = $(str);
    //清空标题头
    $("#exportData thead").empty().append(headNode);
    //清空数据
    $("#exportData tbody").empty();
    //装填数据
    if(tag == 1 && pageData != 0){
        var len = pageData.length-1;
        pageCount =Math.ceil(pageData[pageData.length-1]/pageRow);
        $("#exportData caption").text("小时数据表 当前 "+pageNow+"/"+pageCount+" 页");

        for(i = 0; i < len; i++){
            Time = new Date(pageData[i].time * 1000);
            str = "<tr><td> "+Html5DateFormate2(Time)+"</td>";

            if($("#dataShow").find("input[name = 'option']")[0].checked)
                str += "<td> "+pageData[i].tm+"</td>";
            if($("#dataShow").find("input[name = 'option']")[1].checked)
                str += "<td> "+pageData[i].ph+"</td>";
            if($("#dataShow").find("input[name = 'option']")[2].checked)
                str += "<td> "+pageData[i].ox+"</td>";
            if($("#dataShow").find("input[name = 'option']")[3].checked)
                str += "<td> "+pageData[i].el+"</td>";
            if($("#dataShow").find("input[name = 'option']")[4].checked)
                str += "<td> "+pageData[i].nt+"</td>";
            if($("#dataShow").find("input[name = 'option']")[5].checked)
                str += "<td> "+pageData[i].cl+"</td>";
            if($("#dataShow").find("input[name = 'option']")[6].checked)
                str += "<td> "+pageData[i].po+"</td>";
            if($("#dataShow").find("input[name = 'option']")[7].checked)
                str += "<td> "+pageData[i].nh+"</td>";
            if($("#dataShow").find("input[name = 'option']")[8].checked)
                str += "<td> "+pageData[i].ca+"</td>";
            str += "</tr>";
            $("#exportData tbody").append($(str));
        }
        // 1->末页,2->下一页,3->上一页,4->首页
        if(pageNow <= 1 && pageNow == pageCount){
            $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(3)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(4)").css({"display":"none"});
        }else if(pageNow <= 1 && pageNow != pageCount){
            $(".pageMenu a:nth-of-type(1)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(2)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(3)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(4)").css({"display":"none"});
        }else if(pageNow > 1 && pageNow != pageCount){
            $(".pageMenu a:nth-of-type(1)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(2)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(3)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(4)").css({"display":"block"});
        }else if(pageNow > 1 && pageNow == pageCount){
            $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
            $(".pageMenu a:nth-of-type(3)").css({"display":"block"});
            $(".pageMenu a:nth-of-type(4)").css({"display":"block"});
        }
    }else{
        pageNow = 1;
        $("#exportData caption").text("小时数据表 当前 "+0+"/"+0+" 页");
        //隐藏翻页按钮
        $(".pageMenu a:nth-of-type(1)").css({"display":"none"});
        $(".pageMenu a:nth-of-type(2)").css({"display":"none"});
        $(".pageMenu a:nth-of-type(3)").css({"display":"none"});
        $(".pageMenu a:nth-of-type(4)").css({"display":"none"});
    }
}

//获取列表数据
function getData() {
    var pdata = "pageNow="+pageNow+"&pageRow="+pageRow+"&start="+start+"&end="+end+"&no="+no+"&belongid="+belongid;
    $.ajax({
        url:'../includes/dataQueryProcess.php',
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