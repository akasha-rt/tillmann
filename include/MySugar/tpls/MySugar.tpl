{*

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




*}
{literal}
<style>
.menu{
	z-index:100;
}

.subDmenu{
	z-index:100;
}

div.moduleTitle {
height: 10px;
	}
</style>
{/literal}

<!-- begin includes for overlib -->
{sugar_getscript file="cache/include/javascript/sugar_grp_overlib.js"}
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
<!-- end includes for overlib -->

<script type='text/javascript' src='{sugar_getjspath file='custom/include/js/jquery.js'}'></script>
<script type="text/javascript" src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/dashlets.js'}"></script>
<script type='text/javascript' src='{sugar_getjspath file='include/MySugar/javascript/MySugar.js'}'></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/swfobject.js'}"></script>
<link rel='stylesheet' href='{sugar_getjspath file='include/ytree/TreeView/css/folders/tree.css'}'>

{$chartResources}
{$mySugarChartResources}

<script type="text/javascript">
var numPages = {$numPages};
var loadedPages = new Array();
loadedPages[0] = '{$loadedPage}';
var activePage = {$activePage};
var theme = '{$theme}';
current_user_id = '{$current_user}';
jsChartsArray = new Array();
var moduleName = '{$module}';
document.body.setAttribute("class", "yui-skin-sam");
{literal}
//window.onload = function () {
var mySugarLoader = new YAHOO.util.YUILoader({
	require : ["my_sugar", "sugar_charts"],
    // Bug #48940 Skin always must be blank
    skin: {
        base: 'blank',
        defaultSkin: ''
    },
	onSuccess: function(){
		initMySugar();
		initmySugarCharts();
		SUGAR.mySugar.maxCount = 	{/literal}{$maxCount}{literal};
		SUGAR.mySugar.homepage_dd = new Array();
		var j = 0;

		{/literal}
		var dashletIds = {$dashletIds};

		{if !$lock_homepage}
			for(i in dashletIds) {ldelim}
				SUGAR.mySugar.homepage_dd[j] = new ygDDList('dashlet_' + dashletIds[i]);
				SUGAR.mySugar.homepage_dd[j].setHandleElId('dashlet_header_' + dashletIds[i]);
				SUGAR.mySugar.homepage_dd[j].onMouseDown = SUGAR.mySugar.onDrag;
				SUGAR.mySugar.homepage_dd[j].afterEndDrag = SUGAR.mySugar.onDrop;
				j++;
			{rdelim}
			{if $hiddenCounter > 0}
			for(var wp = 0; wp <= {$hiddenCounter}; wp++) {ldelim}
				SUGAR.mySugar.homepage_dd[j++] = new ygDDListBoundary('page_'+activePage+'_hidden' + wp);
			{rdelim}
			{/if}
			YAHOO.util.DDM.mode = 1;
		{/if}
		{literal}
		SUGAR.mySugar.renderDashletsDialog();
		SUGAR.mySugar.sugarCharts.loadSugarCharts();                
                SUGAR.mySugar.renderAddPageDialog();
                SUGAR.mySugar.renderChangeLayoutDialog();
        	SUGAR.mySugar.renderLoadingDialog();
		{/literal}
		{literal}
	}

});

mySugarLoader.addModule({
	name :"my_sugar",
	type : "js",
	fullpath: {/literal}"{sugar_getjspath file='include/MySugar/javascript/MySugar.js'}"{literal},
	varName: "initMySugar",
	requires: []
});
mySugarLoader.addModule({
	name :"sugar_charts",
	type : "js",
	fullpath: {/literal}"{sugar_getjspath file="include/SugarCharts/Jit/js/mySugarCharts.js"}"{literal},
	varName: "initmySugarCharts",
	requires: []
});
mySugarLoader.insert();
//}
{/literal}
</script>


