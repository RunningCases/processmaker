new Ext.KeyMap(document, [
    {
        key: Ext.EventObject.F5,
        fn: function(k, e) {
            if (!e.ctrlKey) {
                if (Ext.isIE) {
                    e.browserEvent.keyCode = 8;
                }
                e.stopEvent();
                document.location = document.location;
            } else {
                Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
            }
        }
    },
    {
        key: Ext.EventObject.DELETE,
        fn: function(k, e) {
            iGrid = Ext.getCmp('ownerInfoGrid');
            rowSelected = iGrid.getSelectionModel().getSelected();
            if (rowSelected) {
                deleteDashboard();
            }
        }
    },
    {
        key: Ext.EventObject.F2,
        fn: function(k, e) {
            iGrid = Ext.getCmp('ownerInfoGrid');
            rowSelected = iGrid.getSelectionModel().getSelected();
            if (rowSelected){
                editDashboard();
            }
        }
    }
]);

var viewport;
var dashboardFields;
var frmDashboard;
var addTabButton;
var tabPanel;
var dashboardIndicatorFields;
var store;

var indexTab = 0;
var comboPageSize = 10;
var resultTpl;
var storeIndicatorType;
var storeGraphic;
var storeFrequency;
var storeProject;
var storeGroup;
var storeUsers;
var dataUserGroup;
var flag = true;
var myMask;
var dataIndicator = '';
var tabActivate = [];

