/**
 * Created by Jaslew on 2017/7/31.
 * 描述：全选与全不选
 *c参数为table的class名
 */
function theadChange(c){
    var s =  $(c+"thead").find("input[name = 'selects']").is(":checked");
    if(s){
        $(c+"thead").find("input[name = 'selects']").attr("checked",true);
        $(c+"tbody").find("input[name = 'selects']").each(function(){
            this.checked = true;
            $(this).attr("checked",true);
        });
    }else{
        $(c+"thead").find("input[name = 'selects']").removeAttr("checked");
        $(c+"tbody").find("input[name = 'selects']").each(function(){
            this.checked = false;
            $(this).removeAttr("checked");
        });
    }
}

function tbodyChange(c){
    var state = new Array();
    var len = $(c+"tbody").find("input[name = 'selects']").length;
    var i = 0;
    if(this.checked){
        $(this).attr("checked",true);
    }else{
        $(this).removeAttr("checked");
    }
    $(c+"tbody").find("input[name = 'selects']").each(function(){
        state[i] = this.checked;
        i++;
    });
    var s = $.inArray(false,state);
    if(s != -1){
        $(c+"thead").find("input[name = 'selects']").removeAttr("checked");
        $(c+"thead").find("input[name = 'selects']").each(function(){
            this.checked = false;
        })
    }else{
        $(c+"thead").find("input[name = 'selects']").attr("checked",true);
        $(c+"thead").find("input[name = 'selects']").each(function(){
            this.checked = true;
        })
    }
}