{$form_header}
<table cellpadding="0" cellspacing="0" border="0" width="100%" id="tabListContainerTable">
    <tr>
        <td nowrap id="tabListContainerTD">
            <div id="tabListContainer" class="yui-module yui-scroll">
                <!--<div class="yui-hd">
                    <span class="yui-scroll-controls">
                        <a title="scroll left" class="yui-scrollup"><em>scroll left</em></a>
                        <a title="scroll right" class="yui-scrolldown"><em>scroll right</em></a>
                    </span>
                </div>-->

                <div class="yui-bd">
                    <ul class="subpanelTablist" id="tabList">
                        {foreach from=$pages key=pageNum item=pageData}
                            <li id="pageNum_{$pageNum}" {if ($pageNum == $activePage)}class="active"{/if}>
                                <a id="pageNum_{$pageNum}_anchor" class="{$pageData.tabClass}" href="javascript:SUGAR.mySugar.togglePages('{$pageNum}');">
                                    <span id="pageNum_{$pageNum}_input_span" style="display:none;">
                                        <input type="hidden" id="pageNum_{$pageNum}_name_hidden_input" value="{$pageData.pageTitle}"/>
                                        <input type="text" id="pageNum_{$pageNum}_name_input" value="{$pageData.pageTitle}" size="10" onblur="SUGAR.mySugar.savePageTitle('{$pageNum}',this.value);"/>
                                    </span>
                                    <span id="pageNum_{$pageNum}_link_span" class="tabText">
                                        <span id="pageNum_{$pageNum}_title_text" {if !$lock_homepage}ondblclick="SUGAR.mySugar.renamePage('{$pageNum}');"{/if}>{$pageData.pageTitle}</span></span>
                                    <img id="pageNum_{$pageNum}_delete_page_img" class="deletePageImg" style="display: none;" onclick="return SUGAR.mySugar.deletePage()" src='{sugar_getimagepath file="info-del.png"}' alt='{$lblLnkHelp}' border='0' align='absmiddle'>
                                </a>
                            </li>
                        {/foreach}	
                    </ul>
                </div>

            </div>
            <div id="addPage">
                <a href='#' id="add_page" onclick="return SUGAR.mySugar.showAddPageDialog();"><img src='{sugar_getimagepath file="info-add.gif"}' alt='{$lblLnkHelp}' border='0' align='absmiddle'></a>
            </div>
        </td>

        {if !$lock_homepage}
            <td nowrap align="right">
                <div id="dashletCtrls">
                    <a href="#" id="add_dashlets" onclick="return SUGAR.mySugar.showDashletsDialog();" class='utilsLink'>
                        <img src='{sugar_getimagepath file="info-add.png"}' alt='{$lblLnkHelp}' border='0' align='absmiddle'>
                        {$mod.LBL_ADD_DASHLETS}
                    </a>
                    <a href="#" id="change_layout" onclick="return SUGAR.mySugar.showChangeLayoutDialog();" class='utilsLink'>
                        <img src='{sugar_getimagepath file="info-layout.png"}' alt='{$lblLnkHelp}' border='0' align='absmiddle'>
                        {$app.LBL_CHANGE_LAYOUT}
                    </a>
                    <a href="#" id="rename_tab" onclick="return SUGAR.mySugar.rename_Page();" class='utilsLink'>
                        <img src='{sugar_getimagepath file="RenameTabs.gif"}' alt='{$lblLnkHelp}' border='0' align='absmiddle'>
                        <!-- {$app.LBL_RENAME_TAB} --> Rename Tab
                    </a>
                </div>
            </td>
        {/if}
    </tr>
