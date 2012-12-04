function showItemHistoryChart(id,accName){
    var data = 'module=Accounts&action=getItemHistoryChart&id='+id + '&accName='+accName;
    $('body').append('<div id="backgroundpopup" style="display:none"></div>');
    $('body').append('<div id="historydetail_div" style="display:none" ></div>');
    $('#backgroundpopup').css({
        'background': 'none repeat scroll 0 0 #000000',
        'border': '1px solid #CECECE',                                                                        
        'left': '0',
        'position': 'fixed',
        'top': '0',
        'width': '100%',
        'height': '100%',
        'opacity': '0.25',
        'z-index': '1'                                                                         
    });
    $.ajax({                
        url: "index.php",	
        type: "POST",
        
        data: data,
        beforeSend: function() {  
            $('#ajaxloading_c').css({
                'visibility': 'visible',
                'display' :'block',
            });
            $('#backgroundpopup').show();
        },
        complete: function() {
            $('#ajaxloading_c').css({
                'visibility': 'hidden',
                'display' :'none',
            });
        },
        success: function (result) { 
            
            $('#historydetail_div').html(result);              
            $('#historydetail_div').show();
            $('#historydetail_div').css("top","25%");
            $('#historydetail_div').css("left","50%");
            $('#historydetail_div').css("margin-left",'-'+($('#historydetail').width()/2)+'px');           
            $('#historydetail_div').css("position", "fixed");
            $('#historydetail_div').css("height", "auto"); 
            $('#historydetail_div').css("z-index", "10000"); 
        }
    });  

}