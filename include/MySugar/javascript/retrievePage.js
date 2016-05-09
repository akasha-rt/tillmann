/**
 * Created by lewis on 18/02/14.
 */



function retrievePage(page_id, pageRefresh) {
    retrieveData(page_id, pageRefresh);

}

function retrieveData(page_id, pageRefresh) {
//    if(typeof pageRefresh == 'undefined'){
//        pageRefresh = true;
//    }
    debugger;
    SUGAR.ajaxUI.showLoadingPanel();
    $.ajax({
        url: "index.php?entryPoint=retrieve_dash_page",
        dataType: 'html',
        type: 'POST',
        data: {
            'page_id': page_id
        },
        success: function (data) {
            debugger;
            var pageContent = data;

            outputPage(page_id, pageContent)
            if (pageRefresh) {
                window.location.reload();
            }
            debugger;
            SUGAR.ajaxUI.hideLoadingPanel();
            if (typeof pageRefresh == 'undefined') {
                renderChangeLayoutDialog();
            }

        },
        error: function (request, error)
        {
            SUGAR.ajaxUI.hideLoadingPanel();
        }
    })
}

function outputPage(page_id, pageContent, pageRefresh) {


    $("div[id^=pageNum_]").each(function () {
        $(this).css("display", "none");
        $(this).empty();

    });

    $(".active").removeClass("active");
    $("#pageNum_" + page_id).addClass("active");

    $(".current").removeClass("current");
    $("#pageNum_" + page_id + "_anchor").addClass("current");

    $("#pageNum_" + page_id + "_div").css("display", "block");

    $("#pageNum_" + page_id + "_div").append(pageContent);

//    $("#removeTab_anchor").attr("onclick","removeForm("+ page_id +")");

}
function renderChangeLayoutDialog() {
    SUGAR.mySugar.changeLayoutDialog = new YAHOO.widget.Dialog("changeLayoutDialog", {
        width: "300px",
        fixedcenter: true,
        visible: false,
        draggable: false,
        effect: [{
                effect: YAHOO.widget.ContainerEffect.SLIDE,
                duration: 0.5
            }, {
                effect: YAHOO.widget.ContainerEffect.FADE,
                duration: 0.5
            }],
        modal: true
    });
    document.getElementById('changeLayoutDialog').style.display = '';
    SUGAR.mySugar.changeLayoutDialog.render();
    document.getElementById('changeLayoutDialog_c').style.display = 'none';
}



