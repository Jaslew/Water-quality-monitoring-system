/**
 * Created by Jaslew on 2017/7/12.
 */
function GetDateStr(AddDayCount) {
    var dd = new Date();
    dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
    var m = dd.getMonth()+1;//获取当前月份的日期
    var d = dd.getDate();
    //判断 月
    if(m < 10){
        m = "0" + m;
    }else{
        m = m;
    }
    //判断 日n
    if(d < 10){//如果天数<10
        d = "0" + d;
    }else{
        d = d;
    }
    return m+"月"+d+"日";
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

//返回一个HTML5 Date格式的日期，月和日,时分秒都是两位数
function Html5DateFormate2(date) {
    var Reg = /^\d{2}$/;
    var Month = date.getMonth()+1;
    var Day = date.getDate();
    var Hour = date.getHours();
    var Min = date.getMinutes();
    var Sec = date.getSeconds();
    Month = Reg.exec(Month) ? Month : '0'+Month;
    Day = Reg.exec(Day) ? Day : '0'+Day;
    Hour = Reg.exec(Hour) ? Hour : '0'+Hour;
    Min = Reg.exec(Min) ? Min : '0'+Min;
    Sec = Reg.exec(Sec) ? Sec : '0'+Sec;
    return date.getFullYear()+"-"+Month+"-"+Day+" "+Hour+":"+Min+":"+Sec;
}