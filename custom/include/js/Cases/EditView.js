$(document).ready(function() {
    $("#external_office_c").change(function() {
        $.ajax({
            url: 'index.php',
            type: "POST",
            data: {
                module: 'bc_ExternalOffice',
                action: 'updateExternalOfficeUser',
                office_id: $("#external_office_c").val()
            },
            beforeSend: function() {
                ajaxStatus.showStatus('Updating Office User List...');
            },
            complete: function() {
                ajaxStatus.hideStatus();
            },
            success: function(data, status, soapResponse) {
                $("#external_user_id_c").html(data);
                $("#external_user_name_c").val($("#external_user_id_c option:selected").text());

            },
            error: function(data, status, soapResponse) {
                //authentication failed
                alert('Seems like some problem with connection. Please try again later.');
            }
        });
    });

    $("#external_user_id_c").change(function() {
        $("#external_user_name_c").val($("#external_user_id_c option:selected").text());
    });

});

