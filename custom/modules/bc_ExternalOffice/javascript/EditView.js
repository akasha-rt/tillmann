$(document).ready(function() {
    $("#TestConnection").click(function() {
        var api_url = $("#api_url").val();
        var api_user = $("#api_user").val();
        var api_pass = $("#api_user_pass").val();
        var old_pass = $("#old_api_pass").val();

        if (old_pass !== api_pass) {
            api_pass = $.md5(api_pass);
        }

        var soapMessage =
                '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' +
                '<soap:Body>' +
                '<login>' +
                '<user_auth>' +
                '<user_name>' + api_user + '</user_name>' +
                '<password>' + api_pass + '</password>' +
                '</user_auth>' +
                '</login>' +
                '</soap:Body>' +
                '</soap:Envelope>';

        $.ajax({
            url: api_url,
            type: "POST",
            dataType: "xml",
            contentType: "text/xml; charset=\"utf-8\"",
            headers: {
                //SOAPAction: "http://localhost/biorbytcrm/service/v4/soap.php/login"
                SOAPAction: api_url + "/login"
            },
            data: soapMessage,
            success: function(data, status, soapResponse) {
                //Sucessfully authenticated
                //alert(soapResponse.responseText);
                /*parsedResponseDoc = $.parseXML(soapResponse.responseText);
                 $parsedResponse = $(parsedResponseDoc);
                 session_id = $parsedResponse.find("id");*/
                session_id = $(data).find('id').text();
                alert('Connection Sucessfull');
            },
            error: function(data, status, soapResponse) {
                //authentication failed
                //alert(soapResponse);
                alert('Connection Failed.');
            }
        });
    });
});


