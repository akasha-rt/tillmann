/**
 * Created by lewis on 18/02/14.
 */



function retrievePage(page_id, pageRefresh) {
    retrieveData(page_id, pageRefresh);

}

function retrieveData(page_id, pageRefresh) {
    SUGAR.ajaxUI.showLoadingPanel();
    $.ajax({
        url: "index.php?entryPoint=retrieve_dash_page",
        dataType: 'html',
        type: 'POST',
        data: {
            'page_id': page_id
        },
        success: function (data) {
            var pageContent = data;

            outputPage(page_id, pageContent)
            if (pageRefresh) {
                window.location.reload();
            }
        },
        error: function (request, error)
        {

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



