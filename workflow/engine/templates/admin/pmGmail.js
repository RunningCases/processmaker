var saveButton;
var testButton;
var storeUsers;
Ext.onReady(function(){
    Ext.QuickTips.init();

    testButton = new Ext.Action({
        text : _('ID_TEST_CONNECTION'),
        disabled : !enablePMGmail,
        handler : testSettings
    });

    saveButton = new Ext.Action({
        text : _('ID_SAVE_SETTINGS'),
        disabled : true,
        handler : saveSettings
    });

    var configurationPMGmail = new Ext.form.FieldSet({
        title: _('ID_PMGMAIL_SETTINGS'),
        items: [
            {
                xtype: 'checkbox',
                id: 'status_pmgmail',
                name: 'status_pmgmail',
                boxLabel: _('ID_ENABLE_PMGMAIL'),
                value: 0,
                inputValue: 1,
                uncheckedValue: 0,
                listeners   : {
                    check : function(that, checked) {
                        changeSettings();
                        if (checked) {
                            Ext.getCmp('email_service_account').enable();
                            Ext.getCmp('status_pmgmail').enable();
                            Ext.getCmp('file_p12').enable();
                        } else {

                            Ext.MessageBox.confirm(
                                _('ID_CONFIRM'),
                                _('ID_PMGMAIL_DISABLE'),
                                function (btn, text) {
                                    if (btn == "yes") {
                                        Ext.getCmp('email_service_account').disable();
                                        Ext.getCmp('file_p12').disable();
                                        Ext.getCmp('listUsers').hide();
                                        testButton.disable();
                                        saveButton.disable();
                                        saveSettings();
                                    } else {
                                        Ext.getCmp('status_pmgmail').enable();
                                        Ext.getCmp('email_service_account').enable();
                                        Ext.getCmp('file_p12').enable();
                                        Ext.getCmp('status_pmgmail').setValue(1);
                                        return false;
                                    }
                                }
                            );
                        }
                    }
                }
            },
            {
                xtype       : 'textfield',
                id          : 'email_service_account',
                name        : 'email_service_account',
                fieldLabel  : _('ID_PMG_EMAIL'),
                width       : 400,
                allowBlank  : false,
                value       : accountEmail,
                disabled    : !enablePMGmail,
                listeners   : {
                    change: function(){
                        changeSettings();
                    },
                    focus   : function(tb, e) {
                        Ext.QuickTips.register({
                            target: tb,
                            title: _('ID_PMG_EMAIL'),
                            text: accountEmail
                        });
                    }
                }
            },
            {
                xtype       : 'fileuploadfield',
                id          : 'file_p12',
                emptyText   : _('ID_PMG_SELECT_FILE'),
                fieldLabel  : _('ID_PMG_FILE'),
                name        : 'file_p12',
                buttonText  : '',
                width       : 400,
                disabled    : !enablePMGmail,
                buttonCfg   : {
                    iconCls : 'upload-icon'
                },
                listeners:{
                    change  : function(){
                        changeSettings();
                    },
                    afterrender:function(cmp){
                        changeSettings();
                        cmp.fileInput.set({
                            accept:'*/p12'
                        });
                    }
                },
                regex       : /(.)+((\.p12)(\w)?)$/i,
                regexText   : _('ID_PMG_TYPE_ACCEPT')
            },
            {
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : '',
                text        : fileP12,
                width       : 400,
                style       : "padding-left:180px;"
            }
        ]
    });

    var testPMGmail = new Ext.form.FieldSet({
        id      : 'testPMGmail',
        title   : _('ID_TEST_CONNECTION'),
        hidden  : true,
        items   : [
            {
                id          : 'currentUserName',
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : _('ID_CURRENT_USER'),
                text        : '',
                width       : 400
            },
            {
                id          : 'rootFolderId',
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : _('ID_ROOT_FOLDER'),
                text        : '',
                width       : 400
            },
            {
                id          : 'quotaType',
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : _('ID_QUOTA_TYPE'),
                text        : '',
                width       : 400
            },
            {
                id          : 'quotaBytesTotal',
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : _('ID_QUOTA_TOTAL'),
                text        : '',
                width       : 400
            },
            {
                id          : 'quotaBytesUsed',
                xtype       : 'label',
                labelAlign  : 'right',
                fieldLabel  : _('ID_QUOTA_USED'),
                text        : '',
                width       : 400
            },
            {
                id          : 'responseGmailTest',
                xtype       : 'label',
                labelAlign  : 'right',
                labelStyle  : 'font-weight:bold;',
                fieldLabel  : _('SERVER_RESPONSE'),
                text        : '',
                width       : 400
            }
        ]
    });

    storeUsers = new Ext.data.JsonStore({
        url: '../pmGmail/testUserGmail',
        fields: [
            'USR_UID',
            'FULL_NAME',
            'EMAIL'
        ]
    });

    var listViewUsers = new Ext.list.ListView({
        store: storeUsers,
        singleSelect: true,
        emptyText: _('ID_GRID_PAGE_NO_USERS_MESSAGE'),
        reserveScrollOffset: true,

        columns: [
            {
                header: _('ID_FULL_NAME'),
                width:.4,
                dataIndex: 'FULL_NAME'
            },{
                header: _('ID_EMAIL'),
                width:.4,
                dataIndex: 'EMAIL'
            }]
    });

    var listUsers = new Ext.form.FieldSet({
        id      : 'listUsers',
        title   : _('ID_USERS'),
        hidden  : true,
        items   : [
            listViewUsers
        ]
    });

    var formPMGmail = new Ext.FormPanel({
        title       : '&nbsp',
        id          :'formPMGmail',
        labelWidth  : 170,
        labelAlign  :'right',
        autoScroll  : true,
        fileUpload  : true,
        bodyStyle   :'padding:5px',
        waitMsgTarget : true,
        frame       : true,
        defaults: {
            allowBlank: false,
            msgTarget: 'side',
            align:'center'
        },
        items:[ configurationPMGmail, testPMGmail, listUsers ],
        buttons : [testButton, saveButton]
    });

    var viewport = new Ext.Viewport({
        layout: 'fit',
        autoScroll: false,
        items: [
            formPMGmail
        ]
    });

    Ext.getCmp('status_pmgmail').checked = enablePMGmail;
    Ext.getCmp('status_pmgmail').setValue(enablePMGmail);
});

