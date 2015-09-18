Ext.onReady(function() {
    Ext.QuickTips.init();
    Ext.form.Field.prototype.msgTarget = 'side';

    /////////////////////////////////
    ///// PANEL SEARCH USERS
    /////////////////////////////////

    //Variables
    var pageSize = parseInt(CONFIG.pageSize);

    //Components
    var searchUsersText = new Ext.form.TextField({
        width: 280,
        allowBlank: true,
        listeners:{
            specialkey:function(f,o){
                if(o.getKey()==13){
                    storeGridSearch.load({ params: {sKeyword: searchUsersText.getValue()} });
                }
            }
        }
    });

    var compSearchUsers = new Ext.form.CompositeField({
        fieldLabel: 'Keyword',
        labelStyle: 'width:100px; padding: 3px 3px 3px 15px;',
        items: [
            searchUsersText,
            {
                xtype: 'button',
                iconCls: 'button_menu_ext ss_sprite ss_magnifier',
                text: _('ID_SEARCH'),
                width : 40,
                handler: function(){
                    storeGridSearch.load({ params: {sKeyword: searchUsersText.getValue()} });
                }
            }
        ]
    });

    var panelSearch = new Ext.Panel({
        region: 'north',
        height: 65,
        margins: '0 0 0 0',
        frame: true,
        labelAlign: 'left',
        align: 'center',
        labelStyle: 'font-weight:bold; padding: 3px 3px 3px 15px;',
        title: "<div><div style=\"float: left;\">" + _('ID_SEARCH_FOR_USER') + "</div><div id=\"divBack\" style=\"float: right;\"></div><div style=\"clear: both; height: 0; line-height:0; font-size: 0;\"></div></div>",
        items: [
            new Ext.FormPanel({
                labelWidth : 120,
                labelStyle: 'padding: 3px 3px 3px 15px;',
                autoScroll: false,
                monitorValid : true,
                bodyStyle: "border: 0px;",
                items:[
                    compSearchUsers
                ]
            })
        ],

        listeners: {
            afterrender: function (panel)
            {
                var btn = new Ext.Button({
                    text: _("ID_BACK"),
                    iconCls: "button_menu_ext ss_sprite ss_arrow_left",
                    renderTo: "divBack",

                    handler: function ()
                    {
                        redirectPage("authSources_List");
                    }
                });
            }
        }
    });




    /////////////////////////////////
    ///// GRID SEARCH USERS
    /////////////////////////////////


    var storeGridSearch = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            method: 'POST',
            url: 'ldapAdvancedProxy.php',
            timeout: 240000
        }),
        autoDestroy: true,
        remoteSort: false,
        totalProperty: "resultTotal",
        root: "resultRoot",
        fields: [
            'sUsername',
            'sFullname',
            'sFirstname',
            'sLastname',
            'sEmail',
            'sCategory',
            'sDN',
            'sManagerDN',
            'STATUS',
            'IMPORT'
        ],
        listeners: {
            beforeload: function (store, opt)
            {
                this.baseParams = {
                    functionAccion: "searchUsers",
                    sUID: Fields.AUTH_SOURCE_UID,
                    sKeyword: searchUsersText.getValue(),
                    pageSize: pageSize
                };
            },
            load: function (store, record, opt)
            {
                Ext.getCmp('BUTTON_IMPORT').disable();
            }
        }
    });
    storeGridSearch.setDefaultSort('sUsername', 'asc');

    var tbarSearch = [
        {
            id: 'BUTTON_IMPORT',
            text: _('ID_IMPORT'),
            iconCls: 'button_menu_ext ss_sprite ss_group_go ',
            disabled: true,
            handler: function () {
                rowSelected = gridSearch.getSelectionModel().getSelected();
                var auxUsersSelect = gridSearch.getSelectionModel().selections.items;
                var countSelect = auxUsersSelect.length;

                if (countSelect != 0) {
                    var con = 0;
                    var usersSelect = new Array();
                    var numberCases = '';
                    while (con < countSelect) {
                        if (auxUsersSelect[con].data.IMPORT == 1) {
                            var newArray = {
                                sUsername   : auxUsersSelect[con].data.sUsername,
                                sFullname   : auxUsersSelect[con].data.sFullname,
                                sFirstname  : auxUsersSelect[con].data.sFirstname,
                                sLastname   : auxUsersSelect[con].data.sLastname,
                                sEmail      : auxUsersSelect[con].data.sEmail,
                                sCategory   : auxUsersSelect[con].data.sCategory,
                                sDN         : auxUsersSelect[con].data.sDN,
                                sManagerDN  : auxUsersSelect[con].data.sManagerDN
                            };
                            usersSelect.push(newArray);
                        }
                        con++;
                    }

                    var countImport = usersSelect.length;
                    if (countImport != 0) {
                        Ext.MessageBox.confirm('Confirm', 'Are you sure you want to import the selected users?', function (val) {
                            if (val == 'yes') {
                                Ext.MessageBox.show({
                                    msg: _('ID_IMPORTING_USERS'),
                                    progressText: _('ID_SAVING'),
                                    width:300,
                                    wait:true,
                                    waitConfig: {interval:200},
                                    animEl: 'mb7'
                                });

                                Ext.Ajax.request({
                                    params: {
                                        'UsersImport': Ext.encode(usersSelect),
                                        'functionAccion': 'importUsers',
                                        'AUTH_SOURCE_UID': Fields.AUTH_SOURCE_UID
                                    },
                                    url : 'ldapAdvancedProxy.php',
                                    success: function (returnData) {
                                        var resp = Ext.decode(returnData.responseText);
                                        Ext.MessageBox.hide();
                                        if (resp.success) {
                                            Ext.MessageBox.show({
                                                title: _('ID_IMPORT_USERS'),
                                                msg: _('ID_IMPORTED_SUCCESSFULLY'),
                                                buttons: Ext.MessageBox.OK,
                                                animEl: 'mb9',
                                                icon: Ext.MessageBox.INFO
                                            });
                                            redirectPage('../users/users_List');
                                        }
                                    },
                                    failure: function () {
                                        Ext.MessageBox.alert('ERROR', _('ID_ERROR_IN_SERVER'));
                                    }
                                });
                            }
                        });
                    } else {
                        PMExt.notify('WARNING', _('ID_YOU_DO_NOT_SELECT_ANY_USER_TO_IMPORT'));
                    }
                } else {
                    PMExt.notify('WARNING', _('ID_YOU_DO_NOT_SELECT_ANY_USER_TO_IMPORT'));
                }
            }
        }
    ];

    //var pageSize = parseInt(CONFIG.pageSize);
    //
    //var storePageSize = new Ext.data.SimpleStore({
    //    autoLoad: true,
    //    fields: ['size'],
    //    data:[['20'],['30'],['40'],['50'],['100']]
    //});
    //
    //var comboPageSize = new Ext.form.ComboBox({
    //    typeAhead     : false,
    //    mode          : 'local',
    //    triggerAction : 'all',
    //    store: storePageSize,
    //    valueField: 'size',
    //    displayField: 'size',
    //    width: 50,
    //    editable: false,
    //    listeners:{
    //    select: function(c,d,i){
    //        pagingSearchList.pageSize = parseInt(d.data['size']);
    //        pagingSearchList.moveFirst();
    //    }
    //    }
    //});
    //
    //comboPageSize.setValue(pageSize);
    //
    //var pagingSearchList = new Ext.PagingToolbar({
    //    pageSize : 1000,
    //    store : storeGridSearch,
    //    displayInfo : true,
    //    autoHeight : true,
    //    displayMsg : 'Ldap Users' + ' {0} - {1} ' + 'of' + ' {2}',
    //    emptyMsg : ' There are no LDAP Users '//,
    //    //items: [
    //      //comboPageSize
    //    //]
    //});

    var pagingSearchList = new Ext.PagingToolbar({
        pageSize: pageSize,
        store: storeGridSearch,
        displayInfo: true,
        displayMsg: _('ID_LDAP_USERS') + " {0} - {1} " + "of" + " {2}",
        emptyMsg: _('ID_THERE_ARE_NO_LDAP_USERS')
    });

    var selectModelList = new Ext.grid.CheckboxSelectionModel({
        listeners: {
            selectionchange: function() {
                if (selectModelList.getCount() > 0) {
                    Ext.getCmp('BUTTON_IMPORT').enable();
                } else {
                    Ext.getCmp('BUTTON_IMPORT').disable();
                }
            }
        }
    });

    var gridSearch = new Ext.grid.GridPanel({
        store : storeGridSearch,
        tbar : tbarSearch,
        bbar : pagingSearchList,
        region: 'center',
        margins: '0 0 0 0',
        loadMask : true,
        sm: selectModelList,

        cm: new Ext.grid.ColumnModel({
            defaults: {
              sortable: true
            },
            columns: [
                selectModelList,
                {header: _('ID_USER_ID'),  width: 15, dataIndex: 'sUsername', sortable: true},
                {header: _('ID_FIRST_NAME'),  width: 15, dataIndex: 'sFirstname', sortable: true},
                {header: _('ID_LAST_NAME'),  width: 15, dataIndex: 'sLastname', sortable: true},
                {header: _('ID_EMAIL'),  width: 15, dataIndex: 'sEmail', sortable: true},
                {header: _('ID_DISTINGUISHED_NAME'), width: 35, dataIndex: 'sDN'},
                {dataIndex: "STATUS", header: _("ID_STATUS"), width: 10, css: "background: #D4D4D4; font-weight: bold;", align: "center", renderer: renderStatus}
            ]
        }),
        border: false,
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

        viewConfig: {
            forceFit:true,
            emptyText: '<div align="center"><b> ' + _('ID_THERE_ARE_NO_LDAP_USERS') + ' </b></div>'
        }
    });

    new Ext.Viewport({
        layout:'border',
        border: false,
        items: [panelSearch, gridSearch]
    });
});

