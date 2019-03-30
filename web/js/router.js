/**
 * Created by Jaslew on 2017/7/13.
 */

function router(src){
    //获取当前站点
    var site = $('#top-bar').find("select[name='site']").val();
    //刷新框架页
    $('iframe').attr("src",src+"?site="+site).ready();
}