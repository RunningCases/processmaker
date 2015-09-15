Ext.onReady(function() {
    //Head - Add style
    var nhead = document.getElementsByTagName("head")[0];
    var nstyle = document.createElement("style");
    var strCss = "\
    .ext-mb-ok {\
        background: transparent url(/images/dialog-ok-apply.png) no-repeat top left;\
    }";

    nstyle.setAttribute("type", "text/css");

    nhead.appendChild(nstyle);

    if (nstyle.styleSheet) {
        //IE
        nstyle.styleSheet.cssText = strCss;
    } else {
        //Others browsers
        nstyle.appendChild(document.createTextNode(strCss));
    }

    //Init
    Ext.QuickTips.init();
    Ext.form.Field.prototype.msgTarget = 'side';

    var ldapGridProxy = new Ext.data.HttpProxy({
        method: 'POST',
        api: {
            read    : 'ldapAdvancedProxy.php?functionAccion=ldapGrid&tipo=read',
            create  : 'ldapAdvancedProxy.php?functionAccion=ldapGrid&tipo=create',
            save    : 'ldapAdvancedProxy.php?functionAccion=ldapGrid&tipo=save',
            destroy : 'ldapAdvancedProxy.php?functionAccion=ldapGrid&tipo=destroy',
            update  : 'ldapAdvancedProxy.php?functionAccion=ldapGrid&tipo=update'
        }
    });

    var ldapGridReader = new Ext.data.JsonReader({
        totalProperty: 'total',
        successProperty: 'success',
        messageProperty: 'message',
        idProperty: 'ID',
        root: 'data',
        fields: [
            {name: 'ID'},
            {name: 'ATTRIBUTE_LDAP'},
            {name: 'ATTRIBUTE_USER'}
        ]
    });

    var ldapGridWriter = new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        listful: true
    });

    var ldapGridStore = new Ext.data.Store({
        proxy:  ldapGridProxy,
        reader: ldapGridReader,
        writer: ldapGridWriter,
        autoSave: true,
        listeners:{
            load: function() {
                //
            }
        }
    });



    Ext.data.DataProxy.addListener('beforewrite', function(proxy, action) {
        /*
        if(action != 'create')
        {
             Ext.MessageBox.show({
                    msg: 'Guardando su información, espere un momento por favor',
                    progressText: 'Saving...',
                    width:300,
                    wait:true,
                    waitConfig: {interval:200},
                    animEl: 'mb7'
             });
        }
        */
    });

    Ext.data.DataProxy.addListener('write', function(proxy, action, result, res, rs) {
        //
    });

    Ext.data.DataProxy.addListener('exception', function(proxy, type, action, options, res) {
        /*
        Ext.MessageBox.show({
            title: 'Error de almacenamiento',
            msg: 'Error al almacenar datos',
            buttons: Ext.MessageBox.OK,
            animEl: 'mb9',
            icon: Ext.MessageBox.ERROR
        });
        */
    });

    var ldapGridFieldLdap = new Ext.form.TextField({
        name: 'DELETE1',
        id: 'DELETE1',
        autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
    });

    var values = Fields.AUTH_SOURCE_ATTRIBUTE_IDS;
    values = values.trim();
    var allValues = new Array();
    var comboValues = new Array();
    allValues = values.split('|');
    for (var i = 0; i < allValues.length; i++) {
        if (allValues[i] != '') {
            comboValues.push([allValues[i],allValues[i]]);
        }
    }

    var ldapGridFieldUser = new Ext.form.ComboBox({
        valueField: 'ID',
        displayField: 'VALUE',
        value: '0',

        typeAhead: true,
        forceSelection: true,
        triggerAction: 'all',
        name: 'DELETE2',
        id: 'DELETE2',
        editable: true,
        width: 130,
        store: comboValues
    });

    var ldapGridCol = [
        {
            id: 'ID',
            dataIndex: 'ID',
            sortable: true,
            hidden: true,
            hideable:false
        },{
            id: 'ATTRIBUTE_LDAP',
            header: _("ID_LDAP_FIELD"),
            dataIndex: 'ATTRIBUTE_LDAP',
            width: 10,
            sortable: true,
            editor: ldapGridFieldLdap
        },
        {
            id: 'ATTRIBUTE_USER',
            header: _("ID_USER_FIELD"),
            dataIndex: 'ATTRIBUTE_USER',
            width: 10,
            sortable: true,
            editor: ldapGridFieldUser
        }
    ];

    var ldapGridEditor = new Ext.ux.grid.RowEditor({
        saveText: _('ID_SAVE'),
        cancelText: _('ID_CANCEL'),
        listeners: {
          canceledit: function(grid,obj){
            //
          },
          afteredit: function(grid,obj,record){
            //
          }
        }
    });

    var ldapGrid = new Ext.grid.GridPanel({
        store: ldapGridStore,
        loadMask : true,
        plugins: [ldapGridEditor],
        frame: true,
        height: 365,
        columns : ldapGridCol,
        autoShow: true,
        autoFill:true,
        nocache: true,
        autoWidth: true,
        stripeRows: true,
        stateful: true,
        animCollapse: true,
        enableColumnResize: true,
        enableHdMenu: true,
        columnLines: true,

        tbar: [{
            text: _('ID_ADD'),
            iconCls: ' x-btn-text button_menu_ext ss_sprite ss_add',
            handler: onAdd
        }, '-', {
            text: _('ID_REMOVE'),
            iconCls: ' x-btn-text button_menu_ext ss_sprite ss_delete',
            handler: onDelete
        }],
        viewConfig: {
            forceFit: true
        }
    });

    function onAdd(btn, ev) {
        var row = new ldapGrid.store.recordType({
            ID: 'NUEVO',
            ATTRIBUTE_LDAP: '',
            ATTRIBUTE_USER: ''
        });

        var length = ldapGrid.getStore().data.length;
        ldapGridEditor.stopEditing();
        ldapGridStore.insert(length, row);
        ldapGrid.getView().refresh();
        ldapGrid.getSelectionModel().selectRow(length);
        ldapGridEditor.startEditing(length);
    }

    function onDelete() {
        var rec = ldapGrid.getSelectionModel().getSelected();
        if (!rec) {
            return false;
        }
        ldapGrid.store.remove(rec);
    }

    ///////////////////////////////////////////////////////////////////////////////////////

    var pnlAttribute = new Ext.Panel({
        height: 425,
        bodyStyle: "border-top: 0px; padding: 10px;",

        title: "<div id=\"containerChkAttribute\" style=\"height: 20px;\"></div>",
        items: [ldapGrid],

        listeners: {
            afterrender: function (panel)
            {
                var chk = new Ext.form.Checkbox({
                    id: "AUTH_SOURCE_SHOWGRID-checkbox",
                    name: "AUTH_SOURCE_SHOWGRID-checkbox",
                    boxLabel: _("ID_MATCH_ATTRIBUTES_TO_SYNC"),
                    renderTo: "containerChkAttribute",

                    listeners: {
                        check: function (chk, checked)
                        {
                            ldapGrid.setVisible(checked);
                        }
                    }
                });
            }
        }
    });

    ///////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////

    var ldapFormSubmit = function ()
    {
        var itemsLdapGrid = ldapGrid.store.data.items;
        var arrayDetail = [];

        for (var i = 0; i <= itemsLdapGrid.length - 1; i++) {
            var newItem = {
                attributeLdap: itemsLdapGrid[i].data.ATTRIBUTE_LDAP,
                attributeUser: itemsLdapGrid[i].data.ATTRIBUTE_USER
            };

            arrayDetail[i] = newItem;
        }

        Ext.get("LDAP_TYPE").dom.value = ldapFormType.getValue();
        Ext.get("AUTH_SOURCE_AUTO_REGISTER").dom.value = ldapFormAutoRegister.getValue();
        Ext.get("AUTH_SOURCE_ENABLED_TLS").dom.value = ldapFormTls.getValue();
        Ext.get("AUTH_ANONYMOUS").dom.value = ldapFormAnonymous.getValue();
        Ext.get("AUTH_SOURCE_GRID_TEXT").dom.value = Ext.util.JSON.encode(arrayDetail);

        ldapForm.getForm().submit({
            method: "POST",
            waitTitle: _('ID_CONNECTING'),
            waitMsg: _("ID_SAVING"),
            success: function (form, action)
            {
                redirectPage("../authSources/authSources_List?" + randomNum(1, 9999999));
            },
            failure: function (form, action)
            {
                //
            }
        });
    };

    var ldapForm = new Ext.FormPanel({
        url : 'ldapAdvancedProxy.php?functionAccion=ldapSave',
        frame : true,
        title : _("ID_AUTHENTICATION_SOURCE_INFORMATION"),
        border : false,
        autoScroll: true,
        monitorValid : true,

        items:[
            {
                layout:'column',
                autoScroll:true,

                bodyStyle: "border: 0px;",

                items:[{
                    columnWidth: 0.5,
                    bodyStyle: "border: 0px;",
                    items: [pnlData]
                },{
                    columnWidth: 0.5,
                    bodyStyle: "border: 0px; padding-left: 10px;",
                    items: [pnlAttribute]
                }]
            },
            {
                layout: "column",
                autoScroll: true,

                bodyStyle: "margin-top: 0.5em; border: 0px;",

                items: [
                    {
                        columnWidth: 1,
                        bodyStyle: "border: 0px;",
                        html: _("ID_MINIMUM_DATA_REQUIRED_TO_RUN_THE")
                    }
                ]
            }
        ],
        buttons: [
            {
                text: _("ID_SAVE"),
                formBind: true,
                handler: function ()
                {
                    if (typeof(Fields.AUTH_SOURCE_UID) != "undefined" && typeof(Fields.AUTH_SOURCE_BASE_DN) != "undefined" && ldapFormBaseDN.getValue() != Fields.AUTH_SOURCE_BASE_DN) {
                        Ext.Ajax.request({
                            url: "ldapAdvancedProxy.php",
                            method: "POST",
                            params: {
                                functionAccion: "ldapVerifyIfExistsRecordsInDb",
                                authenticationSourceUid: Fields.AUTH_SOURCE_UID
                            },

                            success: function (response, opts)
                            {
                                var dataResponse = Ext.util.JSON.decode(response.responseText);

                                if (dataResponse.status) {
                                    if (dataResponse.status == "OK" && dataResponse.existsRecords + "" == "1") {
                                        Ext.MessageBox.confirm(
                                            _("ID_CONFIRM"),
                                            "System has detected that there are synchronized elements with the \"Authentication Source \" you are editing, if you change the \"Base DN\" those synchronized elements could have problems. Are you sure you want to change the \"Base DN\"?",
                                            function (btn)
                                            {
                                                if (btn == "yes") {
                                                    ldapFormSubmit();
                                                }
                                            }
                                        );
                                    } else {
                                        ldapFormSubmit();
                                    }
                                } else {
                                    ldapFormSubmit();
                                }
                            },
                            failure: function (response, opts)
                            {
                                //
                            }
                        });
                    } else {
                        ldapFormSubmit();
                    }
                }
            },
            {
                text: _('ID_TEST_CONNECTION'),
                formBind: true,
                handler: function ()
                {
                    var loadMaskAux = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_TESTING_CONNECTION')});
                    loadMaskAux.show();

                    Ext.Ajax.request({
                        url: "ldapAdvancedProxy.php",
                        method: "POST",
                        params: {
                            functionAccion: "ldapTestConnection",
                            AUTH_SOURCE_SERVER_NAME: Ext.getCmp("AUTH_SOURCE_SERVER_NAME").getValue(),
                            AUTH_SOURCE_PORT:        Ext.getCmp("AUTH_SOURCE_PORT").getValue(),
                            AUTH_SOURCE_ENABLED_TLS: Ext.getCmp("AUTH_SOURCE_ENABLED_TLS").getValue(),
                            AUTH_ANONYMOUS:          Ext.getCmp("AUTH_ANONYMOUS").getValue(),
                            AUTH_SOURCE_SEARCH_USER: Ext.getCmp("AUTH_SOURCE_SEARCH_USER").getValue(),
                            AUTH_SOURCE_PASSWORD:    Ext.getCmp("AUTH_SOURCE_PASSWORD").getValue(),
                            AUTH_SOURCE_VERSION:     3
                        },

                        success: function (response, opts)
                        {
                            var dataResponse = Ext.util.JSON.decode(response.responseText);

                            if (dataResponse.status) {
                                Ext.MessageBox.show({
                                    title: _('ID_TEST_CONNECTION'),
                                    msg: (dataResponse.status == "OK")? _('ID_SUCCESSFULLY_CONNECTED') : dataResponse.message,

                                    icon: (dataResponse.status == "OK")? "ext-mb-ok" : Ext.MessageBox.ERROR,
                                    buttons: {ok: _("ID_ACCEPT")}
                                });
                            }

                            loadMaskAux.hide();
                        },
                        failure: function (response, opts)
                        {
                            loadMaskAux.hide();
                        }
                    });
                }
            },
            {
                text: _("ID_CANCEL"),
                handler: function ()
                {
                    redirectPage("../authSources/authSources_List?" + randomNum(1, 9999999));
                }
            }
        ]
    });

    var gridAttribute = '';
    if (typeof(Fields.AUTH_SOURCE_UID) == 'undefined' || Fields.AUTH_SOURCE_UID == '') {
        ldapFormProvider.setValue(Fields.AUTH_SOURCE_PROVIDER);
        ldapFormAttrinuteIds.setValue(Fields.AUTH_SOURCE_ATTRIBUTE_IDS);
        gridAttribute = '';
    } else {
        ldapFormId.setValue(Fields.AUTH_SOURCE_UID);
        ldapFormName.setValue(Fields.AUTH_SOURCE_NAME);
        ldapFormProvider.setValue(Fields.AUTH_SOURCE_PROVIDER);
        ldapFormType.setValue(Fields.LDAP_TYPE);
        ldapFormAutoRegister.setValue(Fields.AUTH_SOURCE_AUTO_REGISTER);
        ldapFormServerName.setValue(Fields.AUTH_SOURCE_SERVER_NAME);

        ldapFormPort.setValue(Fields.AUTH_SOURCE_PORT);
        ldapFormTls.setValue(Fields.AUTH_SOURCE_ENABLED_TLS);
        ldapFormBaseDN.setValue(Fields.AUTH_SOURCE_BASE_DN);
        ldapFormAnonymous.setValue(Fields.AUTH_ANONYMOUS);
        ldapFormSearchUser.setValue(Fields.AUTH_SOURCE_SEARCH_USER);
        ldapFormPassword.setValue(Fields.AUTH_SOURCE_PASSWORD);
        ldapFormIdentifier.setValue(Fields.AUTH_SOURCE_IDENTIFIER_FOR_USER);
        ldapFormUsersFilter.setValue(Fields.AUTH_SOURCE_USERS_FILTER);
        ldapFormRetiredEmployees.setValue(Fields.AUTH_SOURCE_RETIRED_OU);

        if (typeof(Fields.AUTH_SOURCE_GRID_ATTRIBUTE) != 'undefined') {
            gridAttribute = Ext.util.JSON.encode(Fields.AUTH_SOURCE_GRID_ATTRIBUTE);
        }
    }

    ldapGridStore.load({
       params:{'data': gridAttribute}
    });

    var arrayObject = [];
    arrayObject["ldapFormSearchUser"] = ldapFormSearchUser;
    arrayObject["ldapFormPassword"] = ldapFormPassword;

    ldapFormAnonymousOnChange(ldapFormAnonymous, arrayObject);

    new Ext.Viewport({
        layout:'fit',
        border: false,
        items: [ldapForm]
    });

    ldapFormProvider.setValue(Fields.AUTH_SOURCE_PROVIDER);
    ldapFormAttrinuteIds.setValue(Fields.AUTH_SOURCE_ATTRIBUTE_IDS);

    Ext.getCmp("AUTH_SOURCE_SHOWGRID-checkbox").setValue(typeof(Fields.AUTH_SOURCE_GRID_ATTRIBUTE) != "undefined");
    ldapGrid.setVisible(typeof(Fields.AUTH_SOURCE_GRID_ATTRIBUTE) != "undefined");
});

