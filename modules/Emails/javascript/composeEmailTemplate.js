/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
var http = createRequestObject();
        function createRequestObject()
                {
                var ro;
                        var browser = navigator.appName;
                        if (browser == "Microsoft Internet Explorer")
                {
                ro = new ActiveXObject("Microsoft.XMLHTTP");
                }
                else
                {
                ro = new XMLHttpRequest();
                }
                return ro;
                        }

        function GetXmlHttpObject()
                {

                var xmlHttp = null;
                        try
                {
                // Firefox, Opera 8.0+, Safari
                xmlHttp = new XMLHttpRequest();
                }
                catch (e)
                {
                //Internet Explorer
                try {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch (e) {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                }
                return xmlHttp;
                        }

        function selectDeptartment(idx, id)
                {
                xmlHttp = GetXmlHttpObject()
                        if (xmlHttp == null)
                {
                alert ("Browser does not support HTTP Request")
                        return
                }

                var url = "index.php?module=Emails&action=template&dept=" + id + "&idx=" + idx;
                        xmlHttp.onreadystatechange = selectTemplate
                        xmlHttp.open("GET", url, true)
                        xmlHttp.send(null);
                        }

        function selectTemplate()
                {
                if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete")
                {
                var resonseText = xmlHttp.responseText;
                        var str = resonseText.split("||");
                        document.getElementById("emailTemplate" + str[1]).innerHTML = str[0];
                }
                }

function ForwardLookUp(input_fields, module_name, action){

var input_field = input_fields;
        $("." + input_field + "_search_class").remove();
        $("#" + input_field).attr('autocomplete', 'off');
        $($("#" + input_field).parent()).append("<div id='" + input_field + "_search' class='" + input_field + "_search_class'>");
        $("#" + input_field + "_search").hide();
        $(document).on("input", "#" + input_field, function() {

var search = $("#" + input_field).val().replace(new RegExp('<[^<]+\>', 'g'), "");
        search = $.trim(search);
        if (search != '') {
$.ajax({
cache: true,
        async: true,
        type: 'POST',
        url: 'index.php?module=' + module_name + '&action=' + action,
        dataType: 'json',
        data: {
        name : search
        },
        success: function(data) {
        if (data == '') {
        $("." + input_field + "_search_class").remove();
                return false;
        }
        fset = '';
                for (i in data) {
        fset += '<option value="' + data[i]['option'] + '">' + data[i]['name'] + '</option>';
        }
        fset = "<select id='" + input_field + "_search_offers' size='5' multiple='true'>" + fset;
                fset += '</select>';
                $("#" + input_field + "_search").html(fset);
                $("#" + input_field + "_search").css('position', 'absolute');
                $("#" + input_field + "_search").css('z-index', '1');
                $("#" + input_field + "_search").show();
        }
});
}
});
        $(document).on('click', "#" + input_field + "_search_offers", function() {

var str = "";
        str = $(this).val();
        $("#" + input_field).val(str);
        $("." + input_field + "_search_class").remove();
});
        return true;
}

//showdiv Canned Response div
function showCRdiv(idx, id)
{
if (id == 'newCannedResponse'){
var showHideCheck = document.getElementById('cannedresponsediv' + idx).style.display;
        if (showHideCheck == "block")
        document.getElementById('cannedresponsediv' + idx).style.display = "none";
        else
        document.getElementById('cannedresponsediv' + idx).style.display = "block";
}
}


// Save Canned Response
function saveCannedResponse(idx)
{

var tiny = SE.util.getTiny('htmleditor' + idx);
        var tinyHTML = tiny.getContent();
        var text = decodeURI(encodeURI(tinyHTML)).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi, '&').replace(/&nbsp;/gi, ' ').replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&#039;/gi, '\'').replace(/&quot;/gi, '"').replace(/#/gi, 'h3a5sh'); //replace('#',/&#35;/gi).
        var openTag = '<body>';
        var closeTag = '</body>';
        //first process text if it contains <html> and <body> tag
        var startText = text.indexOf(openTag) + 6;
        var endText = text.indexOf(closeTag) - 1;
        var lengthText = endText - startText;
        if (text.indexOf(openTag) > - 1){
text = text.substr(startText, lengthText);
}

if (tinyHTML){
var mydiv = document.createElement("div");
        mydiv.innerHTML = tinyHTML;
        var body = '';
        if (document.all) // IE Stuff
{
body = mydiv.innerText;
}
else // Mozilla does not work with innerText
{
body = mydiv.textContent;
}
}

var name = document.getElementById('name' + idx).value;
        var subject = document.getElementById('emailSubject' + idx).value;
        var dept = document.getElementById('dept' + idx).value;
        //validate data
        if (name == ''){
alert("Name for canned response is required");
        document.getElementById('name' + idx).focus();
        return false;
}
//End 
$.ajax({
cache: true,
        async: true,
        type: 'POST',
        //url: 'index.php?module=EmailTemplates&action=cannedresponse&name='+name+'&sub='+subject+'&dept='+dept+'&body='+body+'&body_html='+text,                   
        url: 'index.php?module=EmailTemplates&action=cannedresponse&name=' + encodeURIComponent(name) + '&sub=' + encodeURIComponent(subject) + '&dept=' + encodeURIComponent(dept) + '&body=' + encodeURIComponent(body) + '&body_html=' + encodeURIComponent(text),
        success: function(data) {
        alert('Canned response created.');
                return false;
        }
});
        return true;
}

SUGAR.email2.templates['compose'] = '<div id="composeLayout{idx}" class="ylayout-inactive-content"></div>' +
                '<div id="composeOverFrame{idx}" style="height:100%;width:100%">' +
        '	<form id="emailCompose{idx}" name="ComposeEditView{idx}" action="index.php" method="POST">' +
        '		<input type="hidden" id="email_id{idx}" name="email_id" value="">' +
        '		<input type="hidden" id="uid{idx}" name="uid" value="">' +
        '		<input type="hidden" id="ieId{idx}" name="ieId" value="">' +
        '		<input type="hidden" id="mbox{idx}" name="mbox" value="">' +
        '		<input type="hidden" id="type{idx}" name="type" value="">' +
        '		<input type="hidden" id="composeLayoutId" name="composeLayoutId" value="shouldNotSeeMe">' +
        '		<input type="hidden" id="composeType" name="composeType">' +
        '		<input type="hidden" id="fromAccount" name="fromAccount">' +
        '		<input type="hidden" id="sendSubject" name="sendSubject">' +
        '		<input type="hidden" id="sendDescription" name="sendDescription">' +
        '		<input type="hidden" id="sendTo" name="sendTo">' +
        '		<input type="hidden" id="sendBcc" name="sendBcc">' +
        '		<input type="hidden" id="sendCc" name="sendCc">' +
        '		<input type="hidden" id="setEditor" name="setEditor">' +
        '		<input type="hidden" id="saveToSugar" name="saveToSugar">' +
        '		<input type="hidden" id="parent_id" name="parent_id">' +
        '		<input type="hidden" id="parent_type" name="parent_type">' +
        '		<input type="hidden" id="attachments" name="attachments">' +
        '		<input type="hidden" id="documents" name="documents">' +
        '		<input type="hidden" id="outbound_email{idx}" name="outbound_email">' +
        '		<input type="hidden" id="templateAttachments" name="templateAttachments">' +
        '		<input type="hidden" id="templateAttachmentsRemove{idx}" name="templateAttachmentsRemove">' +
        '		<table id="composeHeaderTable{idx}" cellpadding="0" cellspacing="0" border="0" width="100%" class="list">' +
        '			<tr>' +
        '				<th><table cellpadding="0" cellspacing="0" border="0"><tbody><tr ><td style="padding: 0px !important;margin:0px; !important" >' +
        '					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.sendEmail({idx}, false);"><img src="index.php?entryPoint=getImage&themeName=' + SUGAR.themes.theme_name + '&imageName=icon_email_send.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_SEND}</button>' +
        '					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.saveDraft({idx}, false);"><img src="index.php?entryPoint=getImage&themeName=' + SUGAR.themes.theme_name + '&imageName=icon_email_save.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_SAVE_DRAFT}</button>' +
        '					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.showAttachmentPanel({idx}, false);"><img src="index.php?entryPoint=getImage&themeName=' + SUGAR.themes.theme_name + '&imageName=icon_email_attach.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_ATTACHMENT}</button>' +
        '					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.showOptionsPanel({idx}, false);"><img src="index.php?entryPoint=getImage&themeName=' + SUGAR.themes.theme_name + '&imageName=icon_email_options.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_OPTIONS}</button>' +
        '</td><td style="padding: 0px !important;margin:0px; !important">&nbsp;&nbsp;{mod_strings.LBL_EMAIL_RELATE}:&nbsp;&nbsp;<select class="select" id="data_parent_type{idx}" onchange="document.getElementById(\'data_parent_name{idx}\').value=\'\';document.getElementById(\'data_parent_id{idx}\').value=\'\'; SUGAR.email2.composeLayout.enableQuickSearchRelate(\'{idx}\');" name="data_parent_type{idx}">{linkbeans_options}</select>' +
        '&nbsp;</td><td style="padding: 0px !important;margin:0px; !important"><input id="data_parent_id{idx}" name="data_parent_id{idx}" type="hidden" value="">' +
        '<input class="sqsEnabled" id="data_parent_name{idx}" name="data_parent_name{idx}" type="text" value="">&nbsp;<button type="button" class="button" onclick="SUGAR.email2.composeLayout.callopenpopupForEmail2({idx});"><img src="index.php?entryPoint=getImage&themeName=default&imageName=id-ff-select.png" align="absmiddle" border="0"></button>' +
        '			</td></tr></tbody></table></th>' +
        '			</tr>' +
        '			<tr>' +
        '				<td>' +
        '					<div style="margin:5px;">' +
        '					<table cellpadding="4" cellspacing="0" border="0" width="100%">' +
        '						<tr>' +
        '							<td class="emailUILabel" NOWRAP >' +
        '								<label for="addressFrom{idx}">{app_strings.LBL_EMAIL_FROM}:</label>' +
        '							</td>' +
        '							<td class="emailUIField" NOWRAP>' +
        '								<div>' +
        '									&nbsp;&nbsp;<select style="width: 500px;" class="ac_input" id="addressFrom{idx}" name="addressFrom{idx}"></select>' +
        '								</div>' +
        '							</td>' +
        '						</tr>' +
        '						<tr>' +
        '							<td class="emailUILabel" NOWRAP>' +
        '								<br /><button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressTO{idx}\')">' +
        '                                   {app_strings.LBL_EMAIL_TO}:' +
        '                               </button>' +
        '							</td>' +
        '							<td class="emailUIField" NOWRAP>' +
                '								<div class="">' +
                '									&nbsp;&nbsp;<input class="sqsEnabled" type="text" size="96" id="addressTO{idx}" title="{app_strings.LBL_EMAIL_TO}" name="addressTO{idx}" onkeypress="ForwardLookUp(\'addressTO{idx}\', \'Emails\', \'EmailQuickSearch\');" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
        '									<span class="rolloverEmail"> <a id="MoreaddressTO{idx}" href="#" style="display: none;">+<span id="DetailaddressTO{idx}">&nbsp;</span></a> </span>' +
        '									<div class="ac_container" id="addressToAC{idx}"></div>' +
        '								</div>' +
        '							</td>' +
        '						</tr>' +
        '						<tr id="add_addr_options_tr{idx}">' +
        '							<td class="emailUILabel" NOWRAP>&nbsp;</td><td class="emailUIField" valign="top" NOWRAP>&nbsp;&nbsp;<span id="cc_span{idx}"><a href="#" onclick="SE.composeLayout.showHiddenAddress(\'cc\',\'{idx}\');">{mod_strings.LBL_ADD_CC}</a></span><span id="bcc_cc_sep{idx}">&nbsp;{mod_strings.LBL_ADD_CC_BCC_SEP}&nbsp;</span><span id="bcc_span{idx}"><a href="#" onclick="SE.composeLayout.showHiddenAddress(\'bcc\',\'{idx}\');">{mod_strings.LBL_ADD_BCC}</a></span></td>' +
        '						</tr>' +
        '						<tr class="yui-hidden" id="cc_tr{idx}">' +
        '							<td class="emailUILabel" NOWRAP>' +
        '                               <button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressCC{idx}\')">' +
        '								{app_strings.LBL_EMAIL_CC}:' +
        '                               </button>' +
        '							</td>' +
        '							<td class="emailUIField" NOWRAP>' +
        '								<div class="ac_autocomplete">' +
                '									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="addressCC{idx}" name="addressCC{idx}"   title="{app_strings.LBL_EMAIL_CC}" onkeypress="ForwardLookUp(\'addressCC{idx}\', \'Emails\', \'EmailQuickSearch\');" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
        '									<span class="rolloverEmail"> <a id="MoreaddressCC{idx}" href="#"  style="display: none;">+<span id="DetailaddressCC{idx}">&nbsp;</span></a> </span>' +
        '									<div class="ac_container" id="addressCcAC{idx}"></div>' +
        '								</div>' +
        '							</td>' +
        '						</tr>' +
        '						<tr class="yui-hidden" id="bcc_tr{idx}">' +
        '							<td class="emailUILabel" NOWRAP>' +
        '                               <button class="button" type="button" onclick="SUGAR.email2.addressBook.selectContactsDialogue(\'addressBCC{idx}\')">' +
        '                               {app_strings.LBL_EMAIL_BCC}:' +
        '                               </button>' +
        '							</td>' +
        '							<td class="emailUIField" NOWRAP>' +
        '								<div class="ac_autocomplete">' +
                '									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="addressBCC{idx}" name="addressBCC{idx}" title="{app_strings.LBL_EMAIL_BCC}" onkeypress="ForwardLookUp(\'addressBCC{idx}\', \'Emails\', \'EmailQuickSearch\');" onkeyup="SE.composeLayout.showAddressDetails(this);">' +
        '									<span class="rolloverEmail"> <a id="MoreaddressBCC{idx}" href="#" style="display: none;">+<span id="DetailaddressBCC{idx}">&nbsp;</span></a> </span>' +
        '									<div class="ac_container" id="addressBccAC{idx}"></div>' +
        '								</div>' +
        '							</td>' +
        '						</tr>' +
        '						<tr>' +
        '							<td class="emailUILabel" NOWRAP width="1%">' +
        '								<label for="emailSubject{idx}">{app_strings.LBL_EMAIL_SUBJECT}:</label>' +
        '							</td>' +
        '							<td class="emailUIField" NOWRAP width="99%">' +
        '								<div class="ac_autocomplete">' +
                '									&nbsp;&nbsp;<input class="ac_input" type="text" size="96" id="emailSubject{idx}" name="subject{idx}" value="">' +
        '								</div>' +
        '							</td>' +
        '						</tr>' +
        '					</table>' +
        '					</div>' +
        '				</td>' +
        '			</tr>' +
        '		</table>' +
        '		<textarea id="htmleditor{idx}" name="htmleditor{idx}" style="width:100%; height: 100px;"></textarea>' +
        '		<div id="divAttachments{idx}" class="ylayout-inactive-content">' +
        '			<div style="padding:5px;">' +
        '				<table cellpadding="2" cellspacing="0" border="0">' +
        '					<tr>' +
        '						<th>' +
        '							<b>{app_strings.LBL_EMAIL_ATTACHMENTS}</b>' +
        '							<br />' +
        '							&nbsp;' +
        '						</th>' +
        '					</tr>' +
        '					<tr>' +
        '						<td>' +
        '							<input type="button" name="add_file_button" onclick="SUGAR.email2.composeLayout.addFileField();" value="{mod_strings.LBL_ADD_FILE}" class="button" />' +
        '							<div id="addedFiles{idx}" name="addedFiles{idx}"></div>' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<td>' +
        '							&nbsp;' +
        '							<br />' +
        '							&nbsp;' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<th>' +
        '							<b>{app_strings.LBL_EMAIL_ATTACHMENTS2}</b>' +
        '							<br />' +
        '							&nbsp;' +
        '						</th>' +
        '					</tr>' +
        '					<tr>' +
        '						<td>' +
        '							<input type="button" name="add_document_button" onclick="SUGAR.email2.composeLayout.addDocumentField({idx});" value="{mod_strings.LBL_ADD_DOCUMENT}" class="button" />' +
        '							<div id="addedDocuments{idx}"></div>' + //<input name="document{idx}0" id="document{idx}0" type="hidden" /><input name="documentId{idx}0" id="documentId{idx}0" type="hidden" /><input name="documentName{idx}0" id="documentName{idx}0" disabled size="30" type="text" /><input type="button" id="documentSelect{idx}0" onclick="SUGAR.email2.selectDocument({idx}0, this);" class="button" value="{app_strings.LBL_EMAIL_SELECT}" /><input type="button" id="documentRemove{idx}0" onclick="SUGAR.email2.deleteDocumentField({idx}0, this);" class="button" value="{app_strings.LBL_EMAIL_REMOVE}" /><br /></div>' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<td>' +
        '							&nbsp;' +
        '							<br />' +
        '							&nbsp;' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<th>' +
        '							<div id="templateAttachmentsTitle{idx}" style="display:none"><b>{app_strings.LBL_EMAIL_ATTACHMENTS3}</b></div>' +
        '							<br />' +
        '							&nbsp;' +
        '						</th>' +
        '					</tr>' +
        '					<tr>' +
        '						<td>' +
        '							<div id="addedTemplateAttachments{idx}"></div>' +
        '						</td>' +
        '					</tr>' +
        '				</table>' +
        '			</div>' +
        '		</div>' +
        '	</form>' +
        '		<div id="divOptions{idx}" class="ylayout-inactive-content"' +
        '             <div style="padding:5px;">' +
        '			<form name="composeOptionsForm{idx}" id="composeOptionsForm{idx}">' +
        '				<table border="0" width="100%">' +
        '					<tr>' +
        '						<td NOWRAP style="padding:2px;">' +
                '							<b>{app_strings.LBL_EMAIL_DEPARTMENT}:</b>' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<td NOWRAP style="padding:2px;">' +
                '							<select name="department" tabindex="2" onchange="selectDeptartment(\'{idx}\',this.value);"><option id="" value="">-none-</option><option id="Operation"  value="Operation">Operation</option><option id="Marketing" value="Marketing">Marketing</option><option id="Accounts" value="Accounts">Accounts</option><option id="Support" value="Support">Support</option></select>' +
        '						</td>' +
        '					</tr>' +
                '					<tr>' +
                '						<td NOWRAP style="padding:2px;">' +
                '							<b>{app_strings.LBL_EMAIL_TEMPLATES}:</b>' +
                '						</td>' +
                '					</tr>' +
                '					<tr>' +
                '						<td NOWRAP style="padding:2px;"><div id="emailTemplate{idx}">' +
                '						</div></td>' +
                '					</tr>' +
        '				</table>' +
        '				<br />' +
        '				<table border="0" width="100%">' +
        '					<tr>' +
        '						<td NOWRAP style="padding:2px;">' +
        '							<b>{app_strings.LBL_EMAIL_SIGNATURES}:</b>' +
        '						</td>' +
        '					</tr>' +
        '					<tr>' +
        '						<td NOWRAP style="padding:2px;">' +
        '							<select name="signatures{idx}" id="signatures{idx}" onchange="SUGAR.email2.composeLayout.setSignature(\'{idx}\');"></select>' +
        '						</td>' +
        '					</tr>' +
        '				</table>' +
        '				<table border="0" width="100%">' +
        '					<tr>' +
        '						<td NOWRAP style="padding:2px;">' +
        '							<input type="checkbox" id="setEditor{idx}" name="setEditor{idx}" value="1" onclick="SUGAR.email2.composeLayout.renderTinyMCEToolBar(\'{idx}\', this.checked);"/>&nbsp;' +
        '							<b>{mod_strings.LBL_SEND_IN_PLAIN_TEXT}</b>' +
        '						</td>' +
        '					</tr>' +
        '				</table>' +
                '				<table border="0" width="100%">' +
                '					<tr>' +
                '						<td NOWRAP style="padding:2px;">' +
                '							<input type="button" name="newCannedResponse" value="New Canned Response" id="newCannedResponse" onclick="showCRdiv(\'{idx}\',id);">' +
                '						</td>' +
                '					</tr>' +
                '				</table>' +
                '				<table border="0" width="100%">' +
                '					<tr>' +
                '						<td NOWRAP style="padding:2px;">' +
                '							<div id="cannedresponsediv{idx}" style="display:none;">' +
                '                                                   <b>Name : </b> <br/> <input type="text" name="name{idx}" id="name{idx}"><br/><br/>' +
                '                                                   <b>Department : </b> <br/> <select name="dept{idx}" id="dept{idx}"><option id="Operation"  value="Operation">Operation</option><option id="Marketing" value="Marketing">Marketing</option><option id="Accounts" value="Accounts">Accounts</option><option id="Support" value="Support">Support</option></select>' +
                '                                                   <input type="button" onclick="return saveCannedResponse(\'{idx}\');" value="Create" name="submit">' +
                '						</td>' +
                '					</tr>' +
                '				</table>' +
        '         </form>' +
        '			</div> ' +
        '		</div>' +
        '</div>';
