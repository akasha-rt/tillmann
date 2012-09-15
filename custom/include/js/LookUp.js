$(document).ready(function(){
    createDiv();
    $(document).mouseup(function(e) {
        if(getSelectedText().trim() !='')
        {
            if($('#lookup_toolbar_div').css('display') == "block"){
                $('#lookup_toolbar_div').fadeIn('slow');
            }else{
                $('#lookup_toolbar_div').css({
                    position:"absolute", 
                    top:e.pageY + 10, 
                    left: e.pageX + 15
                }).fadeIn('slow');
            }
                            
        }
        else
        {
            $('#lookup_toolbar_div').css( {
                display:"none"
            }).fadeOut('slow');
        }
    });
                
    $('#lookpbtn').mouseup(function(e){
        //alert(getSelectedText());   
        var data = '&search='+getSelectedText();                     
        $.ajax({               
            url :'index.php?module=Home&action=lookup',                    
            type:"GET",
            data: data,
            success:function(result){  
                $('#lookup_result_div').html(result);
                var x = e.pageX,y = e.pageY,
                scX = $(window).scrollLeft(),
                scY = $(window).scrollTop(),
                scMaxX = scX + $(window).width(),
                scMaxY = scY + $(window).height(),
                wd = $("#lookup_result_div").width(),
                hgh = $("#lookup_result_div").height();

                if (x + wd > scMaxX) x = scMaxX - wd;
                if (x < scX) x = scX;
                if (y + hgh > scMaxY) y = scMaxY - hgh;
                if (y < scY) y = scY;
                $('#lookup_result_div').css( {
                    position:"absolute", 
                    top:y , 
                    left:x
                }).fadeIn('slow');
            /*$('#lookup_result_div').css({
                    position:"absolute" , 
                    top: 539 , 
                    left: 343
                }).fadeIn('slow');*/
            //alert(result);
            }
                                        
        });
        e.stopPropagation();
        e.preventDefault();
        e.returnValue = false;
        e.cancelBubble = true;
        return false; 
                
    }); 
         
    function getSelectedText() {
        if (window.getSelection) {
            return window.getSelection().toString();
        } else if (document.selection) {
            return document.selection.createRange().text;
        }
        return '';
                
    }
    function createDiv() 
    { 
        $('body').append('<div id="lookup_toolbar_div" style="display:none;z-index: 5000;"><img src="custom/include/images/lookup.png" id="lookpbtn" title="Look Up"></div>');
        $('body').append('<div id="lookup_result_div" style="display:none;z-index: 1000; background-image: none;overflow: auto;"></div>');
    }
    
});
            

            
            