var testSettings = function ()
{
    storeUsers.reload();
    Ext.getCmp('testPMGmail').hide();
    Ext.getCmp('listUsers').hide();
    Ext.getCmp('currentUserName').setText('');
    Ext.getCmp('rootFolderId').setText('');
    Ext.getCmp('quotaType').setText('');
    Ext.getCmp('quotaBytesTotal').setText('');
    Ext.getCmp('quotaBytesUsed').setText('');
    Ext.getCmp('responseGmailTest').setText('');
    Ext.getCmp('responseGmailTest').container.dom.style.color = 'red';

    Ext.getCmp('formPMGmail').getForm().submit( {
        url : '../pmGmail/testConfigPmGmail',
        waitMsg : _('ID_TEST_CONNECTION'),
        waitTitle : "&nbsp;",
        timeout : 36000,
        success : function(obj, resp) {
            Ext.getCmp('testPMGmail').show();
            Ext.getCmp('listUsers').show();
            var response = Ext.decode(resp.response.responseText);
            Ext.getCmp('currentUserName').setText(response.currentUserName);
            Ext.getCmp('rootFolderId').setText(response.rootFolderId);
            Ext.getCmp('quotaType').setText(response.quotaType);
            Ext.getCmp('quotaBytesTotal').setText(response.quotaBytesTotal);
            Ext.getCmp('quotaBytesUsed').setText(response.quotaBytesUsed);
            Ext.getCmp('responseGmailTest').setText(response.responseGmailTest);

            Ext.getCmp('responseGmailTest').container.dom.style.color = 'green';
            if (storeUsers.data.length == 0) {
                saveButton.enable();
            }
        },
        failure: function(obj, resp) {
            Ext.getCmp('testPMGmail').show();
            Ext.getCmp('listUsers').hide();
            saveButton.disable();
            Ext.getCmp('responseGmailTest').setText(resp.result.responseGmailTest);
        }
    });
};

var saveSettings = function ()
{
    Ext.getCmp('formPMGmail').getForm().submit( {
        url : '../pmGmail/saveConfigPmGmail',
        waitMsg : _('ID_SAVING_PROCESS'),
        waitTitle : "&nbsp;",
        timeout : 36000,
        success : function(obj, resp) {
            var response = Ext.decode(resp.response.responseText);
            parent.PMExt.notify(_('ID_INFO'),_('ID_SAVED_SUCCESSFULLY'));
            location.href = '../pmGmail/formPMGmail';
        },
        failure: function(obj, resp) {
            PMExt.error( _('ID_ERROR'), resp.result.message);
        }
    });
};

var changeSettings = function()
{
    Ext.getCmp('testPMGmail').hide();
    Ext.getCmp('listUsers').hide();
    if (Ext.getCmp('status_pmgmail').checked) {
        testButton.enable();
    }
    saveButton.disable();
};
