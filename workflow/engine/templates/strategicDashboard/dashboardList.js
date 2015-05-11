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
            iGrid = Ext.getCmp('infoGrid');
            rowSelected = iGrid.getSelectionModel().getSelected();
            if (rowSelected) {
                deleteDashboard();
            }
        }
    },
    {
        key: Ext.EventObject.F2,
        fn: function(k, e) {
            iGrid = Ext.getCmp('infoGrid');
            rowSelected = iGrid.getSelectionModel().getSelected();
            if (rowSelected){
                editDashboard();
            }
        }
    }
]);

var store;
var cmodel;
var infoGrid;
var viewport;
var smodel;
var newButton;
var editButton;
var deleteButton;
var statusButton;
var actionButtons;
var contextMenu;
var myMask;

Ext.onReady(function() {
    Ext.QuickTips.init();

    pageSize = 20; 
    myMask = new Ext.LoadMask(Ext.getBody(), {msg:_('ID_LOADING')});

    newButton = new Ext.Action({
        text: _('ID_NEW'),
        iconCls: 'button_menu_ext ss_sprite ss_add',
        handler: newDashboard
    });

    editButton = new Ext.Action({
        text: _('ID_EDIT'),
        iconCls: 'button_menu_ext ss_sprite  ss_pencil',
        handler: editDashboard,
        disabled: true
    });

    deleteButton = new Ext.Action({
        text: _('ID_DELETE'),
        iconCls: 'button_menu_ext ss_sprite  ss_delete',
        handler: deleteDashboard,
        disabled: true
    });

    statusButton = new Ext.Action({
        text: _('ID_STATUS'),
        icon : '',
        id : 'activator',
        iconCls: 'silk-add',
        handler: statusDashboard,
        disabled: true
    });

    contextMenu = new Ext.menu.Menu({
        items: [editButton, deleteButton, statusButton]
    });

    actionButtons = [newButton, '-', editButton, deleteButton, statusButton];

    smodel = new Ext.grid.RowSelectionModel({
        singleSelect: true,
        listeners:{
            rowselect: function(sm, index, record) {
                editButton.disable();
                deleteButton.disable();
                statusButton.enable();

                if (typeof(_rowselect) !== 'undefined') {
                    if (Ext.isArray(_rowselect)) {
                        for (var i = 0; i < _rowselect.length; i++) {
                            if (Ext.isFunction(_rowselect[i])) {
                                _rowselect[i](sm, index, record);
                            }
                        }
                    }
                }

                var activator = Ext.getCmp('activator');

                if( record.data.DAS_STATUS == 1 ){
                    activator.setIcon('/images/deactivate.png');
                    activator.setText( _('ID_DEACTIVATE') );
                    editButton.enable();
                    deleteButton.enable();
                } else {
                    activator.setIcon('/images/activate.png');
                    activator.setText( _('ID_ACTIVATE') );
                    editButton.disable();
                    deleteButton.disable();
                }
            },
            rowdeselect: function(sm, index, record){
                editButton.disable();
                deleteButton.disable();
                statusButton.disable();
                if (typeof(_rowdeselect) !== 'undefined') {
                    if (Ext.isArray(_rowdeselect)) {
                        for (var i = 0; i < _rowdeselect.length; i++) {
                            if (Ext.isFunction(_rowdeselect[i])) {
                                _rowdeselect[i](sm, index, record);
                            }
                        }
                    }
                }
            }
        }
    });

    store = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            api: {
                read : urlProxy + 'dashboard'
            }
            ,method: 'GET'
            ,headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + credentials.access_token
            }
        }),
        reader : new Ext.data.JsonReader( {
            root: 'data',
            totalProperty: 'total',
            fields : [
                {name : 'DAS_UID'},
                {name : "DAS_TITLE"},
                {name : 'DAS_DESCRIPTION'},
                {name : 'DAS_OWNER'},
                {name : 'DAS_UPDATE_DATE'},
                {name : 'DAS_LABEL_STATUS'},
                {name : 'DAS_STATUS'}
            ]
        }),
        sortInfo: {
            field: 'DAS_TITLE',
            direction: 'ASC'
        }
    });

    var formatLineWrap = function (value) {
        str = '<div class="title-dashboard-text">'+value+'</div>';
        return str;
    };

    cmodel = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {id:'DAS_UID',                  dataIndex: 'DAS_UID', hidden:true, hideable:false},
            {header: _("ID_TITLE"),         dataIndex: "DAS_TITLE", width: 150, hidden: false, align: "left", renderer : formatLineWrap},
            {header: _("ID_DESCRIPTION"),   dataIndex: "DAS_DESCRIPTION", width: 200, hidden: false, align: "left"},
            {header: _('ID_ASSIGNED_TO'),   dataIndex: 'DAS_OWNER', width: 200, hidden: false, align: 'center'},
            {header: _('ID_UPDATE_DATE'),   dataIndex: 'DAS_UPDATE_DATE', width: 80, hidden: false, align: 'center'},
            {header: _('ID_STATUS'),        dataIndex: 'DAS_LABEL_STATUS', width: 60, hidden: false, align: 'center'}
        ]
    });

    storePageSize = new Ext.data.SimpleStore({
        fields: ['size'],
        data: [['20'],['30'],['40'],['50'],['100']],
        autoLoad: true
    });

    comboPageSize = new Ext.form.ComboBox({
        typeAhead     : false,
        mode          : 'local',
        triggerAction : 'all',
        store: storePageSize,
        valueField: 'size',
        displayField: 'size',
        width: 50,
        editable: false,
        listeners:{
            select: function(c,d,i){
                bbarpaging.pageSize = parseInt(d.data['size']);
                bbarpaging.moveFirst();
            }
        }
    });

    comboPageSize.setValue(pageSize);

    bbarpaging = new Ext.PagingToolbar({
        pageSize: pageSize,
        store: store,
        displayInfo: true,
        displayMsg: _('ID_GRID_PAGE_DISPLAYING_DASHBOARD_MESSAGE') + '&nbsp; &nbsp; ',
        //displayMsg: 'Displaying Dashboards s {0} - {1} of {2}' + '&nbsp; &nbsp; ',
        emptyMsg: _('ID_GRID_PAGE_NO_DASHBOARD_MESSAGE'),
        //emptyMsg: 'No Dashboards s to display',
        items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
    });

    infoGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'infoGrid',
        height:100,
        autoWidth : true,
        stateful : true,
        stateId : 'gridDashboardList',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        columnLines: false,
        viewConfig: {
          forceFit:true
        },
        title : _('ID_KPI'),
        store: store,
        cm: cmodel,
        sm: smodel,
        tbar: actionButtons,
        bbar: bbarpaging,
        listeners: {
            rowdblclick: editDashboard,
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

    infoGrid.on('rowcontextmenu',
        function (grid, rowIndex, evt) {
            var sm = grid.getSelectionModel();
            sm.selectRow(rowIndex, sm.isSelected(rowIndex));
        },
        this
    );

    infoGrid.on('contextmenu',
        function (evt) {
            evt.preventDefault();
        },
        this
    );

    infoGrid.addListener('rowcontextmenu',onMessageContextMenu,this);

    infoGrid.store.load();

    viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        items: [
            infoGrid
        ]
    });

    if (typeof(__DASHBOARD_ERROR__) !== 'undefined') {
        PMExt.notify(_('ID_DASHBOARD'), __DASHBOARD_ERROR__);
    }
});

