/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
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
function Datetimecombo(datetime,field,timeformat,tabindex,showCheckbox,checked,allowEmptyHM){this.datetime=datetime;this.allowEmptyHM=allowEmptyHM;if(typeof this.datetime=="undefined"||datetime==''||trim(datetime).length<10){this.datetime='';var d=new Date();var month=d.getMonth();var date=d.getDate();var year=d.getYear();var hours=d.getHours();var minutes=d.getMinutes();}
this.fieldname=field;if(datetime!='')
{parts=datetime.split(' ');this.hrs=parseInt(parts[1].substring(0,2),10);this.mins=parseInt(parts[1].substring(3,5),10);}
this.timeformat=timeformat;this.tabindex=tabindex==null||isNaN(tabindex)?1:tabindex;this.timeseparator=this.timeformat.substring(2,3);this.has12Hours=/^11/.test(this.timeformat);this.hasMeridiem=/am|pm/i.test(this.timeformat);if(this.hasMeridiem){this.pm=/pm/.test(this.timeformat);}
this.meridiem=this.hasMeridiem?trim(this.datetime.substring(16)):'';this.datetime=this.datetime.substr(0,10);this.showCheckbox=showCheckbox;this.checked=parseInt(checked);document.getElementById(this.fieldname+'_date').value=this.datetime;if(this.mins>0&&this.mins<15){this.mins=15;}else if(this.mins>15&&this.mins<30){this.mins=30;}else if(this.mins>30&&this.mins<45){this.mins=45;}else if(this.mins>45){this.hrs+=1;this.mins=0;if(this.hasMeridiem&&this.hrs==12){if(this.meridiem=="pm"||this.meridiem=="am"){if(this.meridiem=="pm"){this.meridiem="am";}else{this.meridiem="pm";}}else{if(this.meridiem=="PM"){this.meridiem="AM";}else{this.meridiem="PM";}}}
if(this.hrs>12){this.hrs=this.hrs-12;}}}
Datetimecombo.prototype.jsscript=function(callback){text='\nfunction update_'+this.fieldname+'(calendar) {';text+='\nd = document.getElementById("'+this.fieldname+'_date").value;';text+='\nh = document.getElementById("'+this.fieldname+'_hours").value;';text+='\nm = document.getElementById("'+this.fieldname+'_minutes").value;';text+='\nnewdate = d + " " + h + "'+this.timeseparator+'" + m;';if(this.hasMeridiem){text+='\nif(typeof document.getElementById("'+this.fieldname+'_meridiem") != "undefined") {';text+='\n   newdate += document.getElementById("'+this.fieldname+'_meridiem").value;';text+='\n}';}
text+='\nif(trim(newdate) =="'+this.timeseparator+'") newdate="";';text+='\ndocument.getElementById("'+this.fieldname+'").value = newdate;';text+='\n'+callback;text+='\n}';return text;}
Datetimecombo.prototype.html=function(callback){var text='<select class="datetimecombo_time" size="1" id="'+this.fieldname+'_hours" tabindex="'+this.tabindex+'" onchange="combo_'+this.fieldname+'.update(); '+callback+'">';var h1=this.has12Hours?1:0;var h2=this.has12Hours?12:23;if(this.allowEmptyHM){text+='<option></option>';}
for(i=h1;i<=h2;i++){val=i<10?"0"+i:i;text+='<option value="'+val+'" '+(i==this.hrs?"SELECTED":"")+'>'+val+'</option>';}
text+='\n</select>&nbsp;';text+=this.timeseparator;text+='\n&nbsp;<select class="datetimecombo_time" size="1" id="'+this.fieldname+'_minutes" tabindex="'+this.tabindex+'" onchange="combo_'+this.fieldname+'.update(); '+callback+'">';if(this.allowEmptyHM){text+='\n<option></option>';}
text+='\n<option value="00" '+(this.mins==0?"SELECTED":"")+'>00</option>';text+='\n<option value="15" '+(this.mins==15?"SELECTED":"")+'>15</option>';text+='\n<option value="30" '+(this.mins==30?"SELECTED":"")+'>30</option>';text+='\n<option value="45" '+(this.mins==45?"SELECTED":"")+'>45</option>';text+='\n</select>';if(this.hasMeridiem){text+='\n&nbsp;';text+='\n<select class="datetimecombo_time" size="1" id="'+this.fieldname+'_meridiem" tabindex="'+this.tabindex+'" onchange="combo_'+this.fieldname+'.update(); '+callback+'">';if(this.allowEmptyHM){text+='\n<option></option>';}
text+='\n<option value="'+(this.pm?"am":"AM")+'" '+(/am/i.test(this.meridiem)?"SELECTED":"")+'>'+(this.pm?"am":"AM")+'</option>';text+='\n<option value="'+(this.pm?"pm":"PM")+'" '+(/pm/i.test(this.meridiem)?"SELECTED":"")+'>'+(this.pm?"pm":"PM")+'</option>';text+='\n</select>';}
if(this.showCheckbox){text+='\n<input style="visibility:hidden;" type="checkbox" name="'+this.fieldname+'_flag" id="'+this.fieldname+'_flag" tabindex="'+this.tabindex+'" onchange="combo_'+this.fieldname+'.update(); '+callback+'" '+(this.checked?'CHECKED':'')+'>';}
return text;};Datetimecombo.prototype.update=function(updateListeners){if(typeof(updateListeners)=="undefined")
updateListeners=true;var d=window.document.getElementById(this.fieldname+'_date');var h=window.document.getElementById(this.fieldname+'_hours');var m=window.document.getElementById(this.fieldname+'_minutes');var mer=document.getElementById(this.fieldname+"_meridiem");if(d.value==""){h.selectedIndex=0;m.selectedIndex=0;if(mer)mer.selectedIndex=0;}else{if(this.allowEmptyHM){if(h.selectedIndex==0)h.selectedIndex=12;if(m.selectedIndex==0)m.selectedIndex=1;if(mer&&(mer.selectedIndex==0))mer.selectedIndex=1;}}
var newdate=d.value+' '+h.value+this.timeseparator+m.value;if(this.hasMeridiem){ampm=document.getElementById(this.fieldname+"_meridiem").value;newdate+=ampm;}
if(trim(newdate)==""+this.timeseparator+""){newdate='';}
document.getElementById(this.fieldname).value=newdate;if(updateListeners)
SUGAR.util.callOnChangeListers(this.fieldname);if(this.showCheckbox){flag=this.fieldname+'_flag';date=this.fieldname+'_date';hours=this.fieldname+'_hours';mins=this.fieldname+'_minutes';if(document.getElementById(flag).checked){document.getElementById(flag).value=1;document.getElementById(this.fieldname).value='';document.getElementById(date).disabled=true;document.getElementById(hours).disabled=true;document.getElementById(mins).disabled=true;}else{document.getElementById(flag).value=0;document.getElementById(date).disabled=false;document.getElementById(hours).disabled=false;document.getElementById(mins).disabled=false;}}};
