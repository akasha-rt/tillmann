<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        {{foreach name=colIteration from=$formData key=col item=colData}}

        {counter assign=index}
        {math equation="left % right"
   		  left=$index
          right=$templateMeta.maxColumns
          assign=modVal
        }
        {if ($index % $templateMeta.maxColumns == 1 && $index != 1)}
            {if $isHelperShown==0}
                {assign var="isHelperShown" value="1"}
                <td class="helpIcon" width="*">
                    <img alt="{$APP.LBL_SEARCH_HELP_TITLE}" id="helper_popup_image" border="0" src='{sugar_getimagepath file="help-dashlet.gif"}' onmouseover="return overlib(SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TEXT'), STICKY, MOUSEOFF, 1000,WIDTH, 700, LEFT,CAPTION,'<div style=\'float:left\'>'+SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TITLE')+'</div>', CLOSETEXT, '<div style=\'float: right\'><img border=0 style=\'margin-left:2px; margin-right: 2px;\' src={sugar_getimagepath file='close.gif'}></div>',CLOSETITLE, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP'), CLOSECLICK,FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass');" class="help-search">
                </td>
            {else}
                <td>&nbsp;</td>
            {/if}
        </tr><tr>
        {/if}

        <td scope="row" nowrap="nowrap" width='{{$templateMeta.widths.label}}%' >
            {{if isset($colData.field.label)}}	
            <label for='{{$colData.field.name}}'>{sugar_translate label='{{$colData.field.label}}' module='{{$module}}'}</label>
            {{elseif isset($fields[$colData.field.name])}}
            <label for='{{$fields[$colData.field.name].name}}'>{sugar_translate label='{{$fields[$colData.field.name].vname}}' module='{{$module}}'}</label>
            {{/if}}
        </td>
        <td  nowrap="nowrap" width='{{$templateMeta.widths.field}}%'>
            {{if $fields[$colData.field.name]}}
            {{sugar_field parentFieldArray='fields' vardef=$fields[$colData.field.name] displayType=$displayType displayParams=$colData.field.displayParams typeOverride=$colData.field.type formName=$form_name}}
            {{/if}}
        </td>
        {{/foreach}}
    </tr>
    <tr>
        <td colspan='20'>
            &nbsp;
        </td>
    </tr>	
    {if $DISPLAY_SAVED_SEARCH}
        <tr>
            <td colspan='2'>
                <a class='tabFormAdvLink' onhover href='javascript:toggleInlineSearch()'>
                {capture assign="alt_show_hide"}{sugar_translate label='LBL_ALT_SHOW_OPTIONS'}{/capture}
                {sugar_getimage alt=$alt_show_hide name="advanced_search" ext=".gif" other_attributes='border="0" id="up_down_img" '}&nbsp;{$APP.LNK_SAVED_VIEWS}
            </a><br>
            <input type='hidden' id='showSSDIV' name='showSSDIV' value='{$SHOWSSDIV}'><p>
        </td>
        <td scope='row' width='10%' nowrap="nowrap">
            {sugar_translate label='LBL_SAVE_SEARCH_AS' module='SavedSearch'}:
        </td>
        <td width='30%' nowrap>
            <input type='text' name='saved_search_name'>
            <input type='hidden' name='search_module' value=''>
            <input type='hidden' name='saved_search_action' value=''>
            <input type='hidden' name='popup_return_action' value=''>                

            <input title='{$APP.LBL_SAVE_BUTTON_LABEL}' value='{$APP.LBL_SAVE_BUTTON_LABEL}' class='button' type='button' name='saved_search_submit' onclick='SUGAR.savedViews.setChooser(); return popup_saved_search_action("save");'>
        </td>
        <td scope='row' width='10%' nowrap="nowrap">
            {sugar_translate label='LBL_MODIFY_CURRENT_SEARCH' module='SavedSearch'}:
        </td>
        <td width='30%' nowrap>
            <input class='button' onclick='SUGAR.savedViews.setChooser(); return popup_saved_search_action("update")' value='{$APP.LBL_UPDATE}' title='{$APP.LBL_UPDATE}' name='ss_update' id='ss_update' type='button' >
            <input class='button' onclick='return popup_saved_search_action("delete", "{sugar_translate label='LBL_DELETE_CONFIRM' module='SavedSearch'}")' value='{$APP.LBL_DELETE}' title='{$APP.LBL_DELETE}' name='ss_delete' id='ss_delete' type='button'>
            <br><span id='curr_search_name'></span>
        </td>
    </tr>

    <tr>
        <td colspan='6'>
            <div style='{$DISPLAYSS}' id='inlineSavedSearch' >
                {$SAVED_SEARCH}
            </div>
        </td>
    </tr>

{/if}


<tr>
    <td colspan='5'>
        <input tabindex='2' title='{$APP.LBL_SEARCH_BUTTON_TITLE}' accessKey='{$APP.LBL_SEARCH_BUTTON_KEY}' onclick='SUGAR.savedViews.setChooser()' class='button' type='submit' name='button' value='{$APP.LBL_SEARCH_BUTTON_LABEL}' id='search_form_submit'/>&nbsp;
        <input tabindex='2' title='{$APP.LBL_CLEAR_BUTTON_TITLE}' accessKey='{$APP.LBL_CLEAR_BUTTON_KEY}' onclick='SUGAR.searchForm.clear_form(this.form); document.getElementById("saved_search_select").options[0].selected=true; return false;' class='button' type='button' name='clear' id='search_form_clear' value='{$APP.LBL_CLEAR_BUTTON_LABEL}'/>
        {if $DOCUMENTS_MODULE}
            &nbsp;<input title="{$APP.LBL_BROWSE_DOCUMENTS_BUTTON_TITLE}" accessKey="{$APP.LBL_BROWSE_DOCUMENTS_BUTTON_KEY}" type="button" class="button" value="{$APP.LBL_BROWSE_DOCUMENTS_BUTTON_LABEL}" onclick='open_popup("Documents", 600, 400, "&caller=Documents", true, false, "");' />
        {/if}        
        <span class='white-space'>
            &nbsp;&nbsp;&nbsp;{if $SAVED_SEARCHES_OPTIONS}&nbsp;&nbsp;&nbsp;<b>{$APP.LBL_SAVED_SEARCH_SHORTCUT}</b>&nbsp;
            {$SAVED_SEARCHES_OPTIONS} {/if}
                <span id='go_btn_span' style='display:none'><input tabindex='2' title='go_select' id='go_select'  onclick='SUGAR.searchForm.clear_form(this.form);' class='button' type='button' name='go_select' value=' {$APP.LBL_GO_BUTTON_LABEL} '/></span>	
            </span>
        </td>
        <td class="help">
            {if $DISPLAY_SEARCH_HELP}
                <img  border='0' src='{sugar_getimagepath file="help-dashlet.gif"}' onmouseover="return overlib(SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TEXT'), STICKY, MOUSEOFF,1000,WIDTH, 700, LEFT,CAPTION,'<div style=\'float:left\'>'+SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TITLE')+'</div>', CLOSETEXT, '<div style=\'float: right\'><img border=0 style=\'margin-left:2px; margin-right: 2px;\' src={sugar_getimagepath file='close.gif'}></div>',CLOSETITLE, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP'), CLOSECLICK,FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass');" class="help-search">
            {/if}
        </td>
    </tr>

</table>

<script>
    {literal}
	if(typeof(loadSSL_Scripts)=='function'){
		loadSSL_Scripts();
	}
    {/literal}	
</script>