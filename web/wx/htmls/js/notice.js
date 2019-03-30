function notice() {
    var pageNow = 1,pageRow = 7,AN = 0,readyNID;   //AN:通知总数目
    var lastItemIndex,allowInfinite;
    var noticeDel = app.actions.create({
        buttons: [
            {
                text: '删除',
                color:'red',
                onClick: function (){
                    app.request({
                        url: '../../includes/noticeDelProcess.php',
                        async: true,
                        method: 'POST',
                        data: {time : readyNID},
                        success: function (data) {
                            if(data == "" || data == 0){
                                showTip(0);
                            }else if(data == 1){
                                showTip(1);
                                mainView.router.refreshPage();
                            }
                        },
                        error:function () {
                            showTip(-1);
                        }
                    });
                }
            }
        ]
    });
    getNotice();

    //获取通知
    function getNotice() {
        app.request({
            url: '../../includes/noticeGetProcess.php',
            async: true,
            method: 'POST',
            data: {pageNow: pageNow, pageRow: pageRow},
            success: function (data) {
                var html = "",isread,nid;
                if(data && data != 0){
                    data = JSON.parse(data);
                    //设置消息总数
                    AN = data[data.length - 1];
                    $$("#notice .row .chip-label span").text(AN);
                    for(var i = 0; i < data.length - 1; i++){
                        isread = data[i].isread == 1 ? "已读":"未读";
                        nid = "nid" + data[i].time;
                        html += "<li><div id='"+nid+"' class='card'>" +
                            "<span class=\"item-link item-content\">" +
                            "<div class=\"item-inner\"><div class=\"item-title-row\">" +
                            "<div class=\"item-title\">系统通知</div>" +
                            "<div class=\"item-after\">"+Html5DateFormate2(new Date(data[i].time * 1000))+"</div></div>" +
                            "<div class=\"item-subtitle\">"+isread+"</div>" +
                            "<div class=\"item-text\">"+data[i].content+"</div></div></span></div></li>";
                    }
                }else{
                    html = "暂无通知";
                    $$("#notice .row .chip-label span").text("0");
                }
                $$('#notice .list ul').append(html);
                if(AN > 0){
                    //用户长按
                    $$('#notice .list .card').on('taphold', function () {
                        readyNID = $$(this).attr("id").substr(3);
                        noticeDel.open();
                    });
                    //用户单击
                    $$('#notice .list .card').on('click', function () {
                        var popover = app.popover.create({
                            targetEl: $$(this),
                            content: '<div class="popover">'+
                            '<div class="popover-inner">'+
                            '<div class="block">'+
                            '<p>内容： '+$$(this).find(".item-text").text()+'</p>'+
                            '<p>时间： '+$$(this).find(".item-after").text()+'</p>'+
                            '<p><a href="#" class="link popover-close">关闭</a></p>'+
                            '</div>'+
                            '</div>'+
                            '</div>',
                        });
                        popover.open();
                    });
                }
                //是否显示 更多状态
                lastItemIndex = $$('#notice .list li').length;
                if(lastItemIndex < AN){
                    allowInfinite = true;
                    showMore();
                }else{
                    allowInfinite = false;
                    $$("#notice .preloader").css({"display":"none"});
                }
            },
            error:function () {
                //
            }
        });
    }
    //显示更多
    function showMore() {
        // Max items to load
        var maxItems = 100;

        // Attach 'infinite' event handler
        $$('.infinite-scroll-content').on('infinite', function () {
            // Exit, if loading in progress
            if (!allowInfinite) return;

            // Set loading flag
            allowInfinite = false;

            // Emulate 1s loading
            setTimeout(function () {
                // Reset loading flag
                allowInfinite = true;

                if (lastItemIndex >= maxItems) {
                    // Nothing more to load, detach infinite scroll events to prevent unnecessary loadings
                    app.infiniteScroll.destroy('.infinite-scroll-content');
                    // Remove preloader
                    $$('.infinite-scroll-preloader').remove();
                    return;
                }

                // Generate new items HTML
                pageNow++;

                getNotice();

                // Update last loaded index
                lastItemIndex = $$('.list li').length;
            }, 1000);
        });
    }
    //错误分类
    function showTip(i) {
        var mtext;
        switch (i){
            case 0:{
                mtext = "删除失败！";
                break;
            }
            case 1:{
                mtext = "删除成功！";
                break;
            }
            case -1:{
                mtext = "请求超时！";
                break;
            }
        }
        var tc = app.toast.create({
            text: mtext,
            position: 'center',
            closeTimeout: 2000
        });
        tc.open();
    }
}