Ext.onReady( function() {

    myMask = new Ext.LoadMask(Ext.getBody(), {msg:_('ID_LOADING')});


    Ext.QuickTips.init();

    resultTpl = new Ext.XTemplate(
        '<tpl for="."><div class="x-combo-list-item" style="white-space:normal !important;word-wrap: break-word;">',
        '<span> {APP_PRO_TITLE}</span>',
        '</div></tpl>'
    );

    //FieldSets
    dashboardFields = new Ext.form.FieldSet({
        title       : _('ID_GENERATE_INFO'),
        items       : [
            {
                id          : 'DAS_TITLE',
                fieldLabel  : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_DASHBOARD_TITLE')) +'"> * </span>' + _('ID_DASHBOARD_TITLE'),
                xtype       : 'textfield',
                anchor      : '85%',
                maxLength   : 250,
                maskRe      : /^([a-zA-Z0-9_'\s]+)$/i,
                regex       : /^([a-zA-Z0-9_'\s]+)$/i,
                regexText   : _('ID_INVALID_VALUE', _('ID_DASHBOARD_TITLE')),
                allowBlank  : false
            },
            {
                xtype           : 'textarea',
                id              : 'DAS_DESCRIPTION',
                fieldLabel      : _('ID_DESCRIPTION'),
                anchor          : '85%',
                maskRe          : /([a-zA-Z0-9_'\s]+)$/,
                height          : 50
            }
        ]
    });

    //grid owner
    deleteButton = new Ext.Action({
        text    : _('ID_DELETE'),
        iconCls : 'button_menu_ext ss_sprite  ss_delete',
        handler : deleteOwner,
        disabled: true
    });

    actionButtons = [deleteButton , '->'];


    var owner = Ext.data.Record.create ([
        {
            name : 'DAS_UID',
            type: 'string'
        },
        {
            name : "OWNER_UID",
            type: 'string'
        },
        {
            name : "OWNER_LABEL",
            type: 'string'
        },
        {
            name : 'OWNER_TYPE',
            type: 'string'
        }
    ]);

    store = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'dashboard/'+ DAS_UID +'/owners'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            root: 'owner',
            totalProperty: 'totalCount',
            fields : owner
        }),
        sortInfo: {
            field: 'OWNER_TYPE',
            direction: 'ASC'
        },
        autoLoad: true
    });

    storeGroup = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'group'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields : [
                {name : 'grp_uid'},
                {name : "grp_title"},
                {name : "grp_status"},
                {name : "grp_users"},
                {name : 'grp_tasks'}
            ]
        }),
        sortInfo: {
            field: 'grp_title',
            direction: 'ASC'
        }
    });
    storeGroup.load();

    storeUsers = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'users'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields : [
                {name : 'usr_uid'},
                {name : "usr_firstname"},
                {name : "usr_lastname"},
                {name : 'usr_status'}
            ]
        }),
        sortInfo: {
            field: 'usr_lastname',
            direction: 'ASC'
        }
    });
    storeUsers.load();

    bbarpaging = new Ext.PagingToolbar({
        pageSize: 10,
        store: store,
        displayInfo: true,
        //displayMsg: _('ID_GRID_PAGE_DISPLAYING_0WNER_MESSAGE') + '&nbsp; &nbsp; ',
        displayMsg : '',
        //emptyMsg: _('ID_GRID_PAGE_NO_OWNER_MESSAGE')
        emptyMsg: ''
    });

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: false
        },
        columns: [
            {   id:'DAS_UID',               dataIndex: 'DAS_UID', hidden:true, hideable:false},
            {   header: _("ID_OWNER"),      dataIndex: "OWNER_LABEL", width: 150, hidden: false, align: "left"},
            {   header: _("ID_OWNER_TYPE"), dataIndex: "OWNER_TYPE", width: 80, hidden: false, align: "left"}
        ]
    });

    smodel = new Ext.grid.RowSelectionModel({
        singleSelect: true,
        listeners:{
            rowselect: function(sm, index, record) {
                deleteButton.enable();
            },
            rowdeselect: function(sm, index, record){
                deleteButton.disable();
            }
        }
    });


    storeIndicatorType = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'catalog/indicator'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields : [
                {name : 'CAT_UID'},
                {name : 'CAT_LABEL_ID'},
                {name : 'CAT_TYPE'},
                {name : 'CAT_FLAG'},
                {name : 'CAT_OBSERVATION'},
                {name : 'CAT_CREATE_DATE'},
                {name : 'CAT_UPDATE_DATE'}
            ]
        }),
        sortInfo: {
            field: 'CAT_LABEL_ID',
            direction: 'ASC'
        }
    });

    storeGraphic = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'catalog/graphic'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields : [
                {name : 'CAT_UID'},
                {name : 'CAT_LABEL_ID'},
                {name : 'CAT_TYPE'},
                {name : 'CAT_FLAG'},
                {name : 'CAT_OBSERVATION'},
                {name : 'CAT_CREATE_DATE'},
                {name : 'CAT_UPDATE_DATE'}
            ]
        }),
        sortInfo: {
            field: 'CAT_LABEL_ID',
            direction: 'ASC'
        }
    });

    storeFrequency = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'catalog/periodicity'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields : [
                {name : 'CAT_UID'},
                {name : 'CAT_LABEL_ID'},
                {name : 'CAT_TYPE'},
                {name : 'CAT_FLAG'},
                {name : 'CAT_OBSERVATION'},
                {name : 'CAT_CREATE_DATE'},
                {name : 'CAT_UPDATE_DATE'}
            ]
        }),
        sortInfo: {
            field: 'CAT_LABEL_ID',
            direction: 'ASC'
        }
    });

    var project = Ext.data.Record.create ([
        {
            name : 'prj_uid',
            type: 'string'
        },
        {
            name : 'prj_name',
            type: 'string'
        },
        {
            name : 'prj_description',
            type: 'string'
        },
        {
            name : 'prj_category',
            type: 'string'
        },
        {
            name : 'prj_type',
            type: 'string'
        },
        {
            name : 'prj_create_date',
            type: 'string'
        },
        {
            name : 'prj_update_date',
            type: 'string'
        }
    ]);

    storeProject = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'project'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            fields :project
        }),
        sortInfo: {
            field: 'prj_name',
            direction: 'ASC'
        },
        listeners: {
            load: function( store ) {
                var p = new project({
                    prj_name: _('ID_ALL_PROCESS'),
                    prj_uid: '0',
                    prj_description: '0',
                    prj_category: '0',
                    prj_type: '0',
                    prj_create_date: '0',
                    prj_update_date: '0'
                });
                store.insert(0, p);
            }
        }
    });

    ownerInfoGrid = new Ext.grid.GridPanel({
        region      : 'center',
        id          : 'ownerInfoGrid',
        height      : 200,
        width       : '100%',
        stateId     : 'gridDashboardList',
        enableHdMenu: true,
        frame       : false,
        columnLines : false,
        sortable    : false,
        store: store,
        cm: cmodel,
        sm: smodel,
        tbar: actionButtons,
        bbar: bbarpaging,
        listeners: {
            render: function(){
                this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
            }
        },
        view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text}',
            cls:"x-grid-empty",
            emptyText: _('ID_NO_RECORDS_FOUND')
        })
    });

    dashboardOwnerFields = new Ext.form.FieldSet({
        title       : _('ID_OWNER_INFORMATION'),
        collapsible : true,
        width       : '100%',
        //collapsed   : true,
        items       : [
            {
                xtype           : 'combo',
                id              : 'searchIem',
                anchor          : '60%',
                typeAhead       : false,
                hideLabel       : true,
                hideTrigger     : true,
                editable        : true,
                fieldLabel      : _('ID_SELECT'),
                displayField    : 'field1',
                emptyText       : _('ID_ENTER_SEARCH_TERM'),
                mode            : 'local',
                autocomplete    : true,
                triggerAction   : 'all',
                maskRe          : /([a-zA-Z0-9\s]+)$/,
                store           : new Ext.data.ArrayStore({
                    fields        : ['owner_uid','owner_label','owner_type'],
                    data          : dataUserGroup
                }),
                listConfig      : {
                    loadingText: _('ID_SEARCH'),
                    emptyText: _('ID_NO_FIELD_FOUND'),
                    getInnerTpl: function() {
                        return '<div class="search-item">' +
                            '<h3><span>{owner_uid}</span>{owner_label}</h3>' +
                            '{excerpt}' +
                            '</div>';
                    }
                },
                //pageSize    : 10,
                listeners   :{
                    scope   : this,
                    select  : function(combo, selection) {
                        var sw = false;
                        var data = store.data.items;
                        for (var i=0; i<data.length; i++) {
                            if (selection.data.field2 == data[i].data.OWNER_UID) {
                                sw = true;
                                break;
                            }
                        }
                        if (!sw) {
                            label = selection.data.field1.split('(');
                            var ow = new owner({
                                DAS_UID     : '',
                                OWNER_UID   : selection.data.field2,
                                OWNER_LABEL : label[0],
                                OWNER_TYPE  : selection.data.field3
                            });
                            ownerInfoGrid.store.insert(store.getCount(), ow);
                            ownerInfoGrid.store.totalCount = data.length +1;
                            ownerInfoGrid.store.sort('OWNER_LABEL', 'ASC');
                            ownerInfoGrid.getView().refresh();

                            Ext.getCmp('searchIem').clearValue();
                        } else {
                            label = selection.data.field3 == 'USER' ? 'ID_USER_REGISTERED' : 'ID_MSG_GROUP_NAME_EXISTS'
                            PMExt.warning(_('ID_DASHBOARD'), _(label));
                        }
                    }
                }
            },
            {
                title:  _('ID_PRO_USER')
            },
            ownerInfoGrid
        ]
    });

    addTabButton = new Ext.Button ({
        text: _('ID_NEW_TAB_INDICATOR'),
        iconCls: 'button_menu_ext ss_sprite ss_add',
        handler: addTab
    });

    tabPanel = new Ext.TabPanel({
        resizeTabs      : true,
        minTabWidth     : 115,
        tabWidth        : 135,
        enableTabScroll : true,
        //anchor          : '98%',
        width           : '100%',
        height          : 160,
        defaults        : {
            autoScroll  :true
        },
        listeners: {
            scope: this,
            beforeremove : function ( that, component ) {
                if (flag) {
                    if (tabPanel.items.items.length == 1 ) {
                        PMExt.warning(_('ID_DASHBOARD'), _('ID_MIN_INDICATOR_DASHBOARD'));
                        return false;
                    }

                    tabPanel.getItem(component.id).show();
                    Ext.MessageBox.show({
                        title: _('ID_CONFIRM'),
                        msg: _('ID_DELETE_INDICATOR_SURE'),
                        buttons: Ext.MessageBox.YESNO,
                        fn: function(buttonId) {
                            switch(buttonId) {
                                case 'no':
                                    flag = true;
                                    break;
                                case 'yes':
                                    tabPanel.getItem(component.id).show();
                                    flag = false;
                                    var dasIndUid = Ext.getCmp('DAS_IND_UID_'+component.id).getValue();
                                    if (typeof dasIndUid != 'undefined' && dasIndUid != '') {
                                        removeIndicator(dasIndUid);
                                    }
                                    tabActivate.remove(component.id);
                                    tabPanel.remove(component, true);
                                    break;
                            }
                        },
                        scope: that
                    });
                    return false;
                } else {
                    flag = true;
                }

            },
            tabchange : function ( that, tab  ) {
                var id = tabPanel.getActiveTab().id;
                if (dataIndicator == [] || dataIndicator == '' || Ext.getCmp('IND_TITLE_'+id).getValue() != '' || typeof dataIndicator[id-1] == 'undefined') {
                    return false;
                }

                Ext.getCmp('DAS_IND_UID_'+id).setValue(dataIndicator[id-1]['DAS_IND_UID']);
                var idType = dataIndicator[id-1]['DAS_IND_TYPE'];
                if (typeof dataIndicator[id-1]['DAS_IND_TYPE'] != 'undefined') {
                    Ext.getCmp('IND_TYPE_'+id).store.on('load', function (store) {
                        Ext.getCmp('IND_TYPE_'+id).setValue(idType);
                    });
                    Ext.getCmp('IND_TYPE_'+id).store.load();
                }
                Ext.getCmp('IND_TITLE_'+id).setValue(dataIndicator[id-1]['DAS_IND_TITLE']);
                Ext.getCmp('IND_TYPE_'+id).setValue(dataIndicator[id-1]['DAS_IND_TYPE']);
                Ext.getCmp('IND_GOAL_'+id).setValue(dataIndicator[id-1]['DAS_IND_GOAL']);
                var idProcess = dataIndicator[id-1]['DAS_UID_PROCESS'];
                if (typeof dataIndicator[id-1]['DAS_UID_PROCESS'] != 'undefined') {
                    Ext.getCmp('IND_PROCESS_'+id).store.on('load', function (store) {
                        Ext.getCmp('IND_PROCESS_'+id).setValue(idProcess);
                    });
                    Ext.getCmp('IND_PROCESS_'+id).store.load();
                }
                var idDirection = dataIndicator[id-1]['DAS_IND_DIRECTION'];
                if (typeof dataIndicator[id-1]['DAS_IND_DIRECTION'] != 'undefined') {
                    Ext.getCmp('DAS_IND_DIRECTION_'+id).setValue(idDirection);
                }
                var field = '';
                if (dataIndicator[id-1]['DAS_IND_TYPE'] != '1050') {
                    field = Ext.getCmp('IND_PROCESS_'+id);
                    field.enable();
                    field.show();
                }
                if (dataIndicator[id-1]['DAS_IND_TYPE'] != '1010' && dataIndicator[id-1]['DAS_IND_TYPE'] != '1030' && dataIndicator[id-1]['DAS_IND_TYPE'] != '1050') {
                    var fields = ['DAS_IND_FIRST_FIGURE_'+id,'DAS_IND_FIRST_FREQUENCY_'+ id,'DAS_IND_SECOND_FIGURE_'+id, 'DAS_IND_SECOND_FREQUENCY_'+ id];
                    for (var k=0; k<fields.length; k++) {
                        field = Ext.getCmp(fields[k]);
                        field.enable();
                        field.show();
                    }

                    var indFrist = dataIndicator[id-1]['DAS_IND_FIRST_FIGURE']
                    var indFristF = dataIndicator[id-1]['DAS_IND_FIRST_FREQUENCY']
                    var indSecond = dataIndicator[id-1]['DAS_IND_SECOND_FIGURE']
                    var indSecondF = dataIndicator[id-1]['DAS_IND_SECOND_FREQUENCY']
                    if (typeof dataIndicator[id-1]['DAS_IND_FIRST_FIGURE'] != 'undefined') {
                        Ext.getCmp('DAS_IND_FIRST_FIGURE_'+id).store.on('load', function (store) {
                            Ext.getCmp('DAS_IND_FIRST_FIGURE_'+id).setValue(indFrist);
                        });
                        Ext.getCmp('DAS_IND_FIRST_FIGURE_'+id).store.load();
                    }
                    if (typeof dataIndicator[id-1]['DAS_IND_FIRST_FREQUENCY'] != 'undefined') {
                        Ext.getCmp('DAS_IND_FIRST_FREQUENCY_'+id).store.on('load', function (store) {
                            Ext.getCmp('DAS_IND_FIRST_FREQUENCY_'+id).setValue(indFristF);
                        });
                        Ext.getCmp('DAS_IND_FIRST_FREQUENCY_'+id).store.load();
                    }
                    if (typeof dataIndicator[id-1]['DAS_IND_SECOND_FIGURE'] != 'undefined') {
                        Ext.getCmp('DAS_IND_SECOND_FIGURE_'+id).store.on('load', function (store) {
                            Ext.getCmp('DAS_IND_SECOND_FIGURE_'+id).setValue(indSecond);
                        });
                        Ext.getCmp('DAS_IND_SECOND_FIGURE_'+id).store.load();
                    }
                    if (typeof dataIndicator[id-1]['DAS_IND_SECOND_FREQUENCY'] != 'undefined') {
                        Ext.getCmp('DAS_IND_SECOND_FREQUENCY_'+id).store.on('load', function (store) {
                            Ext.getCmp('DAS_IND_SECOND_FREQUENCY_'+id).setValue(indSecondF);
                        });
                        Ext.getCmp('DAS_IND_SECOND_FREQUENCY_'+id).store.load();
                    }
                }
            }
        }
    });

    dashboardIndicatorFields = new Ext.form.FieldSet({
        title       : _('ID_DASHBOARD_INDICATOR_INFORMATION'),
        items       : [
            addTabButton,
            tabPanel
        ]
    });

    //form
    frmDashboard = new Ext.FormPanel({
        id            : 'frmDashboard',
        labelWidth    : 250,
        labelAlign    :'right',
        autoScroll    : true,
        fileUpload    : true,
        width         : '100%',
        bodyStyle     : 'padding:10px',
        waitMsgTarget : true,
        frame         : true,
        defaults : {
            anchor     : '100%',
            allowBlank : false,
            resizable  : true,
            msgTarget  : 'side',
            align      : 'center'
        },
        items : [
            dashboardFields,
            dashboardOwnerFields,
            dashboardIndicatorFields
        ],
        buttons : [
            {
                text   : _('ID_SAVE'),
                id     : 'save',
                handler: validateNameDashboard
            },
            {
                text    : _('ID_CANCEL'),
                id      : 'cancel',
                handler : function() {
                    window.location = 'dashboardList';
                }
            }
        ]
    });

    ownerInfoGrid.on("afterrender", function(component) {
        component.getBottomToolbar().refresh.hideParent = true;
        component.getBottomToolbar().refresh.hide();
    });

    viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        items: [
            frmDashboard
        ]
    });

    dataUserGroup = [];
    storeGroup.on( 'load', function( store, records, options ) {
        for (var i=0; i< store.data.length; i++) {
            row = [];
            if (store.data.items[i].data.grp_status == 'ACTIVE') {
                row.push(store.data.items[i].data.grp_title + ' (' + _('ID_GROUP') + ')' );
                row.push(store.data.items[i].data.grp_uid);
                row.push('GROUP');
                dataUserGroup.push(row);
            }
        }
        dashboardOwnerFields.items.items[0].bindStore(dataUserGroup);
    } );

    storeUsers.on( 'load', function( store, records, options ) {
        for (var i=0; i< store.data.length; i++) {
            row = [];
            if (store.data.items[i].data.usr_status == 'ACTIVE') {
                row.push(storeUsers.data.items[i].data.usr_firstname + ' ' + storeUsers.data.items[i].data.usr_lastname + ' (' + _('ID_USER') + ')' );
                row.push(storeUsers.data.items[i].data.usr_uid);
                row.push('USER');
                dataUserGroup.push(row);
            }
        }
        dashboardOwnerFields.items.items[0].bindStore(dataUserGroup);
    } );

    if (DAS_UID != '') {
        loadInfoDashboard(DAS_UID);
        loadIndicators(DAS_UID);
    } else {
        addTab();
    }

    if (typeof(__DASHBOARD_ERROR__) !== 'undefined') {
        PMExt.notify(_('ID_DASHBOARD'), __DASHBOARD_ERROR__);
    }
});

