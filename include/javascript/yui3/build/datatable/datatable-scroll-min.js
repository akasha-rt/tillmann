/*
 Copyright (c) 2010, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.com/yui/license.html
 version: 3.3.0
 build: 3167
 */
YUI.add("datatable-scroll",function(B){var J=B.Node,I=B.Lang,L=B.UA,E=B.ClassNameManager.getClassName,K="datatable",A=E(K,"hd"),D=E(K,"bd"),H=E(K,"scrollable"),G='<div class="'+A+'"></div>',C='<div class="'+D+'"></div>',F="<table></table>";function M(){M.superclass.constructor.apply(this,arguments);}B.mix(M,{NS:"scroll",NAME:"dataTableScroll",ATTRS:{width:{value:undefined,writeOnce:"initOnly"},height:{value:undefined,writeOnce:"initOnly"},_scroll:{valueFn:function(){var N=this.get("width"),O=this.get("height");if(N&&O){return"xy";}else{if(N){return"x";}else{if(O){return"y";}else{return null;}}}}},COLOR_COLUMNFILLER:{value:"#f2f2f2",validator:I.isString,setter:function(N){if(this._headerContainerNode){this._headerContainerNode.setStyle("backgroundColor",N);}}}}});B.extend(M,B.Plugin.Base,{_parentTableNode:null,_parentTheadNode:null,_parentTbodyNode:null,_parentMsgNode:null,_parentContainer:null,_bodyContainerNode:null,_headerContainerNode:null,initializer:function(N){var O=this.get("host");this._parentContainer=O.get("contentBox");this._parentContainer.addClass(H);this._setUpNodes();},_setUpNodes:function(){this.afterHostMethod("_addTableNode",this._setUpParentTableNode);this.afterHostMethod("_addTheadNode",this._setUpParentTheadNode);this.afterHostMethod("_addTbodyNode",this._setUpParentTbodyNode);this.afterHostMethod("_addMessageNode",this._setUpParentMessageNode);this.afterHostMethod("renderUI",this.renderUI);this.afterHostMethod("syncUI",this.syncUI);if(this.get("_scroll")!=="x"){this.afterHostMethod("_attachTheadThNode",this._attachTheadThNode);this.afterHostMethod("_attachTbodyTdNode",this._attachTbodyTdNode);}},_setUpParentTableNode:function(){this._parentTableNode=this.get("host")._tableNode;},_setUpParentTheadNode:function(){this._parentTheadNode=this.get("host")._theadNode;},_setUpParentTbodyNode:function(){this._parentTbodyNode=this.get("host")._tbodyNode;},_setUpParentMessageNode:function(){this._parentMsgNode=this.get("host")._msgNode;},renderUI:function(){this._createBodyContainer();this._createHeaderContainer();this._setContentBoxDimensions();},syncUI:function(){this._removeCaptionNode();this._syncWidths();this._syncScroll();},_removeCaptionNode:function(){this.get("host")._captionNode.remove();},_syncWidths:function(){var O=J.all("#"+this._parentContainer.get("id")+" .yui3-datatable-hd table thead th"),P=J.one("#"+this._parentContainer.get("id")+" .yui3-datatable-bd table .yui3-datatable-data").get("firstChild").get("children"),Q,T,V,S,U,R,N=L.ie;for(Q=0,T=O.size();Q<T;Q++){U=O.item(Q).get("firstChild");R=P.item(Q).get("firstChild");if(!N){V=U.get("clientWidth");S=P.item(Q).get("clientWidth");}else{V=U.get("offsetWidth");S=P.item(Q).get("offsetWidth");}if(V>S){R.setStyle("width",(V-20+"px"));}else{if(S>V){U.setStyle("width",(S-20+"px"));R.setStyle("width",(S-20+"px"));}}}},_attachTheadThNode:function(O){var N=O.column.get("width")||"auto";if(N!=="auto"){O.th.get("firstChild").setStyles({width:N,overflow:"hidden"});}return O;},_attachTbodyTdNode:function(O){var N=O.column.get("width")||"auto";if(N!=="auto"){O.td.get("firstChild").setStyles({width:N,overflow:"hidden"});}return O;},_createBodyContainer:function(){var O=J.create(C),N=B.bind("_onScroll",this);this._bodyContainerNode=O;this._setStylesForTbody();O.appendChild(this._parentTableNode);this._parentContainer.appendChild(O);O.on("scroll",N);},_createHeaderContainer:function(){var O=J.create(G),N=J.create(F);this._headerContainerNode=O;this._setStylesForThead();N.appendChild(this._parentTheadNode);O.appendChild(N);this._parentContainer.prepend(O);},_setStylesForTbody:function(){var O=this.get("_scroll"),N=this.get("width")||"",Q=this.get("height")||"",P=this._bodyContainerNode,R={width:"",height:Q};if(O==="x"){R.overflowY="hidden";R.width=N;}else{if(O==="y"){R.overflowX="hidden";}else{if(O==="xy"){R.width=N;}else{R.overflowX="hidden";R.overflowY="hidden";R.width=N;}}}P.setStyles(R);return P;},_setStylesForThead:function(){var N=this.get("width")||"",O=this._headerContainerNode;O.setStyles({"width":N,"overflow":"hidden"});},_setContentBoxDimensions:function(){if(this.get("_scroll")==="y"||(!this.get("width"))){this._parentContainer.setStyle("width","auto");}},_onScroll:function(){this._headerContainerNode.set("scrollLeft",this._bodyContainerNode.get("scrollLeft"));},_syncScroll:function(){this._syncScrollX();this._syncScrollY();this._syncScrollOverhang();if(L.opera){this._headerContainerNode.set("scrollLeft",this._bodyContainerNode.get("scrollLeft"));if(!this.get("width")){document.body.style+="";}}},_syncScrollY:function(){var N=this._parentTbodyNode,P=this._bodyContainerNode,O;if(!this.get("width")){O=(P.get("scrollHeight")>P.get("clientHeight"))?(N.get("parentNode").get("clientWidth")+19)+"px":(N.get("parentNode").get("clientWidth")+2)+"px";this._parentContainer.setStyle("width",O);}},_syncScrollX:function(){var N=this._parentTbodyNode,P=this._bodyContainerNode,O;this._headerContainerNode.set("scrollLeft",this._bodyContainerNode.get("scrollLeft"));if(!this.get("height")&&(L.ie)){O=(P.get("scrollWidth")>P.get("offsetWidth"))?(N.get("parentNode").get("offsetHeight")+18)+"px":N.get("parentNode").get("offsetHeight")+"px";P.setStyle("height",O);}if(N.get("rows").length===0){this._parentMsgNode.get("parentNode").setStyle("width",this._parentTheadNode.get("parentNode").get("offsetWidth")+"px");}else{this._parentMsgNode.get("parentNode").setStyle("width","");}},_syncScrollOverhang:function(){var N=this._bodyContainerNode,O=1;if((N.get("scrollHeight")>N.get("clientHeight"))||(N.get("scrollWidth")>N.get("clientWidth"))){O=18;}this._setOverhangValue(O);if(L.ie!==0&&this.get("_scroll")==="y"&&this._bodyContainerNode.get("scrollHeight")>this._bodyContainerNode.get("offsetHeight")){this._headerContainerNode.setStyle("width",this._parentContainer.get("width"));}},_setOverhangValue:function(O){var Q=this.get("host"),S=Q.get("columnset").get("definitions"),N=S.length,R=O+"px solid "+this.get("COLOR_COLUMNFILLER"),P=J.all("#"+this._parentContainer.get("id")+" ."+A+" table thead th");P.item(N-1).setStyle("borderRight",R);}});B.namespace("Plugin").DataTableScroll=M;},"3.3.0",{requires:["datatable-base","plugin","stylesheet"]});
