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
<script src="custom/include/js/Home/remove_follow_list.js"></script>
<div style="width:100%;vertical-align:middle;">
    <table width="100%" border="0" align="center" class="list view" cellspacing="0" cellpadding="0" id='follow_up'>
        <tr>
            <td align="left">&nbsp;</td>
            <td align="right" nowrap="nowrap" colspan="5">
                <input type="hidden" id="deshlate_id" name="deshlate_id" value="{$deshlate_id}">
                <input type="hidden" id="last_sort" name="last_sort">
                <input type="hidden" id="last_sort_direction" name="last_sort_direction">
                <input type="hidden" name="pagination" id="pagination" value="{$pagginationBy}" />
                <input type="hidden" name="start_pagination" id="start_pagination" value="0" />
                <button title="Start" class="button" id="Start" disabled="" onclick="pagination('start');">
                    <img src="themes/Sugar5/images/start_off.gif" align="absmiddle" border="0" alt="" id="img_first">
                </button>
                <button class="button" disabled="" title="Previous" id="Previous" onclick="pagination('prev');">
                    <img src="themes/Sugar5/images/previous_off.gif" align="absmiddle" border="0" alt="" id="img_prev">
                </button>
                <input type="hidden" name="total_record" id="total_record" value="{$total_record}">
                {if $total_record==0}
                    <span class="pageNumbers" id="pageNumbers">(0 - 0 of 0)</span>
                {elseif $total_record < $pagginationBy}
                    <span class="pageNumbers" id="pageNumbers">(1 - {$total_record} of {$total_record})</span>
                {else}
                    <span class="pageNumbers" id="pageNumbers">(1 - {$pagginationBy} of {$total_record})</span>
                {/if}
                {if $total_record==0 || $total_record <= $pagginationBy}
                    <button title="Next" id="Next" class="button" disabled="" onclick="pagination('next');">
                        <img src="themes/Sugar5/images/next_off.gif" align="absmiddle" border="0" alt="Next" id="img_next">
                    </button>
                    <button title="End" class="button" disabled="" onclick="pagination('end');" id="End">
                        <img src="themes/Sugar5/images/end_off.gif" align="absmiddle" border="0" alt="End" id="img_end">
                    </button>
                {else}
                    <button title="Next" id="Next" class="button"  onclick="pagination('next');">
                        <img src="themes/Sugar5/images/next.gif" align="absmiddle" border="0" alt="Next" id="img_next">
                    </button>
                    <button title="End" class="button" onclick="pagination('end');" id="End">
                        <img src="themes/Sugar5/images/end.gif" align="absmiddle" border="0" alt="End" id="img_end">
                    </button>
                {/if}

            </td>
        </tr>
        <tr>
            {foreach from=$header key=colHeader item=params}
    {if $colHeader=="name"}<th  align="center" width='40%'>{else}<th  align="center">{/if}<a href="#" id="{$colHeader}" onclick="sortRow('{$colHeader}', 'ASC', this);">{$params} {if $params != ""}<img src="themes/Sugar5/images/arrow.gif" width="8" height="10" align="absmiddle" border="0" alt="Sort" id='sort_image_{$colHeader}'>{/if}</a></th>
        {/foreach}
</tr>
{foreach from=$assign_user item=params}
    <tr class="oddListRowS1" id='oddListRowS1'>
        <td width='10px'><img src='custom/image/follow2.png' title='Remove from Watch List' style='height:17px;width:20px;cursor:pointer;' onclick="removeFromFollowList('{$params.id}', '{$params.module}');"></td>
            {if $params.module == 'Task'}
            <td width='10px'><a href="index.php?module={$params.module}&record={$params.id}&action=DetailView"><img src='themes/Sugar5/images/icon_Tasks_32.gif' title='Task' style='height:17px;width:20px;cursor:pointer;' /></a></td>
                {else}
            <td width='10px'><a href="index.php?module={$params.module}&record={$params.id}&action=DetailView"><img src='themes/Sugar5/images/icon_Cases_32.gif' title='Case' style='height:17px;width:20px;cursor:pointer;' /></a></td>
                {/if}
                {foreach from=$params key=colHeader item=element}
                    {if $colHeader !='module' && $colHeader !='id'}
                <td valign="top">{$element}</td>
            {/if}
        {/foreach}
    </tr>
{/foreach}
</table>
</div>