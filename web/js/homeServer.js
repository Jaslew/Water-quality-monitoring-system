/**
 * Created by jaslew on 17-9-1.
 */
var pageNow = 1;
var pageRow = 5;
var noticeNum = 0;

$(document).ready(function(){
    //弹出框触发器
    $("[data-toggle='popover']").popover();
    $(".popover .fade .left .in ").addClass("animated flipInX");
    //获取天气
    getWeather();
    //获取当前时钟
    getTime();
    //获取通知信息
    getNotice();

    $("#notice .hleft").click(function () {
        if(pageNow > 1)
            pageNow--;
        getNotice();
    });
    $("#notice .hright").click(function () {
        if(pageNow < Math.ceil(noticeNum/pageRow))
            pageNow++;
        getNotice();
    });
});

function showNotice(tag, data){
    //首先清空 ul 下的数据结构
    $("#notice .n-content ul").empty();
    var $node = "";
    var Time;
    var ignore = "......"
    //如果有通知
    if(tag){
        noticeNum = data[data.length-1];
        for(var i = 0; i < data.length-1; i++){
            if(data[i]['isread'] == 1){
                $node = "<li><a class=\"isread\" data-toggle=\"modal\" href=\"#detail\" onclick=\"showNoticeModal('"+data[i].time+"','"+data[i].content+"')\">" +
                    "<i class=\"fa fa-envelope-open-o\"></i>";
            }else
                $node = "<li><a data-toggle=\"modal\" href=\"#detail\" onclick=\"showNoticeModal('"+data[i].time+"','"+data[i].content+"')\">" +
                    "<i class=\"fa fa-envelope-o\"></i>";
            Time = new Date(1000 * data[i].time);
            $node += "<span class=\"text\">"+data[i].content.slice(0,30);
            if(data[i].content.length >= 25)
                $node += ignore;
            $node += "</span><span class=\"time\">" +Html5DateFormate2(Time)+ "</span></a></li>";
            $("#notice .n-content ul").append($($node));
        }

        $("#notice .n-content ul a.isread, #notice .n-content ul a.isread i").css("color","#8080808c");

        $(".n-content a").hover(function () {
            $(".n-content a").css({"-webkit-transform":"scale(1.0)","color":"black"});
            $(".n-content a").css({"-moz-transform":"scale(1.0)","color":"black"});
            $(".n-content a").css({"-o-transform":"scale(1.0)","color":"black"});
            $(this).css({"-webkit-transform":"scale(1.01)","color":"#005cda"});
            $(this).css({"-moz-transform":"scale(1.01)","color":"#005cda"});
            $(this).css({"-o-transform":"scale(1.01)","color":"#005cda"});
            $("#notice .n-content ul a.isread, #notice .n-content ul a.isread i").css("color","#8080808c");
        });
        $(".n-content a").mouseleave(function () {
            $(".n-content a").css({"-webkit-transform":"scale(1.0)","color":"black"});
            $(".n-content a").css({"-moz-transform":"scale(1.0)","color":"black"});
            $(".n-content a").css({"-o-transform":"scale(1.0)","color":"black"});
            $("#notice .n-content ul a.isread, #notice .n-content ul a.isread i").css("color","#8080808c");
        })
    }else{
        //如果没有通知
        $node = $("<li></i>" +
            "<span class=\"text\">暂无通知！</span>" +
            "<span class=\"time\"></span></li>");
        $("#notice .n-content ul").append($node);
    }
    //分页按钮
    if(noticeNum > pageRow && pageNow == 1){
        $("#notice .hleft").css("display","none");
        $("#notice .hright").css("display","block");
    }else if(noticeNum > pageRow && pageNow < Math.ceil(noticeNum/pageRow)){
        $("#notice .hleft").css("display","block");
        $("#notice .hright").css("display","block");
    }else if(noticeNum <= pageRow){
        $("#notice .hleft").css("display","none");
        $("#notice .hright").css("display","none");
    }else{
        $("#notice .hleft").css("display","block");
        $("#notice .hright").css("display","none");
    }
}

function showNoticeModal(time, content) {
    $(".notice-text").html(content);
    $(".notice-time").html(Html5DateFormate2(new Date(time * 1000)));
    //给当前消息绑定一个监听事件
    var param = "delNotice('"+time+"')";
    $(".modal-footer button:nth-of-type(1)").attr("onclick",param);
    //将消息设置为已读
    setRead(time);
}

function getTime(){
    var d = new Date();
    var str = d.getFullYear();
    str += "年";
    str += (d.getMonth() + 1);
    str += "月";
    str += d.getDate();
    str += "日";
    $("#dayShow .date div:nth-of-type(1) p:nth-of-type(2)").html(str);
    var week = d.getDay();
    week = getWeek(week.toString());
    $("#dayShow .date div:nth-of-type(1) p:nth-of-type(1)").html(week);
    var str2 = d.getHours();
    str2 += ":";
    var min = d.getMinutes().toString();
    if(min.length == 1){
        min = "0" + min;
    }
    str2 += min;
    $("#dayShow .date div:nth-of-type(2) p:nth-of-type(1)").html(str2);
}

function delNotice(time) {
    $.ajax({
        url:'../includes/noticeDelProcess.php',
        data:"time="+time,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            getNotice();
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function getNotice() {
    var tag = 0;
    var pdata = "pageNow="+pageNow+"&pageRow="+pageRow;
    $.ajax({
        url:'../includes/noticeGetProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            if(data != 0)
                tag = 1;
            showNotice(tag, JSON.parse(data));
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function setRead(time) {
    var pdata = "time="+time;
    $.ajax({
        url:'../includes/noticeSetProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
           //
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}