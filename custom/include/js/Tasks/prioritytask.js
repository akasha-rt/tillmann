function taskstatus(record){
    $.ajax({
        url: 'index.php?module=Home&action=updatetask&record='+record,
        context: document.body,
        success: function(result){ 
            $('#'+record).hide('slow');        
        }
    });
}
function priortasknotify() {  
    var count = $('#task_count').val(); 
    var todayDate=new Date();
    var date=todayDate.getDate();
    var month=todayDate.getMonth()+1;
    var year=todayDate.getFullYear();
    var hours=todayDate.getHours();
    var minutes=todayDate.getMinutes();
    var seconds=todayDate.getSeconds();
    var date_start = date+"-"+month+"-"+year+" "+hours+":"+minutes+":"+ seconds;
    var mydate =  new Date(year ,month ,date,hours,minutes,seconds);
    
    for(var i=0; i<count; i++) {
        var tasks = $('#priority_tasks'+i).val();
        var n = [];
        n=tasks.split('#');
        var notfy = $('#is_t_notified_'+n[0]).val();
        if(notfy != '1'){
            var secs = parseInt((stringToDate(n[1]) - stringToDate(date_start) )) / 1000;
            var leftminutes = Math.floor(parseInt(secs)/60); 
            if(leftminutes <= 15){
                var task_id = n[0];
                var task_name = n[2];
                var task_duedate = n[4];
                var task_status = n[5];
                var content;
                var task_id1 = "'"+task_id+"'";
                content = '<div id="' + task_id + '" style="float:left"><table width="100%" border="0" cellpadding="1" cellspacing="0" class="olBgClass">';
                content += '<tbody><tr><td>';
                content += '<table width="100%" border="0" cellpadding="2" cellspacing="0" class="olCgClass">';
                content += '<tbody><tr><td width="100%" class="olCgClass">';
                content += '<div class="olCapFontClass">';
                content += '<div style="float:left"> Task </div>';
                content += '<div style="float: right">&nbsp;</div></div>';
                content += '</td></tr></tbody>';
                content += '</table></td></tr><tr><td><div style="overflow: auto;max-height:400px;">';
                content += '<table width="100%" cellpadding="2" cellspacing="0" class="olFgClass">';
                content +="<tr><td><strong>Task Name:</strong></td><td>" + task_name + "</td></tr>";
                content +="<tr><td><strong>Due Date:</strong></td><td>" + task_duedate + "</td></tr>";
                content +="<tr><td><strong>Time Remains:</strong></td><td>" + leftminutes + " Min.</td></tr>";
                content +="<tr><td><strong>Status:</strong></td><td>" + task_status + "</td></tr>";
                content +='<tr><td colspan="2"><strong><a href="#" onclick="taskstatus('+task_id1+');">Click To Complete The Task.</a></td></strong></tr>';
                content += "</table>";
                content += '</div></td></tr></tbody></table></div><div style="float:left;">&nbsp;</div>';
                content += '<input type="hidden" name="is_t_notified_'+task_id+'" id="is_t_notified_'+task_id+'" value="1">';
                
                
                if(content != ''){
                    if ($("#task_notify").css('display') == 'none' || $("#task_notify").css('display') == 'block'){
                    }
                    else{
                        $('body').append('<div id="task_notify" style="position:fixed; display:none;z-index: 5000; right:0px;"></div>');                        
                    }                    
                    $("#task_notify").append(content);
                    $('#task_notify').css({
                        'top' : '100%',
                        'margin-top' : '-'+$('#task_notify').height()+'px'
                    });
                    $("#task_notify").show('slow');
                    
                } 
   
            }
        }
    }
}


function stringToDate(s) {
    var dateParts = s.split(' ')[0].split('-'); 
    var timeParts = s.split(' ')[1].split(':');
    //var d = new Date(dateParts[0], --dateParts[1], dateParts[2]);
    //d.setHours(timeParts[0], timeParts[1], timeParts[2])
    var d = new Date(dateParts[2], --dateParts[1], dateParts[0], timeParts[0], timeParts[1], timeParts[2]);
    return d
}

