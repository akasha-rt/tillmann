/*
 Copyright (c) 2010, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.com/yui/license.html
 version: 3.3.0
 build: 3167
 */
YUI.add('datatable-scroll',function(Y){var YNode=Y.Node,YLang=Y.Lang,YUA=Y.UA,YgetClassName=Y.ClassNameManager.getClassName,DATATABLE="datatable",CLASS_HEADER=YgetClassName(DATATABLE,"hd"),CLASS_BODY=YgetClassName(DATATABLE,"bd"),CLASS_SCROLLABLE=YgetClassName(DATATABLE,"scrollable"),CONTAINER_HEADER='<div class="'+CLASS_HEADER+'"></div>',CONTAINER_BODY='<div class="'+CLASS_BODY+'"></div>',TEMPLATE_TABLE='<table></table>';function DataTableScroll(){DataTableScroll.superclass.constructor.apply(this,arguments);}
Y.mix(DataTableScroll,{NS:"scroll",NAME:"dataTableScroll",ATTRS:{width:{value:undefined,writeOnce:"initOnly"},height:{value:undefined,writeOnce:"initOnly"},_scroll:{valueFn:function(){var w=this.get('width'),h=this.get('height');if(w&&h){return'xy';}
else if(w){return'x';}
else if(h){return'y';}
else{return null;}}},COLOR_COLUMNFILLER:{value:'#f2f2f2',validator:YLang.isString,setter:function(param){if(this._headerContainerNode){this._headerContainerNode.setStyle('backgroundColor',param);}}}}});Y.extend(DataTableScroll,Y.Plugin.Base,{_parentTableNode:null,_parentTheadNode:null,_parentTbodyNode:null,_parentMsgNode:null,_parentContainer:null,_bodyContainerNode:null,_headerContainerNode:null,initializer:function(config){var dt=this.get("host");this._parentContainer=dt.get('contentBox');this._parentContainer.addClass(CLASS_SCROLLABLE);this._setUpNodes();},_setUpNodes:function(){this.afterHostMethod("_addTableNode",this._setUpParentTableNode);this.afterHostMethod("_addTheadNode",this._setUpParentTheadNode);this.afterHostMethod("_addTbodyNode",this._setUpParentTbodyNode);this.afterHostMethod("_addMessageNode",this._setUpParentMessageNode);this.afterHostMethod("renderUI",this.renderUI);this.afterHostMethod("syncUI",this.syncUI);if(this.get('_scroll')!=='x'){this.afterHostMethod('_attachTheadThNode',this._attachTheadThNode);this.afterHostMethod('_attachTbodyTdNode',this._attachTbodyTdNode);}},_setUpParentTableNode:function(){this._parentTableNode=this.get('host')._tableNode;},_setUpParentTheadNode:function(){this._parentTheadNode=this.get('host')._theadNode;},_setUpParentTbodyNode:function(){this._parentTbodyNode=this.get('host')._tbodyNode;},_setUpParentMessageNode:function(){this._parentMsgNode=this.get('host')._msgNode;},renderUI:function(){this._createBodyContainer();this._createHeaderContainer();this._setContentBoxDimensions();},syncUI:function(){this._removeCaptionNode();this._syncWidths();this._syncScroll();},_removeCaptionNode:function(){this.get('host')._captionNode.remove();},_syncWidths:function(){var th=YNode.all('#'+this._parentContainer.get('id')+' .yui3-datatable-hd table thead th'),td=YNode.one('#'+this._parentContainer.get('id')+' .yui3-datatable-bd table .yui3-datatable-data').get('firstChild').get('children'),i,len,thWidth,tdWidth,thLiner,tdLiner,ie=YUA.ie;for(i=0,len=th.size();i<len;i++){thLiner=th.item(i).get('firstChild');tdLiner=td.item(i).get('firstChild');if(!ie){thWidth=thLiner.get('clientWidth');tdWidth=td.item(i).get('clientWidth');}
else{thWidth=thLiner.get('offsetWidth');tdWidth=td.item(i).get('offsetWidth');}
if(thWidth>tdWidth){tdLiner.setStyle('width',(thWidth-20+'px'));}
else if(tdWidth>thWidth){thLiner.setStyle('width',(tdWidth-20+'px'));tdLiner.setStyle('width',(tdWidth-20+'px'));}}},_attachTheadThNode:function(o){var w=o.column.get('width')||'auto';if(w!=='auto'){o.th.get('firstChild').setStyles({width:w,overflow:'hidden'});}
return o;},_attachTbodyTdNode:function(o){var w=o.column.get('width')||'auto';if(w!=='auto'){o.td.get('firstChild').setStyles({width:w,overflow:'hidden'});}
return o;},_createBodyContainer:function(){var bd=YNode.create(CONTAINER_BODY),onScrollFn=Y.bind("_onScroll",this);this._bodyContainerNode=bd;this._setStylesForTbody();bd.appendChild(this._parentTableNode);this._parentContainer.appendChild(bd);bd.on('scroll',onScrollFn);},_createHeaderContainer:function(){var hd=YNode.create(CONTAINER_HEADER),tbl=YNode.create(TEMPLATE_TABLE);this._headerContainerNode=hd;this._setStylesForThead();tbl.appendChild(this._parentTheadNode);hd.appendChild(tbl);this._parentContainer.prepend(hd);},_setStylesForTbody:function(){var dir=this.get('_scroll'),w=this.get('width')||"",h=this.get('height')||"",el=this._bodyContainerNode,styles={width:"",height:h};if(dir==='x'){styles.overflowY='hidden';styles.width=w;}
else if(dir==='y'){styles.overflowX='hidden';}
else if(dir==='xy'){styles.width=w;}
else{styles.overflowX='hidden';styles.overflowY='hidden';styles.width=w;}
el.setStyles(styles);return el;},_setStylesForThead:function(){var w=this.get('width')||"",el=this._headerContainerNode;el.setStyles({'width':w,'overflow':'hidden'});},_setContentBoxDimensions:function(){if(this.get('_scroll')==='y'||(!this.get('width'))){this._parentContainer.setStyle('width','auto');}},_onScroll:function(){this._headerContainerNode.set('scrollLeft',this._bodyContainerNode.get('scrollLeft'));},_syncScroll:function(){this._syncScrollX();this._syncScrollY();this._syncScrollOverhang();if(YUA.opera){this._headerContainerNode.set('scrollLeft',this._bodyContainerNode.get('scrollLeft'));if(!this.get("width")){document.body.style+='';}}},_syncScrollY:function(){var tBody=this._parentTbodyNode,tBodyContainer=this._bodyContainerNode,w;if(!this.get("width")){w=(tBodyContainer.get('scrollHeight')>tBodyContainer.get('clientHeight'))?(tBody.get('parentNode').get('clientWidth')+19)+"px":(tBody.get('parentNode').get('clientWidth')+2)+"px";this._parentContainer.setStyle('width',w);}},_syncScrollX:function(){var tBody=this._parentTbodyNode,tBodyContainer=this._bodyContainerNode,w;this._headerContainerNode.set('scrollLeft',this._bodyContainerNode.get('scrollLeft'));if(!this.get('height')&&(YUA.ie)){w=(tBodyContainer.get('scrollWidth')>tBodyContainer.get('offsetWidth'))?(tBody.get('parentNode').get('offsetHeight')+18)+"px":tBody.get('parentNode').get('offsetHeight')+"px";tBodyContainer.setStyle('height',w);}
if(tBody.get('rows').length===0){this._parentMsgNode.get('parentNode').setStyle('width',this._parentTheadNode.get('parentNode').get('offsetWidth')+'px');}
else{this._parentMsgNode.get('parentNode').setStyle('width',"");}},_syncScrollOverhang:function(){var tBodyContainer=this._bodyContainerNode,padding=1;if((tBodyContainer.get('scrollHeight')>tBodyContainer.get('clientHeight'))||(tBodyContainer.get('scrollWidth')>tBodyContainer.get('clientWidth'))){padding=18;}
this._setOverhangValue(padding);if(YUA.ie!==0&&this.get('_scroll')==='y'&&this._bodyContainerNode.get('scrollHeight')>this._bodyContainerNode.get('offsetHeight'))
{this._headerContainerNode.setStyle('width',this._parentContainer.get('width'));}},_setOverhangValue:function(borderWidth){var host=this.get('host'),cols=host.get('columnset').get('definitions'),len=cols.length,value=borderWidth+"px solid "+this.get("COLOR_COLUMNFILLER"),children=YNode.all('#'+this._parentContainer.get('id')+' .'+CLASS_HEADER+' table thead th');children.item(len-1).setStyle('borderRight',value);}});Y.namespace("Plugin").DataTableScroll=DataTableScroll;},'3.3.0',{requires:['datatable-base','plugin','stylesheet']});