//==============================================================//
var addTab = function (flag) {
    if (tabPanel.items.items.length > 3 ) {
        PMExt.warning(_('ID_DASHBOARD'), _('ID_MAX_INDICATOR_DASHBOARD'));
        return false;
    }
    var tab = {
        title   : _('ID_INDICATOR')+ ' '+ (++indexTab),
        id      : indexTab,
        iconCls : 'tabs',
        width       : "100%",
        items   : [
            new Ext.Panel({
                height      : 130,
                width       : "100%",
                border      : true,
                bodyStyle   : 'padding:10px',
                items : [
                    new Ext.form.FieldSet({
                        labelWidth  : 150,
                        labelAlign  :'right',
                        items : [
                            {
                                id          : 'DAS_IND_UID_' + indexTab,
                                xtype       : 'textfield',
                                hidden      : true
                            },
                            {
                                fieldLabel  : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_INDICATOR_TITLE')) +'"> * </span>' + _('ID_INDICATOR_TITLE'),
                                id          : 'IND_TITLE_'+ indexTab,
                                xtype       : 'textfield',
                                anchor      : '85%',
                                maskRe      : /^([a-zA-Z0-9_'\s]+)$/,
                                regex       : /^([a-zA-Z0-9_'\s]+)$/,
                                regexText   : _('ID_INVALID_VALUE', _('ID_INDICATOR_TITLE')),
                                maxLength   : 250,
                                allowBlank  : false
                            },
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                id              : 'IND_TYPE_'+ indexTab,
                                fieldLabel      : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_INDICATOR_TYPE')) +'"> * </span>' + _('ID_INDICATOR_TYPE'),
                                displayField    : 'CAT_LABEL_ID',
                                valueField      : 'CAT_UID',
                                forceSelection  : false,
                                emptyText       : _('ID_SELECT'),
                                selectOnFocus   : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeIndicatorType,
                                listeners:{
                                    scope: this,
                                    select: function(combo, record, index) {
                                        var value = combo.getValue();
                                        var field = '';
                                        var index = tabPanel.getActiveTab().id;
                                        var fields = ['DAS_IND_FIRST_FIGURE_'+index,'DAS_IND_FIRST_FREQUENCY_'+index,'DAS_IND_SECOND_FIGURE_'+index, 'DAS_IND_SECOND_FREQUENCY_'+index];
                                        if (value == '1050') {
                                            field = Ext.getCmp('IND_PROCESS_'+index);
                                            field.setValue('0');
                                            field.disable();
                                            field.hide();
                                        } else {
                                            field = Ext.getCmp('IND_PROCESS_'+index);
                                            field.enable();
                                            field.show();
                                        }
                                        if (value == '1010' || value == '1030' || value == '1050') {
                                            for (var i=0; i<fields.length; i++) {
                                                field = Ext.getCmp(fields[i]);
                                                field.disable();
                                                field.hide();
                                            }
                                        } else {
                                            for (var i=0; i<fields.length; i++) {
                                                field = Ext.getCmp(fields[i]);
                                                field.enable();
                                                field.show();
                                            }
                                        }
                                    }
                                }
                            }),
                            new Ext.form.FieldSet({
                                title : _('ID_INDICATOR_GOAL'),
                                width : "90%",
                                id  : 'fieldSet_'+ indexTab,
                                bodyStyle: 'paddingLeft: 75px;',
                                paddingLeft: "30px",
                                marginLeft : "60px",
                                layout : 'hbox',
                                hidden : true,
                                items       : [
                                    new Ext.form.ComboBox({
                                        editable        : false,
                                        id              : 'DAS_IND_DIRECTION_'+ indexTab,
                                        displayField    : 'label',
                                        valueField      : 'id',
                                        value           : '2',
                                        forceSelection  : false,
                                        selectOnFocus   : true,
                                        typeAhead       : true,
                                        autocomplete    : true,
                                        width           : 90,
                                        triggerAction   : 'all',
                                        mode            : 'local',
                                        allowBlank      : false,
                                        store           : new Ext.data.ArrayStore({
                                            id: 2,
                                            fields: [
                                                'id',
                                                'label'
                                            ],
                                            data: [['1', _('ID_LESS_THAN')], ['2', _('ID_MORE_THAN')]]
                                        })
                                    }),
                                    {
                                        fieldLabel  : _('ID_INDICATOR_GOAL'),
                                        id          : 'IND_GOAL_'+ indexTab,
                                        xtype       : 'textfield',
                                        anchor      : '40%',
                                        maskRe      : /([0-9\.]+)$/,
                                        maxLength   : 9,
                                        value       : 1,
                                        width       : 80,
                                        allowBlank  : false,
                                        listeners   : {
                                            focus : function(tb, e) {
                                                Ext.QuickTips.register({
                                                    target: tb,
                                                    title: _('ID_HELP'),
                                                    text: _('ID_GOAL_HELP')
                                                });
                                            }
                                        }
                                    }
                                ],
                                listeners:
                                {
                                    render: function()
                                    {
                                        var index = tabPanel.getActiveTab().id;
                                        var myfieldset = document.getElementById('fieldSet_'+index);
                                        myfieldset.style.marginLeft = "70px";
                                        myfieldset.style.marginRight = "70px";
                                    }
                                }

                            }),
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                fieldLabel      : '<span style=\"color:red;\" ext:qtip="'+ _('ID_FIELD_REQUIRED', _('ID_PROCESS')) +'"> * </span>' + _('ID_PROCESS'),
                                id              : 'IND_PROCESS_'+ indexTab,
                                displayField    : 'prj_name',
                                valueField      : 'prj_uid',
                                forceSelection  : true,
                                emptyText       : _('ID_EMPTY_PROCESSES'),
                                selectOnFocus   : true,
                                hidden          : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeProject
                            }),
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                fieldLabel      : _('ID_FIRST_FIGURE'),
                                displayField    : 'CAT_LABEL_ID',
                                id              : 'DAS_IND_FIRST_FIGURE_'+ indexTab,
                                valueField      : 'CAT_UID',
                                forceSelection  : false,
                                emptyText       : _('ID_SELECT'),
                                selectOnFocus   : true,
                                hidden          : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeGraphic
                            }),
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                fieldLabel      : _('ID_PERIODICITY'),
                                displayField    : 'CAT_LABEL_ID',
                                id              : 'DAS_IND_FIRST_FREQUENCY_'+ indexTab,
                                valueField      : 'CAT_UID',
                                forceSelection  : false,
                                emptyText       : _('ID_SELECT'),
                                selectOnFocus   : true,
                                hidden          : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeFrequency
                            }),
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                fieldLabel      : _('ID_SECOND_FIGURE'),
                                id              : 'DAS_IND_SECOND_FIGURE_'+ indexTab,
                                displayField    : 'CAT_LABEL_ID',
                                valueField      : 'CAT_UID',
                                forceSelection  : false,
                                emptyText       : _('ID_SELECT'),
                                selectOnFocus   : true,
                                hidden          : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeGraphic
                            }),
                            new Ext.form.ComboBox({
                                anchor          : '85%',
                                editable        : false,
                                fieldLabel      : _('ID_PERIODICITY'),
                                displayField    : 'CAT_LABEL_ID',
                                id              : 'DAS_IND_SECOND_FREQUENCY_'+ indexTab,
                                valueField      : 'CAT_UID',
                                forceSelection  : false,
                                emptyText       : _('ID_SELECT'),
                                selectOnFocus   : true,
                                hidden          : true,
                                typeAhead       : true,
                                autocomplete    : true,
                                triggerAction   : 'all',
                                store           : storeFrequency
                            })
                        ]
                    })
                ]
            })
        ],
        listeners : {
            scope: this,
            activate : function (that) {
                if (tabActivate.indexOf(that.id) == -1 ) {
                    tabActivate.push(that.id);
                }
            }
        },
        closable:true
    };
    if (flag != 'load') {
        tabPanel.add(tab).show();
    } else {
        tabPanel.add(tab);
    }
};


