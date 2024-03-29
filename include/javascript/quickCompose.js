(function () {
    var JSON = YAHOO.lang.JSON;
    SUGAR.quickCompose = {};
    SUGAR.quickCompose = function () {
        return{parentPanel: null, dceMenuPanel: null, options: null, loadingMessgPanl: null, frameLoaded: false, resourcesLoaded: false, tinyLoaded: false, initComposePackage: function (c)
            {
                SUGAR.email2.addressBook.initFixForDatatableSort();
                SUGAR.quickCompose.resourcesLoaded = true;
                var callback = {success: function (o)
                    {
                        var responseData = JSON.parse(o.responseText);
                        var scriptTag = document.createElement('script');
                        scriptTag.id = 'quickComposeScript';
                        scriptTag.setAttribute('type', 'text/javascript');
                        if (YAHOO.env.ua.ie > 0)
                            scriptTag.text = responseData.jsData;
                        else
                            scriptTag.appendChild(document.createTextNode(responseData.jsData));
                        document.getElementsByTagName("head")[0].appendChild(scriptTag);
                        var divTag = document.createElement("div");
                        divTag.innerHTML = responseData.divData;
                        divTag.id = 'quickCompose';
                        YAHOO.util.Dom.insertBefore(divTag, 'footer');
                        SUGAR.quickCompose.frameLoaded = true;
                        SUGAR.quickCompose.initUI(c.data);
                    }}
                if (!SUGAR.quickCompose.frameLoaded)
                    YAHOO.util.Connect.asyncRequest('GET', 'index.php?entryPoint=GenerateQuickComposeFrame', callback, null);
                else
                    SUGAR.quickCompose.initUI(c.data);
            }, initUI: function (options)
            {
                var hei = (85 * document.documentElement.clientHeight) / 100;
                var wid = (85 * document.documentElement.clientWidth) / 100;
                var SQ = SUGAR.quickCompose;
                this.options = options;
                loadingMessgPanl.hide();
                var dce_mode = (typeof this.dceMenuPanel != 'undefined' && this.dceMenuPanel != null) ? true : false;
                if (SQ.parentPanel != null)
                {
                    tinyMCE.execCommand('mceRemoveControl', false, SUGAR.email2.tinyInstances.currentHtmleditor);
                    SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor] = null;
                    SUGAR.email2.tinyInstances.currentHtmleditor = "";
                    SQ.parentPanel.destroy();
                    SQ.parentPanel = null;
                }
                var theme = SUGAR.themes.theme_name;
                var idx = 0;
                if (!SE.composeLayout.composeTemplate)
                    SE.composeLayout.composeTemplate = new YAHOO.SUGAR.Template(SE.templates['compose']);
                var panel_modal = dce_mode ? false : true, panel_width = wid + 'px', panel_constrain = dce_mode ? false : true, panel_height = dce_mode ? 'auto' : hei + 'px', panel_shadow = dce_mode ? false : true, panel_draggable = dce_mode ? false : true, panel_resize = dce_mode ? false : true, panel_close = dce_mode ? false : true;
                SQ.parentPanel = new YAHOO.widget.Panel("container1", {modal: panel_modal, visible: true, constraintoviewport: panel_constrain, width: panel_width, height: panel_height, shadow: panel_shadow, draggable: panel_draggable, resize: panel_resize, close: panel_close});
                if (!dce_mode) {
                    SQ.parentPanel.setHeader(SUGAR.language.get('app_strings', 'LBL_EMAIL_QUICK_COMPOSE'));
                }
                SQ.parentPanel.setBody("<div class='email'><div id='htmleditordiv" + idx + "'></div></div>");
                var composePanel = SE.composeLayout.getQuickComposeLayout(SQ.parentPanel, this.options);
                if (!dce_mode) {
                    var resize = new YAHOO.util.Resize('container1', {handles: ['br'], autoRatio: false, minWidth: 400, minHeight: 350, status: false});
                    resize.on('resize', function (args) {
                        var panelHeight = args.height;
                        this.cfg.setProperty("height", panelHeight + "px");
                        var layout = SE.composeLayout[SE.composeLayout.currentInstanceId];
                        layout.set("height", panelHeight - 50);
                        layout.resize(true);
                        SE.composeLayout.resizeEditor(SE.composeLayout.currentInstanceId);
                    }, SQ.parentPanel, true);
                } else {
                    SUGAR.util.doWhen("typeof SE.composeLayout[SE.composeLayout.currentInstanceId] != 'undefined'", function () {
                        var panelHeight = 400;
                        SQ.parentPanel.cfg.setProperty("height", panelHeight + "px");
                        var layout = SE.composeLayout[SE.composeLayout.currentInstanceId];
                        layout.set("height", panelHeight);
                        layout.resize(true);
                        SE.composeLayout.resizeEditor(SE.composeLayout.currentInstanceId);
                    });
                }
                YAHOO.util.Dom.setStyle("container1", "z-index", 1);
                if (!SQ.tinyLoaded)
                {
                    tinymce.dom.Event.domLoaded = true;
                    tinyMCE.init({convert_urls: false, theme_advanced_toolbar_align: tinyConfig.theme_advanced_toolbar_align, width: tinyConfig.width, theme: tinyConfig.theme, theme_advanced_toolbar_location: tinyConfig.theme_advanced_toolbar_location, theme_advanced_buttons1: tinyConfig.theme_advanced_buttons1, theme_advanced_buttons2: tinyConfig.theme_advanced_buttons2, theme_advanced_buttons3: tinyConfig.theme_advanced_buttons3, plugins: tinyConfig.plugins, elements: tinyConfig.elements, language: tinyConfig.language, extended_valid_elements: tinyConfig.extended_valid_elements, mode: tinyConfig.mode, strict_loading_mode: true});
                    SQ.tinyLoaded = true;
                }
                SQ.parentPanel.show();
                SUGAR.email2.composeLayout.forceCloseCompose = function (o) {
                    SUGAR.quickCompose.parentPanel.hide();
                }
                if (!dce_mode) {
                    SQ.parentPanel.center();
                }
            }, init: function (o) {
                if (typeof o.menu_id != 'undefined') {
                    this.dceMenuPanel = o.menu_id;
                } else {
                    this.dceMenuPanel = null;
                }
                loadingMessgPanl = new YAHOO.widget.SimpleDialog('loading', {width: '200px', close: true, modal: true, visible: true, fixedcenter: true, constraintoviewport: true, draggable: false});
                loadingMessgPanl.setHeader(SUGAR.language.get('app_strings', 'LBL_EMAIL_PERFORMING_TASK'));
                loadingMessgPanl.setBody(SUGAR.language.get('app_strings', 'LBL_EMAIL_ONE_MOMENT'));
                loadingMessgPanl.render(document.body);
                loadingMessgPanl.show();
                if (!SUGAR.quickCompose.resourcesLoaded)
                    this.loadResources(o);
                else
                    this.initUI(o);
            }, loadResources: function (o)
            {
                window.skipTinyMCEInitPhase = true;
                var require = ["layout", "element", "tabview", "menu", "cookie", "tinymce", "sugarwidgets", "sugarquickcompose", "sugarquickcomposecss"];
                var loader = new YAHOO.util.YUILoader({require: require, loadOptional: true, skin: {base: 'blank', defaultSkin: ''}, data: o, onSuccess: this.initComposePackage, allowRollup: true, base: "include/javascript/yui/build/"});
                loader.addModule({name: "tinymce", type: "js", varName: "TinyMCE", fullpath: "include/javascript/tiny_mce/tiny_mce.js"});
                loader.addModule({name: "sugarwidgets", type: "js", fullpath: "include/javascript/sugarwidgets/SugarYUIWidgets.js", varName: "YAHOO.SUGAR", requires: ["datatable", "dragdrop", "treeview", "tabview"]});
                loader.addModule({name: "sugarquickcompose", type: "js", varName: "SUGAR.email2.complexLayout", requires: ["layout", "sugarwidgets", "tinymce"], fullpath: "cache/include/javascript/sugar_grp_quickcomp.js"});
                loader.addModule({name: "sugarquickcomposecss", type: "css", fullpath: "modules/Emails/EmailUI.css"});
                loader.insert();
            }};
    }();
})();
