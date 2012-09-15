$(document).ready(function(){
    if ( webkitNotifications.checkPermission() != 0 ){
        $('body').append('<div id="notification_setting_div" style="top:0px;position:absolute; display:none;z-index: 5000;"><button id="notifSettings" >Change notification settings</button></div>');
        if($("#notification_setting_div").css('display')=='none')
        {
            $('body').css('margin-top','22px');
            $("#notification_setting_div").show();
            $("#notification_setting_div").css('background','url(custom/include/images/notification_per.png)');
            $("#notification_setting_div").css('float','left');
            $("#notification_setting_div").css('width', '100%');
            $("#notification_setting_div").css('text-align', 'right');
            $("#notifSettings").click( function(){
                window.webkitNotifications.requestPermission();
                $("#notification_setting_div").hide();
                $('body').css('margin-top','0px');
            });
        }
    }
});
function createNotificationInstance() {    
    
    if ( webkitNotifications.checkPermission() == 0 )
    {       
        $.ajax({
            url: "index.php?module=Home&action=getnotify",
            context: document.body,
            success: function(htm){ 
                if(htm != "||"){
                    var splitstr = htm.split("||");                    
                    var getarrstr = unserialize(splitstr[0]);
                    var fieldMap = new Array();
                    fieldMap['Tasks'] = "Task";
                    fieldMap['Emails'] = "Email";
                    fieldMap['Cases'] = "Case";
                    fieldMap['Notes'] = "Note";
                    for (var singleArray in getarrstr) {
                        var iconImageUrl = "custom/include/images/" + fieldMap[getarrstr[singleArray][1]] + ".gif";
                        var title = "Hello " + splitstr[1] ;
                        var subTitle = "New " + fieldMap[getarrstr[singleArray][1]]  + " assigned : " + getarrstr[singleArray][0];  

                        switch(fieldMap[getarrstr[singleArray][1]]){
                            case "Email":
                                var email_url = "index.php?module=" + getarrstr[singleArray][1] + "&action=DetailView&record=" + singleArray;
                                var email_notification = webkitNotifications.createNotification(iconImageUrl,title,subTitle);
                                email_notification.show();
                                email_notification.addEventListener('click', function() {  
                                    email_notification.cancel();
                                    window.open(email_url);
                                });
                                setTimeout(function(){
                                    email_notification.cancel();
                                }, 10000);
                                break;
                            case "Task":
                                var task_url = "index.php?module=" + getarrstr[singleArray][1] + "&action=DetailView&record=" + singleArray;
                                var task_notification = webkitNotifications.createNotification(iconImageUrl,title,subTitle);
                                task_notification.show();
                                task_notification.addEventListener('click', function() {  
                                    task_notification.cancel();
                                    window.open(task_url);
                                    
                                });
                                setTimeout(function(){
                                    task_notification.cancel();
                                }, 10000);
                                break;
                            case "Case":
                                var case_url = "index.php?module=" + getarrstr[singleArray][1] + "&action=DetailView&record=" + singleArray;
                                var case_notification = webkitNotifications.createNotification(iconImageUrl,title,subTitle);
                                case_notification.show();
                                case_notification.addEventListener('click', function() {  
                                    case_notification.cancel();
                                    window.open(case_url);
                                });
                                setTimeout(function(){
                                    case_notification.cancel();
                                }, 10000);
                                break;
                            case "Note":
                                var note_url = "index.php?module=" + getarrstr[singleArray][1] + "&action=DetailView&record=" + singleArray;
                                var note_notification = webkitNotifications.createNotification(iconImageUrl,title,subTitle);
                                note_notification.show();
                                note_notification.addEventListener('click', function() {  
                                    note_notification.cancel();
                                    window.open(note_url);
                                });
                                setTimeout(function(){
                                    note_notification.cancel();
                                }, 10000);
                                break;     
                        }
                        
                    }
                }
                
                
            }
        });     
    }
    else{
        
//alert( "Please request permissions first." );
}
}
setInterval(createNotificationInstance, 5000);
