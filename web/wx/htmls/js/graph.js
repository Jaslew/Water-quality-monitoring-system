function graph() {
    var myChart = "";
    var ctx = document.getElementById("myChart").getContext('2d');
    var targets = new Array(9);targets["tm"] = "水温 (℃)";targets["ph"] = "pH";
    targets["ox"] = "溶解氧 (mg/L)";targets["el"] = "电导率 (μS/cm)";targets["nt"] = "浊度 (NTU)";
    targets["po"] = "总磷 (mg/L)";targets["nh"] = "氨氮 (mg/L)";targets["cl"] = "氯含量";targets["ca"] = "碳含量";
    var targets2 = new Array(9);targets2["tm"] = "水温";targets2["ph"] = "pH";targets2["ox"] = "溶解氧";
    targets2["el"] = "电导率";targets2["nt"] = "浊度";targets2["po"] = "总磷";targets2["nh"] = "氨氮";
    targets2["cl"] = "氯含量";targets2["ca"] = "碳含量";
    //y轴步长设置
    var stepSize = new Array(9);stepSize["tm"] = 1;stepSize["ph"] = 0.1;stepSize["ox"] = 1;
    stepSize["el"] = 2;stepSize["nt"] = 0.1;stepSize["po"] = 0.1;stepSize["nh"] = 1;stepSize["cl"] = 1;stepSize["ca"] = 1;

    var calendarRange = app.calendar.create({
        inputEl: '#calendar-range',
        rangePicker: true,
    });

    var t = Html5DateFormate(new Date());
    calendarRange.setValue([t,t]);

    //获取选项数据
    app.request({
        url: 'ajax/graphSelectProcess.php',
        async: false,//这里使用同步
        method: 'POST',
        timeout: 1500,
        success: function (data) {
            if(data){
                data = JSON.parse(data);
                var pnode = "#graph .list a:nth-of-type(1) select[name = 'site']";
                var snode = "";
                for(var i = 0; i < data.length; i++){
                    if(i == 0)
                        snode += "<option value='"+data[i].no+"'>"+data[i].name+" ("+data[i].no+")"+"</option>";
                    else
                        snode += "<option value='"+data[i].no+"'>"+data[i].name+" ("+data[i].no+")"+"</option>";
                }
                $$(pnode).append(snode);
            }
        },
        error:function () {
            //
        }
    });
    //初始化图表数据
    getData();
    //触发获取图表数据
    $$("#graph select,#graph input").on("change",function () {
        getData();
    });

    function getData() {
        var formData = app.form.convertToData('#graph-input');
        //时间范围过滤
        if(formData.time.length == 23){
            var trange = formData.time.split(" - ");
            formData = {no : formData.site, start : trange[0], end : trange[1], queryType : formData.querytype, target: formData.param};
            app.request.post('../../includes/chartQueryProcess.php', formData, function (data, status) {
                if(status == 200 && data){
                    data = JSON.parse(data);
                    var x = [];
                    var y = [];
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
                    showChart(x,y,formData.target);
                }
            });
        }
    }

    function showChart(x,y,target) {
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
                            fontSize: 8
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
        $$("#graph-info li:nth-of-type(1) .item-after").text(max);
        $$("#graph-info li:nth-of-type(2) .item-after").text(avg);
        $$("#graph-info li:nth-of-type(3) .item-after").text(min);
    }

    //返回一个HTML5 Date格式的日期，月和日都是两位数
    function Html5DateFormate(date) {
        var Reg = /^\d{2}$/;
        var Month = date.getMonth()+1;
        var Day = date.getDate();
        Month = Reg.exec(Month) ? Month : '0'+Month;
        Day = Reg.exec(Day) ? Day : '0'+Day;
        return date.getFullYear()+"-"+Month+"-"+Day;
    }
}