/**
 * Created by Jaslew on 2017/7/13.
 */

var start,end,no,queryType,target;
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = "";
var reg = /^2\d{3}$/;
var targets = new Array(9);
targets["tm"] = "水温 (℃)";
targets["ph"] = "pH";
targets["ox"] = "溶解氧 (mg/L)";
targets["el"] = "电导率 (μS/cm)";
targets["nt"] = "浊度 (NTU)";
targets["po"] = "总磷 (mg/L)";
targets["nh"] = "氨氮 (mg/L)";
targets["cl"] = "氯含量";
targets["ca"] = "碳含量";

var targets2 = new Array(9);
targets2["tm"] = "水温";
targets2["ph"] = "pH";
targets2["ox"] = "溶解氧";
targets2["el"] = "电导率";
targets2["nt"] = "浊度";
targets2["po"] = "总磷";
targets2["nh"] = "氨氮";
targets2["cl"] = "氯含量";
targets2["ca"] = "碳含量";

//y轴步长设置
var stepSize = new Array(9);
stepSize["tm"] = 1;
stepSize["ph"] = 0.1;
stepSize["ox"] = 1;
stepSize["el"] = 2;
stepSize["nt"] = 0.1;
stepSize["po"] = 0.1;
stepSize["nh"] = 1;
stepSize["cl"] = 1;
stepSize["ca"] = 1;

$(document).ready(function () {
    //初始化日期选择框
    var date = Html5DateFormate(new Date());
    $(".selection").find("input[name = 'startDate']").val(date);
    $(".selection").find("input[name = 'endDate']").val(date);
    $(".selection").find("input[name = 'option']").get(0).checked = true;

    //初始化一次报表视图
    initParam();
    getData();

    //绑定监听函数
    $(".selection").find("select[name = 'siteno']").change(function () {
        initParam();
        getData();
    });
    $(".selection").find("select[name = 'queryType']").change(function () {
        initParam();
        getData();
    });
    $(".selection").find("input[name = 'option']").change(function () {
        initParam();
        getData();
    });
    $(".selection").find("input[name = 'startDate']").blur(function () {
        var date = $(".selection").find("input[name = 'startDate']").val();
        if(reg.test(date.slice(0,4))){
            initParam();
            getData();
        }
    });
    $(".selection").find("input[name = 'endDate']").blur(function () {
        var date = $(".selection").find("input[name = 'endDate']").val();
        if(reg.test(date.slice(0,4))){
            initParam();
            getData();
        }
    });
});

//初始化参数
function initParam() {
    start =  $(".selection").find("input[name = 'startDate']").val();
    end = $(".selection").find("input[name = 'endDate']").val();
    no = $(".selection").find("select[name = 'siteno']").val();
    queryType = $(".selection").find("select[name = 'queryType']").val();
    target = $(".selection").find("input[type = 'radio']:checked").val();
}

//获取列表数据
function getData() {
    var pdata = "no="+no+"&start="+start+"&end="+end+"&queryType="+queryType+"&target="+target;
    $.ajax({
        url:'../includes/chartQueryProcess.php',
        data:pdata,
        type:'POST', //POST
        async:true,    //或false,是否异步
        timeout:1000,    //超时时间
        dataType:'text',    //返回的数据格式：json/xml/html/script/jsonp/text
        success:function(data,textStatus,jqXHR){
            data = JSON.parse(data);
            var x = new Array();
            var y = new Array();
            if(data != 0){
                for(var i = 0; i < data.length; i++){
                    x[i] = data[i].time;
                    y[i] = data[i].val;
                }
            }
            if(myChart != ""){
                myChart.destroy();
            }
            showInfo(y);    //最大最小平均值
            showChart(x,y);
        },
        error:function(xhr,textStatus){
            //
        },
        complete:function(){
            //
        }
    });
}

function showChart(x,y) {
    myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: x,
            datasets: [{
                label: targets[target],
                data: y,
                backgroundColor:
                    'rgba(1, 118, 253, 0.5)',
                borderColor:
                    'rgba(2, 118, 253, 1)',

                borderWidth: 1,
                fill:false,
                pointStyle:'circle',
            }]
        },
        options: {
            title: {
                display: true,
                fontColor:'black',
                fontSize:16,
                text:targets2[target]+'历史曲线',
                position:"top",
            },
            layout: {
                padding: {
                    left: 10,
                    right: 10,
                    top: 0,
                    bottom: 10
                }
            },
            legend:{
                display:true,
                position:"top",
            },
            scales: {
                yAxes: [{
                    scaleLabel:{
                        display:false,
                    },
                    ticks: {
                        stepSize: stepSize[target],
                        fontColor:'rgba(2, 118, 253, 1)',
                    },
                }],
                xAxes:[{
                    ticks:{
                        display:false,
                    }
                }]
            },
            animation: {
                duration:1500,
                easing:'easeInOutCubic',
            }
        }
    });
}

function showInfo(y) {
    var min = 0,max = 0,avg = 0,len = y.length;
    if(len > 0){
        min = max = y[0];
        for(var i = 0; i < len; i++){
            if(min > y[i])
                min = y[i];
            if(max < y[i])
                max = y[i];
            avg += parseFloat(y[i]);
        }
        avg /= len;
        avg = avg.toFixed(2);
    }
    $(".content .cbox .chartInfo p:nth-of-type(1)").text("Max : "+max);
    $(".content .cbox .chartInfo p:nth-of-type(2)").text("Min : "+min);
    $(".content .cbox .chartInfo p:nth-of-type(3)").text("Avg : "+avg);
}