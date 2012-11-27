
function showItemHistoryChart(id,accName){
    var data = 'module=Accounts&action=getItemHistoryChart&id='+id + '&accName='+accName;
    $('body').append('<div id="historydetail_div" style="display:none" ></div>'); 
    $('body').append('<div id="backgroundpopup" style="display:none"></div>');             
    $('#backgroundpopup').css({
        'background': 'none repeat scroll 0 0 #000000',
        'border': '1px solid #CECECE',                                                                        
        'left': '0',
        'position': 'fixed',
        'top': '0',
        'width': '100%',
        'height': '100%',
        'opacity': '0.8',
        'z-index': '1'                                                                         
    });                                
    $.ajax({                
        url: "index.php",	
        type: "POST",
        data: data,
        success: function (result) {  
            $('#historydetail_div').html(result);  
            $('#backgroundpopup').show();
            $('#historydetail_div').show();
            $('#historydetail_div').css("top",( $(window).height() - $('#historydetail').height() ) / (2+$(window).scrollTop()) + "px");
            $('#historydetail_div').css("left",( $(window).width() - $('#historydetail').width() ) / (2+$(window).scrollLeft()) + "px");
            $('#historydetail_div').css("position", "absolute");
            $('#historydetail_div').css("height", "auto"); 
            $('#historydetail_div').css("z-index", "5000"); 
        }
    });  

}