</table>
<div class="clear"></div>
<div id="pageContainer" class="yui-skin-sam">
<div id="pageNum_{$activePage}_div">
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5px;">
 	<tr>
                <td></td>
                <td rowspan="3">
                 <img height="1" border="0" width="25" src="themes/Sugar5/images/blank.gif">
                 </td>
                <td></td>                	 	
	 	<td rowspan="3">
                <img height="1" border="0" width="25" src="themes/Sugar5/images/blank.gif">
                </td>
	</tr>
	<tr height="350px">
		{counter assign=hiddenCounter start=0 print=false}
		{foreach from=$columns key=colNum item=data}
		<td valign='top' width='{$data.width}'>
			<ul class='noBullet' id='col_{$activePage}_{$colNum}'>
				<li id='page_{$activePage}_hidden{$hiddenCounter}b' style='height: 5px; margin-top:12px;' class='noBullet'>&nbsp;&nbsp;&nbsp;</li>
		        {foreach from=$data.dashlets key=id item=dashlet}
				<li class='noBullet' id='dashlet_{$id}'>
					<div id='dashlet_entire_{$id}' class='dashletPanel'>
						{$dashlet.script}
					{$dashlet.displayHeader}
						{$dashlet.display}
                        {$dashlet.displayFooter}
                  </div>
				</li>
				{/foreach}
				<li id='page_{$activePage}_hidden{$hiddenCounter}' style='height: 5px' class='noBullet'>&nbsp;&nbsp;&nbsp;</li>
			</ul>
		</td>
		{counter}
		{/foreach}
	</tr>
</table>
	</div>

	{foreach from=$divPages key=divPageIndex item=divPageNum}
	<div id="pageNum_{$divPageNum}_div" style="display:none;">
	</div>
	{/foreach}


<div id="addPageDialog" style="display:none;">
    <div class="hd">{$lblAddPage}</div>
    <div class="bd">
        <form method="POST" action="index.php?module=Home&action=DynamicAction&DynamicAction=addTab&to_pdf=1">
            <label>{$lblPageName}: </label><input type="textbox" name="pageName" /><br /><br />
            <label>{$lblNumberOfColumns}:</label>
            <table align="center" cellpadding="8">
                <tr>
                    <td align="center"><img src="{sugar_getimagepath file='icon_Column_1.gif'}" border="0"/><br /><input type="radio" name="numColumns" value="1" /></td>
                    <td align="center"><img src="{sugar_getimagepath file='icon_Column_2.gif'}" border="0"/><br /><input type="radio" name="numColumns" value="2" checked="yes" /></td>
                    <td align="center"><img src="{sugar_getimagepath file='icon_Column_3.gif'}" border="0"/><br /><input type="radio" name="numColumns" value="3" /></td>
                </tr>
            </table>
        </form>
    </div>
</div>					

<div id="changeLayoutDialog" style="display:none;">
    <div class="hd">{$lblChangeLayout}</div>
    <div class="bd">
        <label>{$lblNumberOfColumns}:</label>
        <br /><br />
        <table align="center" cellpadding="15">
            <tr>
                <td align="center"><a href="javascript:SUGAR.mySugar.changePageLayout(1);"><img src="{sugar_getimagepath file='icon_Column_1.gif'}" border="0"/></a></td>
                <td align="center"><a href="javascript:SUGAR.mySugar.changePageLayout(2);"><img src="{sugar_getimagepath file='icon_Column_2.gif'}" border="0"/></a></td>
                <td align="center"><a href="javascript:SUGAR.mySugar.changePageLayout(3);"><img src="{sugar_getimagepath file='icon_Column_3.gif'}" border="0"/></a></td>						
            </tr>
        </table>
    </div>
</div>
	<div id="dashletsDialog" style="display:none;">
		<div class="hd" id="dashletsDialogHeader"><a href="javascript:void(0)" onClick="javascript:SUGAR.mySugar.closeDashletsDialog();">
			<div class="container-close">&nbsp;</div></a>{$lblAdd}
		</div>
		<div class="bd" id="dashletsList">
			<form></form>
		</div>

	</div>


</div>


<!--<script type="text/javascript">

    YAHOO.util.Event.addListener(window, 'load', SUGAR.mySugar.init); 
</script>-->
