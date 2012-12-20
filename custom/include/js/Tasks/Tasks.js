$(document).ready(function(){
    var record = $("input[name='record']").val(); 

    $("input[value='Save']").attr('onclick','').click(function(){    
        if($("#is_priority_c").attr('checked') == true && document.getElementById("date_due_date").value == ''){
            alert("Please Select DueDate");
            return false;
        }
        else{
            this.form.action.value='Save';
            return check_form('EditView'); 
        }
                   
    });
});         