setInterval(priortasknotify, 10000);
// RECURSIVE TASK CODE Starts From Here
var tab = "";
var daycount = "";
  
tab += '<table width="100%">';
tab += '<tr id="TR_repeat_type"><td width="25%">Repeat :</td><td width="50%">';
tab += '<select name="repeat_type" id="repeat_type" onchange="type_change();">';
tab += '<option value="">None</option>';
tab += '<option value="Daily">Daily</option>';
tab += '<option value="Weekly">Weekly</option>';
tab += '<option value="Monthly">Monthly</option>';
tab += '<option value="Yearly">Yearly</option>';
tab += '</select>';
tab += '</td></tr>';
tab += '<tr id="TR_repeat_delay" style="visibility:hidden;" ><td width="25%">Every :</td>';
tab += '<td width="50%"><select name="repeat_delay" id="repeat_delay">';    
for (var i=1;i<=30;i++)
{ 
    daycount += "<option value='"+i+"'>"+i+"</option>";
}    
tab += daycount;
tab += '</select></td></tr>';
tab += '<tr id="TR_repeat_count_radio" style="visibility:hidden;" ><td width="25%">End :</td>';
tab += '<td width="50%">';
tab += '<input type="radio" style="position: relative; top: -5px;" onclick="setend(this.id);" id="repeat_count_radio" checked="checked" value="number" name="repeat_end_type"><span>&nbsp;After&nbsp;</span>'; 
tab += '<input type="input" value="10" name="repeat_count" id="repeat_count" onkeyup="repeat_count_change();" size="3"> recurrences';
tab += '</td>';
tab += '</tr>';
tab += '<tr id="TR_repeat_until_radio" style="visibility:hidden;" ><td width="25%">&nbsp;</td>';
tab += '<td width="50%"><input type="radio" style="position: relative; top: -5px;" value="date" id="repeat_until_radio" onclick="setend(this.id);" name="repeat_end_type">';
tab += '<span>&nbsp;By&nbsp;</span> <input type="input" value="" name="repeat_until" id="repeat_until_input" onchange="repeat_until_change();" maxlength="10" readonly="true" size="11">';
tab += '<img align="absmiddle" border="0" style="" id="repeat_until_trigger" alt="Enter Date" src="index.php?entryPoint=getImage&amp;imageName=jscalendar.gif">';	 						
tab += '<script type="text/javascript">Calendar.setup ({';
tab += 'inputField : "repeat_until_input",';
tab += 'ifFormat : "%m/%d/%Y",';
tab += 'daFormat : "%m/%d/%Y",';
tab += 'button : "repeat_until_trigger",';
tab += 'singleClick : true,';
tab += 'dateStr : "",';
tab += 'step : 1,';
tab += 'startWeekday: 0,';
tab += 'weekNumbers:false});</script>';
tab += '</td></tr>';
tab += '<tr id="TR_weekly" style="visibility:hidden;"><td width="25%">On :</td>';
tab += '<td width="50%">';

tab += 'Sun <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[sun]" id="repeat_dow_0">'; 	
tab += 'Mon <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[mod]" id="repeat_dow_1">'; 	
tab += 'Tue <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[tue]" id="repeat_dow_2">'; 	
tab += 'Wed <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[wed]" id="repeat_dow_3">'; 	
tab += 'Thu <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[thur]" id="repeat_dow_4">'; 	
tab += 'Fri <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[fri]" id="repeat_dow_5">'; 	
tab += 'Sat <input type="checkbox" style="margin-right: 10px;" name="repeat_dow[sat]" id="repeat_dow_6">'; 
tab += '</td></tr>';
tab += '</table>';




$(document).ready(function(){
    $('#is_recursive_c').click(function(){
        if(document.getElementById('is_recursive_c').checked){
            if ($("#appended").css('display') == 'none' || $("#appended").css('display') == 'block'){
            }
            else{
                $("#LBL_TASK_INFORMATION table:first tr:last td:last").append("<div id='appended' style='float:left; width:100%;display:none;position: relative;left: -196px;top: 6px;'>"+tab+"</div>");  
                
            }
            $('#appended').slideDown(); 
        }
        else{
            $('#appended').slideUp();
        /* $("#TR_repeat_delay").css({
                'visibility':'hidden'
            });
        
            $("#TR_repeat_count_radio").css({
                'visibility':'hidden'
            });
        
            $("#TR_repeat_until_radio").css({
                'visibility':'hidden'
            });
        
            $("#TR_weekly").css({
                'visibility':'hidden'
            });
            $("#repeat_type option[value='']").attr("selected", "selected");*/
        }
    });

});

