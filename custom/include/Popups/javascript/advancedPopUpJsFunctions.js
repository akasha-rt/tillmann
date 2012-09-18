function popup_saved_search_action(action,delete_lang){        
    if(action=='delete'){
        if(!confirm(delete_lang))return;
    }
    if(action=='save'){  
        if(document.popup_query_form.saved_search_name.value.replace(/^\s*|\s*$/g,'')==''){               
            alert(SUGAR.language.get('app_strings','LBL_SAVED_SEARCH_ERROR'));
            return;
        }     
 
    }

    if(document.popup_query_form.saved_search_action)
    { 
        document.popup_query_form.saved_search_action.value=action;
        document.popup_query_form.search_module.value=document.popup_query_form.module.value;
        document.popup_query_form.popup_return_action.value='Popup';    
        document.popup_query_form.module.value='SavedSearch';
        document.popup_query_form.action.value='index';    
    }
    SUGAR.ajaxUI.submitForm(document.popup_query_form);
};
function popup_shortcut_select(selectBox,module){
    selecturl='index.php?module=SavedSearch&search_module='+module+'&action=index&popup_return_action=Popup&saved_search_select='+selectBox.options[selectBox.selectedIndex].value
    if(typeof(document.getElementById('searchFormTab'))!='undefined'){
        selecturl=selecturl+'&searchFormTab='+document.popup_query_form.searchFormTab.value;
    }
    if(document.getElementById('showSSDIV')&&typeof(document.getElementById('showSSDIV')!='undefined')){
        selecturl=selecturl+'&showSSDIV='+document.getElementById('showSSDIV').value;
    }
    document.location.href=selecturl;
};