var deleteOwner = function (dasOwnerUid) {
    var rowSelected = ownerInfoGrid.getSelectionModel().getSelected();
    if (rowSelected) {
        Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_DASHBOARD_OWNER'),function(btn, text)
        {
            if (btn == 'yes') {
                if (rowSelected.data.DAS_UID == '' ) {
                    store.removeAt(ownerInfoGrid.getSelectionModel().lastActive);
                    return;
                }
                viewport.getEl().mask(_('ID_PROCESSING'));
                Ext.Ajax.request({
                    url : urlProxy + 'dashboard/'+ DAS_UID +'/owner/' + rowSelected.data.OWNER_UID,
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + credentials.access_token
                    },
                    success:  function (result, request) {
                        viewport.getEl().unmask();
                        response = Ext.util.JSON.decode(result.responseText);
                        PMExt.notify(_('ID_DASHBOARD'),_('ID_DASHBOARD_OWNER_SUCCESS_DELETE'));
                        ownerInfoGrid.store.load();
                    },
                    failure: function (result, request) {
                        Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
                    }
                });
            }
        });
    }
};

var validateNameDashboard = function () {

    Ext.Ajax.request({
        url : urlProxy + 'dashboard?limit=100',
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);

            tabPanel.getItem(0).show();
            var title = Ext.getCmp('DAS_TITLE').getValue();

            for (var i=0; i<jsonResp.data.length; i++) {
                if (jsonResp.data[i].DAS_TITLE == title && DAS_UID != jsonResp.data[i].DAS_UID ) {
                    PMExt.warning(_('ID_DASHBOARD'), _('ID_DIRECTORY_NAME_EXISTS_ENTER_ANOTHER', title));
                    return;
                }
            }
            saveDashboard();
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            PMExt.error(_('ID_ERROR'),jsonResp.error.message);
        }
    });
}