function repeat_count_change() {
    $("#repeat_count_radio").attr('checked','checked');
    $('#repeat_until_input').val(''); 
}
function repeat_until_change() {
    $("#repeat_until_radio").attr('checked','checked');
    $('#repeat_count').val('');  
}
function type_change() {
    var selectVal = $('#repeat_type :selected').val();
    if(selectVal==''){
        $("#TR_repeat_delay").css({
            'visibility':'hidden'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'hidden'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'hidden'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
        
        
        
    }
    else if(selectVal=='Daily'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
    else if(selectVal=='Weekly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'visible'
        });
    }
    else if(selectVal=='Monthly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
    else if(selectVal=='Yearly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
}

function setend(type){
    var Etype = type;
    if(Etype == 'repeat_count_radio'){
        $('#repeat_until_input').val('');
    }
    else if (Etype == 'repeat_until_radio'){
        $('#repeat_count').val('');
    }
    
}


function init_recursive(rpt_type,rpt_delay,rpt_count,rpt_until,rpt_dow){    
    $("#LBL_TASK_INFORMATION table:first tr:last td:last").append("<div id='appended' style='float:left; width:100%;position: relative;left: -196px;top: 6px;'>"+tab+"</div>");
    var dow = [];
    var day = [];   
    var k = 0;
    dow =rpt_dow.split('||');  
    for (var i = 0; i < dow.length; i++) {
        day = dow[i].split('=');
        for (var j = 0; j < day.length; j++) {
            k = j+ 1;
            if(day[j] == 'sun' && day[k] == 'on'){
                $('#repeat_dow_0').attr('checked','checked');
            }
            else if(day[j] == 'mon' && day[k] == 'on'){
                $('#repeat_dow_1').attr('checked','checked');
            }
            else if(day[j] == 'tue' && day[k] == 'on'){
                $('#repeat_dow_2').attr('checked','checked');
            }
            else if(day[j] == 'wed' && day[k] == 'on'){
                $('#repeat_dow_3').attr('checked','checked');
            }
            else if(day[j] == 'thur' && day[k] == 'on'){
                $('#repeat_dow_4').attr('checked','checked');
            }
            else if(day[j] == 'fri' && day[k] == 'on'){
                $('#repeat_dow_5').attr('checked','checked');
            }
            else if(day[j] == 'sat' && day[k] == 'on'){
                $('#repeat_dow_6').attr('checked','checked');
            }
        }
        
    }


    $("#repeat_type option").each(function () {
        if ($(this).html() == rpt_type) {
            $(this).attr("selected", "selected");
            return;
        }
    });
    $("#repeat_delay option").each(function () {
        if ($(this).html() == rpt_delay) {
            $(this).attr("selected", "selected");
            return;
        }
    });
    if(rpt_count.length > 0){
        $('#repeat_count_radio')[0].checked = true;
        $('#repeat_count').val(rpt_count);
        $('#repeat_until_input').val('');
    }
    else if(rpt_until.length > 0){
        $('#repeat_until_radio')[0].checked = true;
        $('#repeat_count').val('');
        $('#repeat_until_input').val(rpt_until);
    }
    var selectVal = $('#repeat_type :selected').val();
    if(selectVal==''){
        $("#TR_repeat_delay").css({
            'visibility':'hidden'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'hidden'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'hidden'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
    else if(selectVal=='Daily'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
    else if(selectVal=='Weekly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'visible'
        });
    }
    else if(selectVal=='Monthly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
    else if(selectVal=='Yearly'){
        $("#TR_repeat_delay").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_count_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_repeat_until_radio").css({
            'visibility':'visible'
        });
        
        $("#TR_weekly").css({
            'visibility':'hidden'
        });
    }
}

// RECURSIVE TASK CODE Ends From Here