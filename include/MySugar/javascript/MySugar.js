/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
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
initMySugar = function(){
SUGAR.mySugar=function(){
    var originalLayout=null;
    var configureDashletId=null;
    var currentDashlet=null;
    var leftColumnInnerHTML=null;
    var leftColObj=null;
    var maxCount;
    var warningLang;
    var closeDashletsDialogTimer=null;
    var num_pages=numPages;
    var activeTab=activePage;
    var current_user=current_user_id;
    var module=moduleName;
    var charts=new Object();
    if(module=='Dashboard'){
        cookiePageIndex=current_user+"_activeDashboardPage";
    }
    else{
        cookiePageIndex=current_user+"_activePage";
    }
    var homepage_dd;
    return{
        clearChartsArray:function(){
            charts[activeTab]=new Object();
        },
        addToChartsArray:function(name,xmlFile,width,height,styleSheet,colorScheme,langFile){
            if(charts[activeTab]==null){
                charts[activeTab]=new Object();
            }
            charts[activeTab][name]=new Object();
            charts[activeTab][name]['name']=name;
            charts[activeTab][name]['xmlFile']=xmlFile;
            charts[activeTab][name]['width']=width;
            charts[activeTab][name]['height']=height;
            charts[activeTab][name]['styleSheet']=styleSheet;
            charts[activeTab][name]['colorScheme']=colorScheme;
            charts[activeTab][name]['langFile']=langFile;
        },
        loadSugarChart:function(name,xmlFile,width,height,styleSheet,colorScheme,langFile){
            loadChartSWF(name,xmlFile,width,height,styleSheet,colorScheme,langFile);
        },
        loadSugarCharts:function(){
            for(id in charts[activeTab]){
                if(id!='undefined'){
                    SUGAR.mySugar.loadSugarChart(charts[activeTab][id]['name'],charts[activeTab][id]['xmlFile'],charts[activeTab][id]['width'],charts[activeTab][id]['height'],charts[activeTab][id]['styleSheet'],charts[activeTab][id]['colorScheme'],charts[activeTab][id]['langFile']);
                }
            }
        },
        getLayout:function(asString){
            columns=new Array();
            for(je=0;je<3;je++){
                dashlets=document.getElementById('col_'+activeTab+'_'+je);
                if(dashlets!=null){
                    dashletIds=new Array();
                    for(wp=0;wp<dashlets.childNodes.length;wp++){
                        if(typeof dashlets.childNodes[wp].id!='undefined'&&dashlets.childNodes[wp].id.match(/dashlet_[\w-]*/)){
                            dashletIds.push(dashlets.childNodes[wp].id.replace(/dashlet_/,''));
                        }
                    }
                    if(asString)
                        columns[je]=dashletIds.join(',');else
                        columns[je]=dashletIds;
                }
            }
            if(asString)return columns.join('|');else return columns;
        },
        onDrag:function(e,id){
            originalLayout=SUGAR.mySugar.getLayout(true);
        },
        onDrop:function(e,id){
            newLayout=SUGAR.mySugar.getLayout(true);
            if(originalLayout!=newLayout){
                SUGAR.mySugar.saveLayout(newLayout);
            SUGAR.mySugar.sugarCharts.loadSugarCharts();
            }
        },
        saveLayout:function(order){
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_SAVING_LAYOUT'));
            var success=function(data){
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_SAVED_LAYOUT'));
                window.setTimeout('ajaxStatus.hideStatus()',2000);
            }
            url='index.php?to_pdf=1&module='+module+'&action=DynamicAction&DynamicAction=saveLayout&layout='+order+'&selectedPage='+activeTab;
            var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                success:success,
                failure:success
            });
        },
        //Change by bc to change the layout in the multiple home page tabs
        changeLayout:function(numCols){
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_SAVING_LAYOUT'));
            var success=function(data){
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_SAVED_LAYOUT'));
                window.setTimeout('ajaxStatus.hideStatus()',2000);
                var pageNum=data.responseText;
                //When working with test instance it prepads \n to pagenum so stip that \n and \r if its there!
                //For F.F
                pageNum = pageNum.replace(/\n/g,'');
                //For IE and Opera
                pageNum = pageNum.replace(/\s/g,' ').replace(/  ,/g,'');  
                SUGAR.mySugar.retrievePage(pageNum);
            }
            url='index.php?to_pdf=1&module='+module+'&action=DynamicAction&DynamicAction=changeLayout&selectedPage='+activeTab+'&numColumns='+numCols;
            var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                success:success,
                failure:success
            });
        },
        //end
        
        uncoverPage:function(id){
            if(!SUGAR.isIE){
                document.getElementById('dlg_c').style.display='none';
            }
            configureDlg.hide();
            if(document.getElementById('dashletType')==null){
                dashletType='';
            }else{
                dashletType=document.getElementById('dashletType').value;
            }
            SUGAR.mySugar.retrieveDashlet(SUGAR.mySugar.configureDashletId,dashletType);
        },
        configureDashlet:function(id){
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
            configureDlg=new YAHOO.widget.SimpleDialog("dlg",{
                visible:false,
                width:"510",
                effect:[{
                    effect:YAHOO.widget.ContainerEffect.SLIDE,
                    duration:0.5
                },{
                    effect:YAHOO.widget.ContainerEffect.FADE,
                    duration:0.5
                }],
                fixedcenter:true,
                modal:true,
                draggable:false
            });
            fillInConfigureDiv=function(data){
                ajaxStatus.hideStatus();
                try{
                    eval(data.responseText);
                }
                catch(e){
                    result=new Array();
                    result['header']='error';
                    result['body']='There was an error handling this request.';
                }
                configureDlg.setHeader(result['header']);
                configureDlg.setBody(result['body']);
                var listeners=new YAHOO.util.KeyListener(document,{
                    keys:27
                },{
                    fn:function(){
                        this.hide();
                    },
                    scope:configureDlg,
                    correctScope:true
                });
                configureDlg.cfg.queueProperty("keylisteners",listeners);
                configureDlg.render(document.body);
                configureDlg.show();
                configureDlg.configFixedCenter(null,false);
                SUGAR.util.evalScript(result['body']);
            }
            SUGAR.mySugar.configureDashletId=id;
            var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=1&module='+module+'&action=DynamicAction&DynamicAction=configureDashlet&id='+id,{
                success:fillInConfigureDiv,
                failure:fillInConfigureDiv
            },null);
        },
        retrieveDashlet:function(id,url,callback,dynamic){
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
            if(!url){
                url='index.php?action=DynamicAction&DynamicAction=displayDashlet&session_commit=1&module='+module+'&to_pdf=1&id='+id;
                is_chart_dashlet=false;
            }
            else if(url=='predefined_chart'){
                url='index.php?action=DynamicAction&DynamicAction=displayDashlet&session_commit=1&module='+module+'&to_pdf=1&id='+id;
                scriptUrl='index.php?action=DynamicAction&DynamicAction=getPredefinedChartScript&session_commit=1&module='+module+'&to_pdf=1&id='+id;
                is_chart_dashlet=true;
            }
            if(dynamic){
                url+='&dynamic=true';
            }
            var fillInDashlet=function(data){
                ajaxStatus.hideStatus();
                if(data){
                    SUGAR.mySugar.currentDashlet.innerHTML=data.responseText;
                }
                SUGAR.util.evalScript(data.responseText);
                if(callback)callback();
                var processChartScript=function(scriptData){
                    SUGAR.util.evalScript(scriptData.responseText);
            SUGAR.mySugar.sugarCharts.loadSugarCharts(activePage);
            //SUGAR.mySugar.sugarCharts.loadSugarChart(charts[activeTab][id]['name'],charts[activeTab][id]['xmlFile'],charts[activeTab][id]['width'],charts[activeTab][id]['height'],charts[activeTab][id]['styleSheet'],charts[activeTab][id]['colorScheme'],charts[activeTab][id]['langFile']);
                }
                if(typeof(is_chart_dashlet)=='undefined'){
                    is_chart_dashlet=false;
                }
                if(is_chart_dashlet){
                    var chartScriptObj=YAHOO.util.Connect.asyncRequest('GET',scriptUrl,{
                        success:processChartScript,
                        failure:processChartScript
                    },null);
                }
                SUGAR.mySugar.attachToggleToolsetEvent(id);
            }
            SUGAR.mySugar.currentDashlet=document.getElementById('dashlet_entire_'+id);
            var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                success:fillInDashlet,
                failure:fillInDashlet
            },null);
            return false;
        },
        setChooser:function(){
            var displayColumnsDef=new Array();
            var hideTabsDef=new Array();
            var left_td=document.getElementById('display_tabs_td');
            var right_td=document.getElementById('hide_tabs_td');
            var displayTabs=left_td.getElementsByTagName('select')[0];
            var hideTabs=right_td.getElementsByTagName('select')[0];
            for(i=0;i<displayTabs.options.length;i++){
                displayColumnsDef.push(displayTabs.options[i].value);
            }
            if(typeof hideTabs!='undefined'){
                for(i=0;i<hideTabs.options.length;i++){
                    hideTabsDef.push(hideTabs.options[i].value);
                }
            }
            document.getElementById('displayColumnsDef').value=displayColumnsDef.join('|');
            document.getElementById('hideTabsDef').value=hideTabsDef.join('|');
        },
        deleteDashlet:function(id){
            if(confirm(SUGAR.language.get('app_strings','LBL_REMOVE_DASHLET_CONFIRM'))){
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_REMOVING_DASHLET'));
                del=function(){
                    var success=function(data){
                        dashlet=document.getElementById('dashlet_'+id);
                        dashlet.parentNode.removeChild(dashlet);
                        ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_REMOVED_DASHLET'));
                        window.setTimeout('ajaxStatus.hideStatus()',2000);
                    }
                    var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=1&module='+module+'&action=DynamicAction&DynamicAction=deleteDashlet&activePage='+activeTab+'&id='+id,{
                        success:success,
                        failure:success
                    },null);
                }
                var anim=new YAHOO.util.Anim('dashlet_entire_'+id,{
                    height:{
                        to:1
                    }
                },.5);
                anim.onComplete.subscribe(del);
                document.getElementById('dashlet_entire_'+id).style.overflow='hidden';
                anim.animate();
                return false;
            }
            return false;
        },
        addDashlet:function(id,type,type_module){
            ajaxStatus.hideStatus();
            columns=SUGAR.mySugar.getLayout();
            var num_dashlets=columns[0].length;
            if(typeof columns[1]==undefined){
                num_dashlets=num_dashlets+columns[1].length;
            }
            if((num_dashlets)>=SUGAR.mySugar.maxCount){
                alert(SUGAR.language.get('app_strings','LBL_MAX_DASHLETS_REACHED'));
                return;
            }
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_ADDING_DASHLET'));
            var success=function(data){
                if(data.responseText=='userpref_error'){
                    SUGAR.mySugar.closeDashletsDialog();
                    ajaxStatus.flashStatus(SUGAR.language.get('app_strings','ERROR_USER_PREFS_DASH'),7000);
                    return;
                }
                colZero=document.getElementById('col_'+activeTab+'_0');
                newDashlet=document.createElement('li');
                newDashlet.id='dashlet_'+data.responseText;
                newDashlet.className='noBullet active';
                newDashlet.innerHTML='<div style="position: absolute; top: -1000px; overflow: hidden;" id="dashlet_entire_'+data.responseText+'"></div>';
                colZero.insertBefore(newDashlet,colZero.firstChild);
                var finishRetrieve=function(){
                    dashletEntire=document.getElementById('dashlet_entire_'+data.responseText);
                    dd=new ygDDList('dashlet_'+data.responseText);
                    dd.setHandleElId('dashlet_header_'+data.responseText);
                    dd.onMouseDown=SUGAR.mySugar.onDrag;
                    dd.onDragDrop=SUGAR.mySugar.onDrop;
                    ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_ADDED_DASHLET'));
                    dashletRegion=YAHOO.util.Dom.getRegion(dashletEntire);
                    dashletEntire.style.position='relative';
                    dashletEntire.style.height='1px';
                    dashletEntire.style.top='0px';
                    dashletEntire.className='dashletPanel';
                    SUGAR.mySugar.attachToggleToolsetEvent(data.responseText);
                    var anim=new YAHOO.util.Anim('dashlet_entire_'+data.responseText,{
                        height:{
                            to:dashletRegion.bottom-dashletRegion.top
                        }
                    },.5);
                    anim.onComplete.subscribe(function(){
                        document.getElementById('dashlet_entire_'+data.responseText).style.height='100%';
                    });
                    anim.animate();
                    newLayout=SUGAR.mySugar.getLayout(true);
                    SUGAR.mySugar.saveLayout(newLayout);
                }
                if(type=='module'||type=='web'){
                    url=null;
                    type='module';
                }
                else if(type=='predefined_chart'){
                    url='predefined_chart';
                    type='predefined_chart';
                }
                else if(type=='chart'){
                    url='chart';
                    type='chart';
                }
                SUGAR.mySugar.retrieveDashlet(data.responseText,url,finishRetrieve,true);
            }
            var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=1&module='+module+'&action=DynamicAction&DynamicAction=addDashlet&activeTab='+activeTab+'&id='+id+'&type='+type+'&type_module='+type_module,{
                success:success,
                failure:success
            },null);
            return false;
        },
        showDashletsDialog:function(){
            
            columns=SUGAR.mySugar.getLayout();
            var num_dashlets=0;
            var i=0;
            for(i=0;i<3;i++){
                if(typeof columns[i]!="undefined"){
                    num_dashlets=num_dashlets+columns[i].length;
                }
            }
            if((num_dashlets)>=SUGAR.mySugar.maxCount){
                alert(SUGAR.language.get('app_strings','LBL_MAX_DASHLETS_REACHED'));
                return;
            }
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
            var success=function(data){
                eval(data.responseText);
                dashletsListDiv=document.getElementById('dashletsList');
                dashletsListDiv.innerHTML=response['html'];
                document.getElementById('dashletsDialog_c').style.display='';
                SUGAR.mySugar.dashletsDialog.show();
                eval(response['script']);
                ajaxStatus.hideStatus();
            }
            var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=true&module='+module+'&action=DynamicAction&DynamicAction=dashletsDialog',{
                success:success,
                failure:success
            });
            return false;
        },
        closeDashletsDialog:function(){
          
            SUGAR.mySugar.dashletsDialog.hide();
            window.setTimeout("document.getElementById('dashletsDialog_c').style.display = 'none';",2000);
        },
        toggleDashletCategories:function(category){
            document.getElementById('search_string').value='';
            document.getElementById('searchResults').innerHTML='';
            var moduleTab=document.getElementById('moduleCategory');
            var moduleTabAnchor=document.getElementById('moduleCategoryAnchor');
            var moduleListDiv=document.getElementById('moduleDashlets');
            var chartTab=document.getElementById('chartCategory');
            var chartTabAnchor=document.getElementById('chartCategoryAnchor');
            var chartListDiv=document.getElementById('chartDashlets');
            var toolsTab=document.getElementById('toolsCategory');
            var toolsTabAnchor=document.getElementById('toolsCategoryAnchor');
            var toolsListDiv=document.getElementById('toolsDashlets');
            var webTab=document.getElementById('webCategory');
            var webTabAnchor=document.getElementById('webCategoryAnchor');
            var webListDiv=document.getElementById('webDashlets');
            switch(category){
                case'module':
                    moduleTab.className='active';
                    moduleTabAnchor.className='current';
                    moduleListDiv.style.display='';
                    chartTab.className='';
                    chartTabAnchor.className='';
                    chartListDiv.style.display='none';
                    toolsTab.className='';
                    toolsTabAnchor.className='';
                    toolsListDiv.style.display='none';
                    webTab.className='';
                    webTabAnchor.className='';
                    webListDiv.style.display='none';
                    break;
                case'chart':
                    moduleTab.className='';
                    moduleTabAnchor.className='';
                    moduleListDiv.style.display='none';
                    chartTab.className='active';
                    chartTabAnchor.className='current';
                    chartListDiv.style.display='';
                    toolsTab.className='';
                    toolsTabAnchor.className='';
                    toolsListDiv.style.display='none';
                    webTab.className='';
                    webTabAnchor.className='';
                    webListDiv.style.display='none';
                    break;
                case'tools':
                    moduleTab.className='';
                    moduleTabAnchor.className='';
                    moduleListDiv.style.display='none';
                    chartTab.className='';
                    chartTabAnchor.className='';
                    chartListDiv.style.display='none';
                    toolsTab.className='active';
                    toolsTabAnchor.className='current';
                    toolsListDiv.style.display='';
                    webTab.className='';
                    webTabAnchor.className='';
                    webListDiv.style.display='none';
                    break;
                case'web':
                    moduleTab.className='';
                    moduleTabAnchor.className='';
                    moduleListDiv.style.display='none';
                    chartTab.className='';
                    chartTabAnchor.className='';
                    chartListDiv.style.display='none';
                    toolsTab.className='';
                    toolsTabAnchor.className='';
                    toolsListDiv.style.display='none';
                    webTab.className='active';
                    webTabAnchor.className='current';
                    webListDiv.style.display='';
                    break;
                default:
                    break;
            }
            document.getElementById('search_category').value=category;
        },
        searchDashlets:function(searchStr,searchCategory){
            var moduleTab=document.getElementById('moduleCategory');
            var moduleTabAnchor=document.getElementById('moduleCategoryAnchor');
            var moduleListDiv=document.getElementById('moduleDashlets');
            var chartTab=document.getElementById('chartCategory');
            var chartTabAnchor=document.getElementById('chartCategoryAnchor');
            var chartListDiv=document.getElementById('chartDashlets');
            var toolsTab=document.getElementById('toolsCategory');
            var toolsTabAnchor=document.getElementById('toolsCategoryAnchor');
            var toolsListDiv=document.getElementById('toolsDashlets');
            if(moduleTab!=null&&chartTab!=null&&toolsTab!=null){
                moduleListDiv.style.display='none';
                chartListDiv.style.display='none';
                toolsListDiv.style.display='none';
            }
            else{
                chartListDiv.style.display='none';
            }
            var searchResultsDiv=document.getElementById('searchResults');
            searchResultsDiv.style.display='';
            var success=function(data){
                eval(data.responseText);
                searchResultsDiv.innerHTML=response['html'];
            }
            var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=true&module='+module+'&action=DynamicAction&DynamicAction=searchDashlets&search='+searchStr+'&category='+searchCategory,{
                success:success,
                failure:success
            });
            return false;
        },
        collapseList:function(chartList){
            document.getElementById(chartList+'List').style.display='none';
            document.getElementById(chartList+'ExpCol').innerHTML='<a href="#" onClick="javascript:SUGAR.mySugar.expandList(\''+chartList+'\');"><img border="0" src="'+SUGAR.themes.image_server+'index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=advanced_search.gif" align="absmiddle" />';
        },
        expandList:function(chartList){
            document.getElementById(chartList+'List').style.display='';
            document.getElementById(chartList+'ExpCol').innerHTML='<a href="#" onClick="javascript:SUGAR.mySugar.collapseList(\''+chartList+'\');"><img border="0" src="'+SUGAR.themes.image_server+'index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=basic_search.gif" align="absmiddle" />';
        },
        collapseReportList:function(reportChartList){
            document.getElementById(reportChartList+'ReportsChartDashletsList').style.display='none';
            document.getElementById(reportChartList+'ExpCol').innerHTML='<a href="#" onClick="javascript:SUGAR.mySugar.expandReportList(\''+reportChartList+'\');"><img border="0" src="'+SUGAR.themes.image_server+'index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=ProjectPlus.gif" align="absmiddle" />';
        },
        expandReportList:function(reportChartList){
            document.getElementById(reportChartList+'ReportsChartDashletsList').style.display='';
            document.getElementById(reportChartList+'ExpCol').innerHTML='<a href="#" onClick="javascript:SUGAR.mySugar.collapseReportList(\''+reportChartList+'\');"><img border="0" src="'+SUGAR.themes.image_server+'index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=ProjectMinus.gif" align="absmiddle" />';
        },
        clearSearch:function(){
            document.getElementById('search_string').value='';
            var moduleTab=document.getElementById('moduleCategory');
            var moduleTabAnchor=document.getElementById('moduleCategoryAnchor');
            var moduleListDiv=document.getElementById('moduleDashlets');
            document.getElementById('searchResults').innerHTML='';
            if(moduleTab!=null){
                SUGAR.mySugar.toggleDashletCategories('module');
            }
            else{
                document.getElementById('searchResults').style.display='none';
                document.getElementById('chartDashlets').style.display='';
            }
        },
        doneAddDashlets:function(){
            SUGAR.mySugar.dashletsDialog.hide();
            return false;
        },
        renderDashletsDialog:function(){

            SUGAR.mySugar.dashletsDialog=new YAHOO.widget.Dialog("dashletsDialog",{
                width:"480px",
                height:"520px",
                fixedcenter:true,
                draggable:false,
                visible:false,
                modal:true,
                close:false
            });
            var listeners=new YAHOO.util.KeyListener(document,{
                keys:27
            },{
                fn:function(){
                    SUGAR.mySugar.closeDashletsDialog();
                }
            });
            SUGAR.mySugar.dashletsDialog.cfg.queueProperty("keylisteners",listeners);
            document.getElementById('dashletsDialog').style.display='';
            SUGAR.mySugar.dashletsDialog.render();
            document.getElementById('dashletsDialog_c').style.display='none';
        },
        
        //Added by Reena 18-1-2012
        togglePages:function(activePage){           
            var pageId='pageNum_'+activePage;
            activeDashboardPage=activePage;
            activeTab=activePage;
            Set_Cookie(cookiePageIndex,activePage,3000,false,false,false);
            for(var i=0;i<num_pages;i++){
                var pageDivId='pageNum_'+i+'_div';
                var pageDivElem=document.getElementById(pageDivId);
                pageDivElem.style.display='none';
            }
            for(var i=0;i<num_pages;i++){
                var tabId='pageNum_'+i;
                var anchorId='pageNum_'+i+'_anchor';
                var pageDivId='pageNum_'+i+'_div';
                var tabElem=document.getElementById(tabId);
                var anchorElem=document.getElementById(anchorId);
                var pageDivElem=document.getElementById(pageDivId);
                if(tabId==pageId){
                    if(!SUGAR.mySugar.pageIsLoaded(pageDivId))
                        SUGAR.mySugar.retrievePage(i);
                    tabElem.className='active';
                    anchorElem.className='current';
                    pageDivElem.style.display='inline';
                }
                else{
                    tabElem.className='';
                    anchorElem.className='';
                }
            }
        },
        deletePage:function(){
            var pageNum=activeTab;
            var tabListElem=document.getElementById('tabList');
            var removeResult='';
            if(confirm(SUGAR.language.get('app_strings','LBL_DELETE_PAGE_CONFIRM')))
                window.location="index.php?module="+module+"&action=DynamicAction&DynamicAction=deletePage&pageNumToDelete="+pageNum;
        },
        renamePage:function(pageNum){
            SUGAR.mySugar.toggleSpansForRename(pageNum);
            document.getElementById('pageNum_'+pageNum+'_name_input').focus();
        },
        rename_Page:function(){            
            var str = $('.active').attr('id');
            var Num = str.split("_");     
            var pageNum = Num[1];            
            SUGAR.mySugar.toggleSpansForRename(pageNum);
            document.getElementById('pageNum_'+pageNum+'_name_input').focus();           
        },
        toggleSpansForRename:function(pageNum){            
            var tabInputSpan=document.getElementById('pageNum_'+pageNum+'_input_span');            
            var tabLinkSpan=document.getElementById('pageNum_'+pageNum+'_link_span');
            if(tabLinkSpan.style.display=='none'){
                tabLinkSpan.style.display='inline';
                tabInputSpan.style.display='none';
            }
            else{
                tabLinkSpan.style.display='none';
                tabInputSpan.style.display='inline';
            }
        },
        savePageTitle:function(pageNum,newTitleValue)
        {
            var currentTitleValue=document.getElementById('pageNum_'+pageNum+'_name_hidden_input').value;
            if(newTitleValue==''){
                newTitleValue=currentTitleValue;
                alert(SUGAR.language.get('app_strings','ERR_BLANK_PAGE_NAME'));
            }
            else if(newTitleValue!=currentTitleValue)
            {
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_SAVING_PAGE_TITLE'));
                url='index.php?DynamicAction=savePageTitle&action=DynamicAction&module='+module+'&to_pdf=1&newPageTitle='+JSON.stringify(newTitleValue)+'&pageId='+pageNum;
                var setPageTitle=function(data)

                {
                    var pageTextSpan=document.getElementById('pageNum_'+pageNum+'_title_text');
                    pageTextSpan.innerHTML=data.responseText;
                    document.getElementById('pageNum_'+pageNum+'_name_input').value=data.responseText;
                    document.getElementById('pageNum_'+pageNum+'_name_hidden_input').value=data.responseText;
                    loadedPages.splice(pageNum,1);
                    ajaxStatus.hideStatus();
                }
                var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                    success:setPageTitle,
                    failure:setPageTitle
                },null);
            }
            var pageTextSpan=document.getElementById('pageNum_'+pageNum+'_title_text');
            pageTextSpan.innerHTML=newTitleValue;
            SUGAR.mySugar.toggleSpansForRename(pageNum);
        },
        pageIsLoaded:function(pageDivId){
            for(var count=0;count<loadedPages.length;count++)

            {
                    if(loadedPages[count]==pageDivId)
                        return true;
                }
            return false;
        },
        retrievePage:function(pageNum){
            document.getElementById('loading_c').style.display='';
            SUGAR.mySugar.loading.show();
            var pageCount=num_pages;
            var addPageElem=document.getElementById('add_page');
            var tabListElem=document.getElementById('tabList');
            url='index.php?action=DynamicAction&DynamicAction=retrievePage&module='+module+'&to_pdf=1&pageId='+pageNum;
            var populatePage=function(data){
                eval(data.responseText);
                var htmlRepsonse=response['html'];
                eval(response['script']);
                var pageDivElem=document.getElementById('pageNum_'+pageNum+'_div');
                pageDivElem.innerHTML=htmlRepsonse;
                loadedPages[loadedPages.length]='pageNum_'+pageNum+'_div';
                var counter=SUGAR.mySugar.homepage_dd.length;
                if(YAHOO.util.DDM.mode==1){
                    for(i in scriptResponse['newDashletsToReg']){
                        SUGAR.mySugar.homepage_dd[counter]=new ygDDList('dashlet_'+scriptResponse['newDashletsToReg'][i]);
                        SUGAR.mySugar.homepage_dd[counter].setHandleElId('dashlet_header_'+scriptResponse['newDashletsToReg'][i]);
                        SUGAR.mySugar.homepage_dd[counter].onMouseDown=SUGAR.mySugar.onDrag;
                        SUGAR.mySugar.homepage_dd[counter].afterEndDrag=SUGAR.mySugar.onDrop;
                        counter++;
                    }
                }
                for(chart in scriptResponse['chartsArray']){
                    SUGAR.mySugar.addToChartsArray(scriptResponse['chartsArray'][chart]['id'],scriptResponse['chartsArray'][chart]['xmlFile'],scriptResponse['chartsArray'][chart]['width'],scriptResponse['chartsArray'][chart]['height'],scriptResponse['chartsArray'][chart]['styleSheet'],scriptResponse['chartsArray'][chart]['colorScheme'],scriptResponse['chartsArray'][chart]['langFile']);
                }
                if(YAHOO.util.DDM.mode==1){
                    for(var wp=0;wp<scriptResponse['numCols'];wp++){
                        SUGAR.mySugar.homepage_dd[counter++]=new ygDDListBoundary('page_'+pageNum+'_hidden'+wp);
                    }
                }
                ajaxStatus.hideStatus();
                if(scriptResponse['trackerScript']){
                    SUGAR.util.evalScript(scriptResponse['trackerScript']);
                    if(typeof(trackerGridArray)!='undefined'&&trackerGridArray.length>0){
                        for(x in trackerGridArray){
                            if(typeof(trackerGridArray[x])!='function'){
                                trackerDashlet=new TrackerDashlet();
                                trackerDashlet.init(trackerGridArray[x]);
                            }
                        }
                    }
                }
                if(scriptResponse['dashletScript']){
                    SUGAR.util.evalScript(scriptResponse['dashletScript']);
                }
                if(scriptResponse['toggleHeaderToolsetScript']){
                    SUGAR.util.evalScript(scriptResponse['toggleHeaderToolsetScript']);
                }
                if(scriptResponse['dashletCtrl']){
                    SUGAR.util.evalScript(scriptResponse['dashletCtrl']);
                }
                SUGAR.mySugar.loadSugarCharts();
                //Dhaval - To evalute Chart Loading JS
                SUGAR.util.evalScript(htmlRepsonse);
                //End - Dhaval
                SUGAR.mySugar.loading.hide();
                document.getElementById('loading_c').style.display='none';
                
            }
            var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                success:populatePage,
                failure:populatePage
            },null);
        },
        showAddPageDialog:function(){  
            
            if(document.getElementById('addPageDialog_c')==null){
                setTimeout(SUGAR.mySugar.showAddPageDialog,100);
                return false;
            }
            document.getElementById('addPageDialog_c').style.display='';
            SUGAR.mySugar.addPageDialog.show();
            SUGAR.mySugar.addPageDialog.configFixedCenter(null,false);
        },
        addTab:function(newPageName,numCols){
            
            var pageCount=num_pages;
            var tabListElem=document.getElementById('tabList');
            var addPageElem=document.getElementById('add_page');
            var dashletCtrlsElem=document.getElementById('dashletCtrls');
            var contentElem=document.getElementById('content');
            var tabListContainerElem=document.getElementById('tabListContainer');
            var contentElemWidth=contentElem.offsetWidth-3;
            var addPageElemWidth=addPageElem.offsetWidth+2;
            var dashletCtrlsElemWidth=dashletCtrlsElem.offsetWidth;
            var tabListElemWidth=tabListElem.offsetWidth;
            var maxWidth=contentElemWidth-(dashletCtrlsElemWidth+addPageElemWidth+2);
            url='index.php?DynamicAction=addPage&action=DynamicAction&module='+module+'&to_pdf=1&numCols='+numCols+'&pageName='+JSON.stringify(newPageName);
            var addBlankPage=function(data){
                var pageContainerDivElem=document.getElementById('pageContainer');
                var newPageId='pageNum_'+pageCount+'_div';
                var newPageDivElem=document.createElement('div');
                newPageDivElem.id=newPageId;
                newPageDivElem.innerHTML=data.responseText;
                newPageDivElem.style.display='none';
                pageContainerDivElem.insertBefore(newPageDivElem,document.getElementById('addPageDialog_c'));
                loadedPages[num_pages]=newPageDivElem.id;
                ajaxStatus.hideStatus();
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_NEW_PAGE_FEEDBACK'));
                window.setTimeout('ajaxStatus.hideStatus()',7500);
                SUGAR.mySugar.togglePages(pageCount);
            }
            ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_CREATING_NEW_PAGE'));
            var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                success:addBlankPage,
                failure:addBlankPage
            },null);
            var new_tab=document.createElement("li");
            new_tab.id='pageNum_'+num_pages;
            var new_anchor=document.createElement("a");
            new_anchor.id='pageNum_'+num_pages+'_anchor';
            new_anchor.className='active';
            new_anchor.href="javascript:SUGAR.mySugar.togglePages('"+num_pages+"');"
            newPageName=newPageName.replace(/\\'/g,"'");
            new_anchor.appendChild(SUGAR.mySugar.insertInputSpanElement(num_pages,newPageName));
            new_anchor.appendChild(SUGAR.mySugar.insertTabNameDisplay(num_pages,newPageName));
            var new_delete_img=document.createElement("img");
            new_delete_img.id='pageNum_'+num_pages+'_delete_page_img';
            new_delete_img.className='deletePageImg';
            new_delete_img.style.display='none';
            new_delete_img.onclick=function(){
                return SUGAR.mySugar.deletePage();
            };

            new_delete_img.src='index.php?entryPoint=getImage&imageName=info-del.png';
            new_delete_img.border=0;
            new_delete_img.align='absmiddle';
            new_anchor.appendChild(new_delete_img);
            new_tab.appendChild(new_anchor);
            tabListElem.appendChild(new_tab);
            if(tabListElemWidth+new_tab.offsetWidth>maxWidth){
                tabListContainerElem.style.width=maxWidth+"px";
                tabListElem.style.width=tabListElemWidth+new_tab.offsetWidth+"px";
                tabListContainerElem.setAttribute("className","active yui-module yui-scroll");
                tabListContainerElem.setAttribute("class","active yui-module yui-scroll");
            }
            num_pages=num_pages+1;
        },
        insertInputSpanElement:function(page_num,pageName){
            var inputSpanElement=document.createElement("span");
            inputSpanElement.id='pageNum_'+page_num+'_input_span';
            inputSpanElement.style.display='none';
            var subInputSpanElement1=document.createElement("input");
            subInputSpanElement1.id='pageNum_'+page_num+'_name_hidden_input';
            subInputSpanElement1.type='hidden';
            subInputSpanElement1.value=pageName;
            inputSpanElement.appendChild(subInputSpanElement1);
            var subInputSpanElement2=document.createElement("input");
            subInputSpanElement2.id='pageNum_'+page_num+'_name_input';
            subInputSpanElement2.type='text';
            subInputSpanElement2.size='10';
            subInputSpanElement2.value=pageName;
            subInputSpanElement2.onblur=function(){
                return SUGAR.mySugar.savePageTitle(page_num,this.value);
            }
            inputSpanElement.appendChild(subInputSpanElement2);
            return inputSpanElement;
        },
        insertTabNameDisplay:function(page_num,pageName){           
            var spanElement=document.createElement("span");
            spanElement.id='pageNum_'+page_num+'_link_span';
            var subSpanElement=document.createElement("span");
            subSpanElement.id='pageNum_'+page_num+'_title_text';
            subSpanElement.ondblclick=function(){
                return SUGAR.mySugar.renamePage(page_num);
            }
            var textNode=document.createTextNode(pageName);
            subSpanElement.appendChild(textNode);
            spanElement.appendChild(subSpanElement);
            return spanElement;
        },
        showChangeLayoutDialog:function(tabNum){
            document.getElementById('changeLayoutDialog_c').style.display='';
            SUGAR.mySugar.changeLayoutDialog.show();
            SUGAR.mySugar.changeLayoutDialog.configFixedCenter(null,false);
        },
        renderLoadingDialog:function(){
            SUGAR.mySugar.loading=new YAHOO.widget.Panel("loading",{
                width:"240px",
                fixedcenter:true,
                close:false,
                draggable:false,
                constraintoviewport:false,
                modal:true,
                visible:false,
                effect:[{
                    effect:YAHOO.widget.ContainerEffect.SLIDE,
                    duration:0.5
                },{
                    effect:YAHOO.widget.ContainerEffect.FADE,
                    duration:.5
                }]
            });
            //SUGAR.mySugar.loading.setBody('<div id="loadingPage" align="center" style="vertical-align:middle;"><img src="'+SUGAR.themes.image_server+'index.php?entryPoint=getImage&themeName='+SUGAR.themes.theme_name+'&imageName=img_loading.gif" align="absmiddle" /> <b>'+SUGAR.language.get('app_strings','LBL_LOADING_PAGE')+'</b></div>');
            SUGAR.mySugar.loading.setBody('<div id="loadingPage" align="center" style="vertical-align:middle;"><img src="themes/Sugar5/images/img_loading.gif" align="absmiddle" /> <b>'+SUGAR.language.get('app_strings','LBL_LOADING_PAGE')+'</b></div>');
            SUGAR.mySugar.loading.render(document.body);
            document.getElementById('loading_c').style.display='none';
        },
        renderAddPageDialog:function(){
            var handleSuccess=function(o){
                var response=o.responseText;
                eval(o.responseText);
                var pageName=result['pageName'];
                var numCols=result['numCols'];
                SUGAR.mySugar.addTab(pageName,numCols);
                if(!SUGAR.isIE){
                    setTimeout("document.getElementById('addPageDialog_c').style.display = 'none';",2000);
                }
                SUGAR.mySugar.addPageDialog.hide();
            };
    
            var handleFailure=function(o){
                if(!SUGAR.isIE){
                    setTimeout("document.getElementById('addPageDialog_c').style.display = 'none';",2000);
                }
                SUGAR.mySugar.addPageDialog.hide();
            };
    
            var handleSubmit=function(){
                this.submit();
            };
    
            var handleCancel=function(){
                SUGAR.mySugar.addPageDialog.hide();
            };
    
            SUGAR.mySugar.addPageDialog=new YAHOO.widget.Dialog("addPageDialog",{
                width:"300px",
                fixedcenter:true,
                visible:false,
                draggable:false,
                effect:[{
                    effect:YAHOO.widget.ContainerEffect.SLIDE,
                    duration:0.5
                },{
                    effect:YAHOO.widget.ContainerEffect.FADE,
                    duration:0.5
                }],
                buttons:[{
                    text:SUGAR.language.get('app_strings','LBL_SUBMIT_BUTTON_LABEL'),
                    handler:handleSubmit,
                    isDefault:true
                },{
                    text:SUGAR.language.get('app_strings','LBL_CANCEL_BUTTON_LABEL'),
                    handler:handleCancel
                }],
                modal:true
            });
            SUGAR.mySugar.addPageDialog.callback={
                success:handleSuccess,
                failure:handleFailure
            };
    
            SUGAR.mySugar.addPageDialog.validate=function(){
                var postData=this.getData();
                if(postData.pageName==""){
                    alert(SUGAR.language.get('app_strings','ERR_BLANK_PAGE_NAME'));
                    return false;
                }
                return true;
            }
            document.getElementById('addPageDialog').style.display='';
            SUGAR.mySugar.addPageDialog.render();
            document.getElementById('addPageDialog_c').style.display='none';
        } ,
        changePageLayout:function(numCols){
            SUGAR.mySugar.changeLayout(numCols);
            if(!SUGAR.isIE){
                setTimeout("document.getElementById('changeLayoutDialog_c').style.display = 'none';",2000);
            }
            SUGAR.mySugar.changeLayoutDialog.hide();
        } ,
        renderChangeLayoutDialog:function(){
            SUGAR.mySugar.changeLayoutDialog=new YAHOO.widget.Dialog("changeLayoutDialog",{
                width:"300px",
                fixedcenter:true,
                visible:false,
                draggable:false,
                effect:[{
                    effect:YAHOO.widget.ContainerEffect.SLIDE,
                    duration:0.5
                },{
                    effect:YAHOO.widget.ContainerEffect.FADE,
                    duration:0.5
                }],
                modal:true
            });
            document.getElementById('changeLayoutDialog').style.display='';
            SUGAR.mySugar.changeLayoutDialog.render();
            document.getElementById('changeLayoutDialog_c').style.display='none';
        } ,
        attachToggleToolsetEvent:function(dashletId){
            var header=document.getElementById("dashlet_header_"+dashletId);
            if(YAHOO.env.ua.ie){
                YAHOO.util.Event.on(header,'mouseenter',function(){
                    document.getElementById("dashlet_header_"+dashletId).className="hd selected";
                });
                YAHOO.util.Event.on(header,'mouseleave',function(){
                    document.getElementById("dashlet_header_"+dashletId).className="hd";
                });
            }else{
                YAHOO.util.Event.on(header,'mouseover',function(){
                    document.getElementById("dashlet_header_"+dashletId).className="hd selected";
                });
                YAHOO.util.Event.on(header,'mouseout',function(){
                    document.getElementById("dashlet_header_"+dashletId).className="hd";
                });
            }
        }
    };

}();
}