var saveDashboard = function () {
    var title = Ext.getCmp('DAS_TITLE').getValue();
    var data = {};
    if (title == '' ) {
        PMExt.warning(_('ID_DASHBOARD'), _('ID_DASHBOARD_TITLE') + ' '+ _('ID_IS_REQUIRED'));
        Ext.getCmp('DAS_TITLE').focus(true,10);
        return false;
    } else if (!Ext.getCmp('DAS_TITLE').isValid()) {
        PMExt.warning(_('ID_DASHBOARD'), _('ID_INVALID_VALUE', _('ID_DASHBOARD_TITLE')));
        Ext.getCmp('DAS_TITLE').focus(true,10);
        return false;
    }
    data['DAS_TITLE'] = title;
    var description = Ext.getCmp('DAS_DESCRIPTION').getValue();

    data['DAS_DESCRIPTION'] = description;
    myMask.msg = _('ID_SAVING');
    myMask.show();

    if (DAS_UID == '') {
        $.ajax({
            url : urlProxy + 'dashboard',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            data: JSON.stringify(data),
            success: function (response) {
                DAS_UID = response;
                saveAllDashboardOwner(response);
                saveAllIndicators(response);
                myMask.hide();
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                myMask.hide();
                PMExt.error(_('ID_ERROR'), jsonResp.error.message);
            },
            async: false
        });
    } else {
        data['DAS_UID'] = DAS_UID
        $.ajax({
            url : urlProxy + 'dashboard',
            type: 'PUT',
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            data: JSON.stringify(data),
            success: function (response) {
                saveAllDashboardOwner(DAS_UID);
                saveAllIndicators(DAS_UID);
                myMask.hide();
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                myMask.hide();
                PMExt.error(_('ID_ERROR'), jsonResp.error.message);
            },
            async: false
        });
    }
};

