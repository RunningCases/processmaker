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
var dashboardIndicatorPanel;
var store;

var indexTab = 0;
var comboPageSize = 10;
var resultTpl;
var storeIndicatorType;
var storeGraphic;
var storeFrecuency;
var storeProject;
var storeGroup;
var storeUsers;
var dataUserGroup;
var dasIndUid;
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
                fieldLabel  : _('ID_DASHBOARD_TITLE'),
                xtype       : 'textfield',
                anchor      : '85%',
                maxLength   : 250,
                maskRe      : /([a-zA-Z0-9\s]+)$/,
                allowBlank  : false
            },
            {
                xtype           : 'textarea',
                id              : 'DAS_DESCRIPTION',
                fieldLabel      : _('ID_DESCRIPTION'),
                labelSeparator  : '',
                anchor          : '85%',
                maskRe          : /([a-zA-Z0-9\s]+)$/,
                height          : 50,
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
        }
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
        displayMsg: _('ID_GRID_PAGE_DISPLAYING_0WNER_MESSAGE') + '&nbsp; &nbsp; ',
        emptyMsg: _('ID_GRID_PAGE_NO_OWNER_MESSAGE'),
    });

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {   id:'DAS_UID',               dataIndex: 'DAS_UID', hidden:true, hideable:false},
            {   header: _("ID_OWNER"),      dataIndex: "OWNER_LABEL", width: 150, hidden: false, align: "left"},
            {   header: _("ID_OWNER_TYPE"),    dataIndex: "OWNER_TYPE", width: 80, hidden: false, align: "left"}
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

    storeFrecuency = new Ext.data.GroupingStore( {
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
        //layout      : 'fit',
        id          : 'ownerInfoGrid',
        height      : 200,
        //autoWidth   : true,
        //anchor      : '80%',
        width       : '100%',
        //stateful    : true,
        stateId     : 'gridDashboardList',
        //enableColumnResize  : true,
        enableHdMenu: true,
        frame       : false,
        columnLines : false,
        /*viewConfig : {
          forceFit:true
        },*/
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
                            var ow = new owner({
                                DAS_UID     : '',
                                OWNER_UID   : selection.data.field2,
                                OWNER_LABEL : selection.data.field1,
                                OWNER_TYPE  : selection.data.field3
                            });
                            ownerInfoGrid.store.insert(store.getCount(), ow);
                            ownerInfoGrid.store.totalCount = data.length +1;
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
                title:  _('ID_PRO_USER'),
            },
            ownerInfoGrid
        ]
    });

    addTabButton = new Ext.Button ({
        text: _('ID_NEW_TAB_INDICATOR'),
        iconCls: 'button_menu_ext ss_sprite ss_add',
        handler: addTab,
    });

    tabPanel = new Ext.TabPanel({
        resizeTabs      : true,
        minTabWidth     : 115,
        tabWidth        : 135,
        enableTabScroll : true,
        //anchor          : '98%',
        width           : '100%',
        height          : 315,
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
                        buttons: Ext.MessageBox.YESNOCANCEL,
                        fn: function(buttonId) {
                            switch(buttonId) {
                                case 'no':
                                    flag = true;
                                    break;
                                case 'yes':
                                    flag = false;
                                    var dasIndUid = Ext.getCmp('DAS_IND_UID_'+component.id).getValue();
                                    if (typeof dasIndUid != 'undefined' && dasIndUid != '') {
                                        removeIndicator(dasIndUid);
                                    }
                                    tabActivate.remove(component.id);
                                    tabPanel.remove(component);
                                    break;
                                case 'cancel':
                                    flag = true;
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
                if (dataIndicator[id-1]['DAS_IND_TYPE'] != '1010' && dataIndicator[id-1]['DAS_IND_TYPE'] != '1030') {
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

    ownerInfoGrid.store.load();

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
                row.push(store.data.items[i].data.grp_title);
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
                row.push(storeUsers.data.items[i].data.usr_firstname + ' ' + storeUsers.data.items[i].data.usr_lastname);
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
    console.log('flag', flag);
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
                    height      : 275,
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
                                    fieldLabel  : _('ID_INDICATOR_TITLE'),
                                    id          : 'IND_TITLE_'+ indexTab,
                                    xtype       : 'textfield',
                                    anchor      : '85%',
                                    maskRe      : /([a-zA-Z0-9\s]+)$/,
                                    maxLength   : 250,
                                    allowBlank  : false
                                },
                                new Ext.form.ComboBox({
                                    anchor          : '85%',
                                    editable        : false,
                                    id              : 'IND_TYPE_'+ indexTab,
                                    fieldLabel      : _('ID_INDICATOR_TYPE'),
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
                                            var index = tabPanel.getActiveTab().id;
                                            var fields = ['DAS_IND_FIRST_FIGURE_'+index,'DAS_IND_FIRST_FREQUENCY_'+index,'DAS_IND_SECOND_FIGURE_'+index, 'DAS_IND_SECOND_FREQUENCY_'+index];
                                            if (value == '1010' || value == '1030') {
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
                                    items       : [
                                        new Ext.form.ComboBox({
                                            editable        : false,
                                            id              : 'DAS_IND_DIRECTION_'+ indexTab,
                                            displayField    : 'label',
                                            valueField      : 'id',
                                            value           : 2,
                                            forceSelection  : false,
                                            selectOnFocus   : true,
                                            typeAhead       : true,
                                            autocomplete    : true,
                                            triggerAction   : 'all',
                                            mode            : 'local',
                                            store           : new Ext.data.ArrayStore({
                                                id: 0,
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
                                            width       : 80,
                                            allowBlank  : false
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
                                    fieldLabel      : _('ID_PROCESS'),
                                    id              : 'IND_PROCESS_'+ indexTab,
                                    displayField    : 'prj_name',
                                    valueField      : 'prj_uid',
                                    forceSelection  : false,
                                    emptyText       : _('ID_EMPTY_PROCESSES'),
                                    selectOnFocus   : true,
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
                                    store           : storeFrecuency
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
                                    store           : storeFrecuency
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
                },
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
    title = title.trim();
    if (title == '') {
        PMExt.warning(_('ID_DASHBOARD'), _('ID_DASHBOARD_TITLE') + ' '+ _('ID_IS_REQUIRED'));
        Ext.getCmp('DAS_TITLE').focus(true,10);
        return false;
    }
    var description = Ext.getCmp('DAS_DESCRIPTION').getValue();
    myMask.msg = _('ID_SAVING');
    myMask.show();

    if (DAS_UID == '') {
        Ext.Ajax.request({
            url : urlProxy + 'dashboard',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            jsonData: {
                "DAS_TITLE" : title,
                "DAS_DESCRIPTION" : description
            },
            success: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                DAS_UID = jsonResp;
                saveAllDashboardOwner(jsonResp);
                saveAllIndicators(jsonResp);
                myMask.hide();
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                myMask.hide();
                PMExt.error(_('ID_ERROR'), jsonResp.error.message);
            }
        });
    } else {
        Ext.Ajax.request({
            url : urlProxy + 'dashboard',
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            jsonData: {
                "DAS_UID"           : DAS_UID,
                "DAS_TITLE"         : title,
                "DAS_DESCRIPTION"   : description
            },
            success: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                saveAllDashboardOwner(jsonResp);
                saveAllIndicators(jsonResp);
                myMask.hide();
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                myMask.hide();
                PMExt.error(_('ID_ERROR'), jsonResp.error.message);
            }
        });
    }
};

var saveAllIndicators = function (DAS_UID) {
    for (var tab in tabActivate) {
        if (tab == 'remove') {
            continue;
        }
        tabPanel.getItem(tabActivate[tab]).show();
        var fieldsTab = tabPanel.getItem(tabActivate[tab]).items.items[0].items.items[0].items.items;
        var goal = fieldsTab[3];
        delete fieldsTab[3];
        fieldsTab.push(goal.items.items[0]);
        fieldsTab.push(goal.items.items[1]);


        data = [];
        data['DAS_UID'] = DAS_UID;

        for (var index in fieldsTab) {
            var node = fieldsTab[index];
            if (index == 'remove') {
                continue;
            }

            id = node.id;
            id = id.split('_');
            field = '';
            for (var part = 0; part<id.length-1; part++) {
                if (part == 0) {
                    field = id[part];
                } else {
                    field = field+'_'+id[part];
                }
            }
            value = node.getValue();


            if (field == 'IND_TITLE' && value.trim() == '') {
                PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_TITLE_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
                node.focus(true,10);
                return false;
            } else if (field == 'IND_TYPE' && value.trim() == '') {
                PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_TYPE_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
                node.focus(true,10);
                return false;
            } else if (field == 'IND_PROCESS' && value.trim() == '') {
                PMExt.warning(_('ID_DASHBOARD'), _('ID_INDICATOR_PROCESS_REQUIRED', tabPanel.getItem(tabActivate[tab]).title));
                node.focus(true,10);
                return false;
            }

            field = field == 'IND_TITLE' ? 'DAS_IND_TITLE' : field;
            field = field == 'IND_TYPE' ? 'DAS_IND_TYPE' : field;
            field = field == 'IND_PROCESS' ? 'DAS_UID_PROCESS' : field;
            field = field == 'IND_GOAL' ? 'DAS_IND_GOAL' : field;

            data[field] = value.trim();
        }
        saveDashboardIndicator(data);
    }
    window.location = 'dashboardList';
};


var saveDashboardIndicator = function (options) {
    if (options['DAS_IND_UID'] == '') {
        Ext.Ajax.request({
            url : urlProxy + 'dashboard/indicator',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            jsonData: {
                "DAS_UID"                   : options['DAS_UID'],
                "DAS_IND_TYPE"              : options['DAS_IND_TYPE'],
                "DAS_IND_TITLE"             : options['DAS_IND_TITLE'],
                "DAS_IND_GOAL"              : options['DAS_IND_GOAL'],
                "DAS_IND_DIRECTION"         : options['DAS_IND_DIRECTION'],
                "DAS_UID_PROCESS"           : options['DAS_UID_PROCESS'],
                "DAS_IND_FIRST_FIGURE"      : options['DAS_IND_FIRST_FIGURE'],
                "DAS_IND_FIRST_FREQUENCY"   : options['DAS_IND_FIRST_FREQUENCY'],
                "DAS_IND_SECOND_FIGURE"     : options['DAS_IND_SECOND_FIGURE'],
                "DAS_IND_SECOND_FREQUENCY"  : options['DAS_IND_SECOND_FREQUENCY']
            },
            success: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                PMExt.error(_('ID_ERROR'),jsonResp.error.message);
            }
        });
    } else {
        Ext.Ajax.request({
            url : urlProxy + 'dashboard/indicator',
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            },
            jsonData: {
                "DAS_UID"                   : options['DAS_UID'],
                "DAS_IND_UID"               : options['DAS_IND_UID'],
                "DAS_IND_TYPE"              : options['DAS_IND_TYPE'],
                "DAS_IND_TITLE"             : options['DAS_IND_TITLE'],
                "DAS_IND_GOAL"              : options['DAS_IND_GOAL'],
                "DAS_IND_DIRECTION"         : options['DAS_IND_DIRECTION'],
                "DAS_UID_PROCESS"           : options['DAS_UID_PROCESS'],
                "DAS_IND_FIRST_FIGURE"      : options['DAS_IND_FIRST_FIGURE'],
                "DAS_IND_FIRST_FREQUENCY"   : options['DAS_IND_FIRST_FREQUENCY'],
                "DAS_IND_SECOND_FIGURE"     : options['DAS_IND_SECOND_FIGURE'],
                "DAS_IND_SECOND_FREQUENCY"  : options['DAS_IND_SECOND_FREQUENCY']
            },
            success: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
            },
            failure: function (response) {
                var jsonResp = Ext.util.JSON.decode(response.responseText);
                PMExt.error(_('ID_ERROR'),jsonResp.error.message);
            }
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
    myMask.msg = _('ID_SAVING');
    myMask.show();
    Ext.Ajax.request({
        url : urlProxy + 'dashboard/owner',
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        jsonData: {
            "DAS_UID" : DAS_UID,
            "OWNER_UID" : uid,
            "OWNER_TYPE" : type
        },
        success: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            myMask.hide();
        },
        failure: function (response) {
            var jsonResp = Ext.util.JSON.decode(response.responseText);
            myMask.hide();
            PMExt.error(_('ID_ERROR'),jsonResp.error.message);
        }
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
            Ext.getCmp('DAS_TITLE').setValue(jsonResp['DAS_TITLE']);
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