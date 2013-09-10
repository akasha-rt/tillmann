function addToWatchList(currentElem,userId,module){
    if(currentElem.title != "Remove from Watch List"){
        currentElem.title = "Remove from Watch List";
        currentElem.src = "custom/image/follow2.png";
    }else{
        currentElem.title = "Add to Watch List";
        currentElem.src = "custom/image/follow1.png";
    }
    $.ajax({
        url: 'index.php?module=Home' + '&action=add_follow_list&record=' + currentElem.id + '&userId=' + userId +'&module_name='+module,
        type: "GET",
        success: function(data) {
            
        },
    });
}