var saveAllIndicators = function (DAS_UID) {
    for (var tab in tabActivate) {
        if (tab == 'remove' || tab == 'indexOf' || tab == 'map') {
            continue;
        }
        tabPanel.getItem(tabActivate[tab]).show();
        var fieldsTab = tabPanel.getItem(tabActivate[tab]).items.items[0].items.items[0].items.items;

        if (fieldsTab[1].getValue().trim() == '') {
            PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_TITLE_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
            fieldsTab[1].focus(true,10);
            return false;
        } else if (!fieldsTab[1].isValid()) {
            PMExt.warning(_('ID_DASHBOARD'), _('ID_INVALID_VALUE', _('ID_INDICATOR_TITLE')));
            fieldsTab[1].focus(true,10);
            return false;
        } else if (fieldsTab[2].getValue().trim() == '') {
            PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_TYPE_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
            fieldsTab[2].focus(true,10);
            return false;
        } else if (fieldsTab[2].getValue() != '1050' && fieldsTab[4].getValue().trim() == '') {
            PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_PROCESS_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
            fieldsTab[4].focus(true,10);
            return false;
        }

        var goal = fieldsTab[3];
        fieldsTab.push(goal.items.items[0]);
        fieldsTab.push(goal.items.items[1]);

        var data = [];
        data['DAS_UID'] = DAS_UID;

        for (var index in fieldsTab) {
            var node = fieldsTab[index];
            if (index == 'remove' || index == 'map') {
                continue;
            }

            var id = node.id;
            if (typeof id == 'undefined' || id.indexOf('fieldSet_') != -1 ) {
                continue;
            }
            id = id.split('_');
            var field = '';
            for (var part = 0; part<id.length-1; part++) {
                if (part == 0) {
                    field = id[part];
                } else {
                    field = field+'_'+id[part];
                }
            }
            var value = node.getValue();

            field = field == 'IND_TITLE' ? 'DAS_IND_TITLE' : field;
            field = field == 'IND_TYPE' ? 'DAS_IND_TYPE' : field;
            field = field == 'IND_PROCESS' ? 'DAS_UID_PROCESS' : field;
            field = field == 'IND_GOAL' ? 'DAS_IND_GOAL' : field;

            data[field] = value.trim();
        }
        saveDashboardIndicator(data, fieldsTab[0].id);
    }
    window.location = 'dashboardList';
};

