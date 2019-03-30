
function getWeather(){
    $.ajax({
        url:'https://api.asilu.com/weather_v2/',
        type:'GET', //GET
        async:true,    //或false,是否异步
        timeout:5000,    //超时时间
        dataType:'JSONP',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            setWeather(data.forecasts[0]);
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function setWeather(data){
    //天气常规显示
    $(".weather div:nth-of-type(1) p span").html(data.casts[0].daytemp+" ℃");
    $(".weather div:nth-of-type(1) span:nth-of-type(2)").html(data.city);
    var src0 = getWState(data.casts[0].dayweather);
    $(".weather div:nth-of-type(1) p img").attr("src",src0);
    var i = 1;
    for(i; i <=3 ; i++){
        var w = getWeek(data.casts[i].week);
        var src = getWState(data.casts[i].dayweather);
        $(".weather div:nth-of-type("+(i+1)+") span:nth-of-type(1)").html(w);
        $(".weather div:nth-of-type("+(i+1)+") img").attr("src",src);
        $(".weather div:nth-of-type("+(i+1)+") span:nth-of-type(2)").html(data.casts[i].daytemp+" ℃");
    }
    //天气弹出框
    for(var j = 0; j <= 3 ; j++){
        var $html = "<p>"+data.city+"<span>"+data.reporttime.substr(11,5)+"更新</span></p>";
        $html += "<div class='pngBox'> <img src='"+getWState(data.casts[j].dayweather)+"' width='60' height='60'></div>";
        $html += "<div class='cenBox'><span>"+data.casts[j].daytemp+"</span>";
        $html += "<ul> <li><strong>℃</strong></li> <li style='font-size: 18px;'>"+data.casts[j].dayweather+"</li></ul></div>";
        $html+= "<ul class='botBox'><li>风向： "+data.casts[j].daywind+" ("+data.casts[j].daypower+"级)"+"</li>";
        $html += "<li>夜间： "+data.casts[j].nightweather+" ("+data.casts[j].nighttemp+"℃)"+"</li></ul>";
        $(".weather div:nth-of-type("+(j+1)+")").attr("data-content",$html);
    }
}

function getWeek(week) {
    switch (week){
        case '1' :{
            return "星期一";
            break;
        }
        case '2':{
            return "星期二";
            break;
        }
        case '3':{
            return "星期三";
            break;
        }
        case '4':{
            return "星期四";
            break;
        }
        case '5':{
            return "星期五";
            break;
        }
        case '6':{
            return "星期六";
            break;
        }
        case '7':{
            return "星期日";
            break;
        }
        case '0':{
            return "星期日";
        }
    }
}

function getWState(str){
    var src;
    if(str.indexOf("晴") >= 0){
        src = "../images/qing.png";
    }else if(str.indexOf("雷") >= 0){
        src = "../images/leizhenyu.png";
    }else if(str.indexOf("雨") >= 0){
        src = "../images/yu.png";
    }else if(str.indexOf("云") >= 0){
        src = "../images/yun.png";
    }else if(str.indexOf("阴") >= 0){
        src = "../images/yin.png";
    }else if(str.indexOf("雪") >= 0){
        src = "../images/xue.png";
    }else if(str.indexOf("雾") >= 0){
        src = "../images/wu.png";
    }else if(str.indexOf("沙") >= 0 || str.indexOf("尘") >= 0 || str.indexOf("霾") >= 0){
        src = "../images/sha.png";
    }else{
        src = "../images/bu.png";
    }
    return src;
}