//Funtion Handles Context Menu Opening
onMessageContextMenu = function (grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    contextMenu.showAt([coords[0], coords[1]]);
};

//Load Grid By Default
gridByDefault = function() {
    infoGrid.store.load();
};

//New Dashboard  Action
newDashboard = function() {
    location.href = 'formDashboard';
};

//Edit Dashboard  Action
editDashboard = function() {
    var rowSelected = infoGrid.getSelectionModel().getSelected();
    if (rowSelected && rowSelected.data.DAS_STATUS == 1 ){
        location.href = 'formEditDashboard?DAS_UID=' + rowSelected.data.DAS_UID;
    }
};

//Delete Dashboard  Action
deleteDashboard = function() {
    var rowSelected = infoGrid.getSelectionModel().getSelected();
    if (rowSelected) {
        Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_CONFIRM_DELETE_DASHBOARD'),function(btn, text)
        {
            if (btn == 'yes') {
                viewport.getEl().mask(_('ID_PROCESSING'));
                Ext.Ajax.request({
                    url : urlProxy + 'dashboard/' + rowSelected.data.DAS_UID,
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + credentials.access_token
                    },
                    success:  function (result, request) {
                        viewport.getEl().unmask();
                        response = Ext.util.JSON.decode(result.responseText);
                        PMExt.notify(_('ID_DASHBOARD'),_('ID_DASHBOARD_SUCCESS_DELETE'));
                        editButton.disable();
                        deleteButton.disable();
                        infoGrid.store.load();
                    },
                    failure: function (result, request) {
                        Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
                    }
                });
            }
        });
    }
};

//Status Dashboard  Action
statusDashboard = function() {
    var rows = infoGrid.getSelectionModel().getSelections();
    if( rows.length > 0 ) {
        for (i=0; i<rows.length; i++) {
            var status;
            if (rows[i].data.DAS_STATUS == 1) {
                status = 0;
            } else {
                status = 1;
            }

            Ext.Ajax.request({
                url : urlProxy + 'dashboard',
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + credentials.access_token
                },
                jsonData: {
                    "DAS_UID"   : rows[i].data.DAS_UID,
                    "DAS_STATUS": status
                },
                success:  function (result, request) {
                    editButton.disable();
                    deleteButton.disable();
                    statusButton.disable();

                    statusButton.setIconClass('silk-add');
                    statusButton.setText( _('ID_STATUS') );
                    infoGrid.store.load();
                },
                failure: function (result, request) {
                    Ext.MessageBox.alert( _('ID_ALERT'), _('ID_AJAX_COMMUNICATION_FAILED') );
                }
            });
        }
    } else {
        Ext.Msg.show({
            title:'',
            msg: _('ID_NO_SELECTION_WARNING'),
            buttons: Ext.Msg.INFO,
            fn: function(){},
            animEl: 'elId',
            icon: Ext.MessageBox.INFO,
            buttons: Ext.MessageBox.OK
        });
    }
};