var saveDashboardIndicator = function (options, id) {
    var data = {};
    data["DAS_UID"] = options['DAS_UID'];
    data["DAS_IND_TYPE"] = options['DAS_IND_TYPE'];
    data["DAS_IND_TITLE"] = options['DAS_IND_TITLE'];
    data["DAS_IND_GOAL"] = options['DAS_IND_GOAL'];
    data["DAS_IND_DIRECTION"] = options['DAS_IND_DIRECTION'];
    data["DAS_UID_PROCESS"] = options['DAS_UID_PROCESS'];
    data["DAS_IND_FIRST_FIGURE"] = options['DAS_IND_FIRST_FIGURE'];
    data["DAS_IND_FIRST_FREQUENCY"] = options['DAS_IND_FIRST_FREQUENCY'];
    data["DAS_IND_SECOND_FIGURE"] = options['DAS_IND_SECOND_FIGURE'];
    data["DAS_IND_SECOND_FREQUENCY"] = options['DAS_IND_SECOND_FREQUENCY'];

    if (options['DAS_IND_UID'] == '') {
        $.ajax({
            url : urlProxy + 'dashboard/indicator',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            data: JSON.stringify(data),
            success: function (response) {
                Ext.getCmp(id).setValue(response);
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                PMExt.error(_('ID_ERROR'),jsonResp.error.message);
            },
            async: false
        });
    } else {
        data["DAS_IND_UID"] = options['DAS_IND_UID'];
        $.ajax({
            url : urlProxy + 'dashboard/indicator',
            type: 'PUT',
            headers: {
                'Content-Type': 'application/json; charset=utf-8',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            data: JSON.stringify(data),
            success: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                PMExt.error(_('ID_ERROR'),jsonResp.error.message);
            },
            async: false
        });
    }
};

var saveAllDashboardOwner = function (DAS_UID) {
    var data = store.data.items;

    for(var i=0; i<data.length; i++) {
        var owner = data[i].data;
        if (owner.DAS_UID == '') {
            saveDashboardOwner (DAS_UID, owner.OWNER_UID, owner.OWNER_TYPE);
        }
    }
    store.proxy.api.read.url = urlProxy +  'dashboard/'+ DAS_UID +'/owners';
    ownerInfoGrid.store.load();
};

var saveDashboardOwner = function (DAS_UID, uid, type) {
    var data = {};
    data['DAS_UID'] = DAS_UID;
    data['OWNER_UID'] = uid;
    data['OWNER_TYPE'] = type;
    $.ajax({
        url : urlProxy + 'dashboard/owner',
        type: 'POST',
        headers: {
            'Content-Type': 'application/json; charset=utf-8',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        data: JSON.stringify(data),
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            PMExt.error(_('ID_ERROR'),jsonResp.error.message);
        },
        async: false
    });
};

var loadIndicators = function (DAS_UID) {
    Ext.Ajax.request({
        url : urlProxy + 'dashboard/' + DAS_UID + '/indicator',
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            if (jsonResp == '') {
                addTab('load');
            }
            dataIndicator = jsonResp;

            for (var i=0; i<=jsonResp.length-1; i++) {
                addTab('load');
                tabPanel.getItem(i+1).setTitle(jsonResp[i]['DAS_IND_TITLE']);
            }
            tabPanel.getItem(0).show();
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            PMExt.error(_('ID_ERROR'),jsonResp.error.message);
        }
    });
};

function converter(str) {
    str = str.replace(/&#39;/g, "'");
    return str;
}

var loadInfoDashboard = function (DAS_UID) {
    Ext.Ajax.request({
        url : urlProxy + 'dashboard/' + DAS_UID,
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            Ext.getCmp('DAS_TITLE').setValue(converter(jsonResp['DAS_TITLE']));
            Ext.getCmp('DAS_DESCRIPTION').setValue(jsonResp['DAS_DESCRIPTION']);
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            PMExt.error(_('ID_ERROR'),jsonResp.error.message);
        }
    });

};

var removeIndicator = function (dasIndUid) {
    myMask.msg = _('ID_REMOVE_FIELD');
    myMask.show();
    Ext.Ajax.request({
        url : urlProxy + 'dashboard/indicator/' + dasIndUid,
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            myMask.hide();
            PMExt.notify( _('ID_SUCSESS') , _('ID_DEL'));
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            myMask.hide();
            PMExt.notify(_('ID_ERROR'),jsonResp.error.message);
        }
    });
}