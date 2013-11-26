var sidebar,
    toolbar,
    shapeFactory,
    layout,
    centerPosition,
    pmApp,
    dynaformModule,
    inputModule,
    outputModule,
    triggerModule,
    reportModule,
    databaseModule,
    schedulerModule,
    menuTreeOptions,
    getAutoIncrementName,
    getMenuFactory;

getAutoIncrementName = function (type) {
    var i, j, k = canvas.getCustomShapes().getSize(), exists, index = 1, auxMap = {
        PMTask: __translations.pmActivityTask,
        PMSubProcess: __translations.pmActivitySubProcess,
        PMStartCase: __translations.pmEventStartCase,
        PMEndOfProcess: __translations.pmEventEndProcess,
        PMSelection: __translations.pmGatewaySelection,
        PMEvaluation: __translations.pmGatewayEvaluation,
        PMParallel: __translations.pmGatewayParallel,
        PMParallelByEvaluation: __translations.pmGatewayParallelEvaluation,
        PMParallelJoin: __translations.pmGatewayParallelJoin
    };

    for (i = 0; i < k; i += 1) {
        exists = false;
        for (j = 0; j < k; j += 1) {
            if (canvas.getCustomShapes().get(j).getName() === auxMap[type] + " # " + (i + 1)) {
                exists = true;
                break;
            }
        }
        if (!exists) {
            break;
        }
    }

    return auxMap[type] + " # " + (i + 1);
};

dynaformModule = function () {
    var w,
        wType,
        wBlank,
        formTypeDynaform,
        form,
        buttonNew;

    event.preventDefault();

    buttonNew = new PMUI.ui.Button({
        id: 'newDynaform',
        text: __translations.dynaformNewTitleWindow,
        handler: function () {
            event.preventDefault();
            h1 = document.createElement("h4");
            h1.innerHTML = __translations.dynaformNewDynaformHeader;
            h1.setAttribute("style","background-color:#676767;color:white;");
            radioGroup = new PMUI.field.RadioButtonGroupField({
                label: "Type",
                required: true,
                options: [
                    {
                        label: __translations.dynaformNewBlankOption,
                        value: "BLANK"
                    },
                    {
                        label: __translations.dynaformNewPmTableOption,
                        value: "PMTABLE"
                    },
                    {
                        label: __translations.dynaformCopyImportOption,
                        value: "COPY_IMPORT"
                    }
                ]
            });

            formTypeDynaform = new PMUI.form.FormPanel({
                width: 570,
                height: 50,
                fieldset: true,
                items: [
                    radioGroup
                ]
            });

            wType = new PMUI.ui.Window({
                title: __translations.dynaformNewDynaform,
                height: 200,
                width: 620,
                buttons: [
                    {
                        text: __translations.dynaformSelectTypeDynaform,
                        handler:  function() {
                            formBlank = new PMUI.form.Form({
                                width: 300,
                                title: "Dynaform Information",
                                items: [
                                    {
                                        pmType: "text",
                                        label: "Title",
                                        value: "",
                                        placeholder: "insert a title",
                                        name: "title",
                                        helper: "Introduce a title",
                                        required: true
                                    },
                                    {
                                        pmType: 'dropdown',
                                        label : 'Type',
                                        name : 'type',
                                        required: true,
                                        helper: "It is required to select a type of dynaform",
                                        options: [                                
                                            {
                                               label: "Normal",
                                               value: "NORMAL"
                                            },
                                            {
                                                label: "Grid",
                                                value: "GRID"
                                            }
                                        ]
                                    },
                                    {
                                        pmType: "textarea",
                                        rows: 200,
                                        label: "Description",

                                    }
                                ]
                            });
                            wBlank = new PMUI.ui.Window({
                                title: __translations.dynaformTitleBlankDynaform,
                                height: 350,
                                width: 350,
                                buttonsPosition : 'center',
                                buttons: [
                                    {
                                        text: "Save"
                                    },
                                    {
                                        text: "Save & Open"
                                    },
                                    {
                                        text:  "Cancel"
                                    }
                                ]
                            });
                            wBlank.addItem(formBlank);
                            wBlank.open();
                        }
                    },
                    {
                        text: __translations.cancelButtonWindow
                    }
                ]
            });
            wType.open();

            jQuery(jQuery("#"+wType.id).children()[1]).append(h1);
            wType.addItem(formTypeDynaform);
        }
    });
    form = new PMUI.form.FormPanel({
        fieldset: true,
        width: 300,
        items: [
            {
                pmType: "text",
                label: "Search",
                value: "",
                placeholder: "insert criteria"
            }
        ]
    });

    w = new PMUI.ui.Window({
        title:__translations.dynaformTitleWindow,
        height: 300,
        width: 400,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.addItem(form);
    w.addItem(buttonNew);
    w.open();

};
inputModule = function () {
    event.preventDefault();
    var form = new PMUI.form.Form({
        title: "Input Document Information",
        fieldset: true,
        width: 300,
        items: [
            {
                pmType: "text",
                label: "Title",
                value: ""
            },
            {
                pmType: "dropdown",
                label : 'Document type',
                name : 'documentType',
                required: true,
                helper: "Document type",
                options: [                                
                    {
                       label: "Digital",
                       value: "DIGITAL"
                    },
                    {
                        label: "Printed",
                        value: "PRINTED"
                    },
                    {
                        label: "Digital/Printed",
                        value: "DIGITAL_PRINTED"
                    }
                ]
            },
            {
                pmType: "textarea",
                label: "Description",
                rows: 100
            },
            {
                pmType: "dropdown",
                label : 'Enable Versioning',
                name : 'enableVersioning',
                helper: "Enable versioning",
                options: [                                
                    {
                       label: "No",
                       value: "NO"
                    },
                    {
                        label: "Yes",
                        value: "YES"
                    }
                ]  
            },
            {
                pmType: "text",
                name: "destinationPath",
                label: "Destination Path",
                helper: "Destination Path"
            },
            {
                pmType: "text",
                name: "tags",
                label: "Tags",
                helper: "Tags"
            }
        ]
    }),
    w = new PMUI.ui.Window({
        title:__translations.inputTitleWindow,
        height: 500,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();}
            }
        ],
        buttonsPosition: 'center'
    });
    w.addItem(form);
    w.open();
};
outputModule = function () {
    event.preventDefault();
    var form = new PMUI.form.Form({
        title: "Output Document Information",
        fieldset: true,
        width: 400,
        items: [
            {
                pmType: "text",
                label: "Title",
                required: true
            },
            {
                pmType: "text",
                label: " Filename generated",
                required: true
            },
            {
                pmType: "textarea",
                label: "Description",
                rows: 150
            },
            {
                pmType: "panel",
                layout: 'hbox',
                items: [
                    {
                        pmType: "dropdown",
                        label: "Media / Orientation",
                        options: [                                
                            {
                               label: "Letter",
                               value: "LETTER"
                            },
                            {
                                label: "Legal",
                                value: "LEGAL"
                            },
                            {
                                label: "Executive",
                                value: "EXECUTIVE"
                            }
                        ]
                    },
                    {
                        pmType: "dropdown",
                        labelVisible: false,
                        options: [                                
                            {
                               label: "Portrait",
                               value: "PORTRAIT"
                            },
                            {
                                label: "Landscape",
                                value: "LANDSCAPE"
                            }
                        ]    
                    }
                ]
            },
            {
                pmType: "dropdown",
                label : "Output Document to Generate",
                helper: "Output Document to Generate",
                options: [                                
                    {
                       label: "Both",
                       value: "BOTH"
                    },
                    {
                        label: "Doc",
                        value: "DOC"
                    },
                    {
                        label: "Pdf",
                        value: "PDF"
                    }
                ]
            },
            {
                pmType: "dropdown",
                label : 'PDF security',
                helper: "PDF security",
                options: [                                
                    {
                       label: "Disabled",
                       value: "DISABLED"
                    },
                    {
                        label: "Enabled",
                        value: "ENABLED"
                    }
                ]  
            },
            {
                pmType: "dropdown",
                label : 'Enable versioning',
                helper: "Versioning",
                options: [                                
                    {
                       label: "Yes",
                       value: "Yes"
                    },
                    {
                        label: "No",
                        value: "NO"
                    }
                ]  
            },
            {
                pmType: "text",
                name: "destinationPath",
                label: "Destination Path",
                helper: "Destination Path"
            },
            {
                pmType: "text",
                name: "tags",
                label: "Tags",
                helper: "Tags"
            }
        ]
    }),
    w = new PMUI.ui.Window({
        title:__translations.outputTitleWindow,
        height: 550,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();

                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.addItem(form);
    w.open();
};
triggerModule = function () {
    event.preventDefault();
    var w = new PMUI.ui.Window({
        title:__translations.triggerTitleWindow,
        height: 300,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.open();
};
reportModule = function () {
    event.preventDefault();
    var w = new PMUI.ui.Window({
        title:__translations.reportTitleWindow,
        height: 300,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.open();  
    
};
databaseModule = function () {
    event.preventDefault();
    var w = new PMUI.ui.Window({
        title:__translations.databaseTitleWindow,
        height: 300,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.open();
};
schedulerModule = function () {
    event.preventDefault();
    var w = new PMUI.ui.Window({
        title:__translations.schedulerTitleWindow,
        height: 300,
        buttons: [
            {
                text: __translations.saveButtonWindow
            },
            {
                text: __translations.closeButtonWindow,
                handler: function() {
                    event.preventDefault();
                    w.close();
                }
            }
        ],
        buttonsPosition: 'center'
    });
    w.open();
};

jQuery(function() {
    shapeFactory  = function (type) {
        var customshape = null,
            name = getAutoIncrementName(type);
        switch (type) {
            case 'PMTask':
                customshape = new PMActivity({
                    canvas : canvas,
                    enabledMenu: true,
                    width: 100,
                    height: 50,
                    act_type: 'TASK',
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: -2,
                            y: -2,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-activity-task']
                            }
                        }

                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'Resize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 8,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMSubProcess':
                customshape = new PMActivity({
                    canvas : canvas,
                    enabledMenu: true,
                    width: 120,
                    height: 60,
                    act_type: 'SUB_PROCESS',
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: -2,
                            y: -2,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-activity-subprocess']
                            }
                        }

                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'Resize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 8,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMStartCase':
                customshape = new PMEvent({
                    canvas : canvas,
                    width: 33,
                    height: 33,
                    evn_type: 'start',
                    evn_marker: 'MESSAGE',
                    evn_behavior: 'catch',
                    evn_message: 'LEAD',
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-event-start']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMEndOfProcess':
                customshape = new PMEvent({
                    canvas : canvas,
                    width: 33,
                    height: 33,
                    evn_type: 'end',
                    evn_marker: 'EMPTY',
                    evn_behavior: 'throw',
                    labels: [
                        {
                            message : name,
                            location : "bottom",
                            diffX : 0,
                            diffY : 0
                        }
                    ],
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: [
                                            'pmui-event-end'
                                            ]
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMSelection':
                customshape = new PMGateway({
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    canvas : canvas,
                    enabledMenu: true,
                    width: 45,
                    height: 45,
                    gat_type: 'SELECTION',
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-gateway']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMEvaluation':
                customshape = new PMGateway({
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    canvas : canvas,
                    enabledMenu: true,
                    width: 45,
                    height: 45,
                    gat_type: 'EVALUATION',
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-gateway']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMParallel':
                customshape = new PMGateway({
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    canvas : canvas,
                    enabledMenu: true,
                    width: 45,
                    height: 45,
                    gat_type: 'PARALLEL',
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-gateway']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMParallelByEvaluation':
                customshape = new PMGateway({
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    canvas : canvas,
                    enabledMenu: true,
                    width: 45,
                    height: 45,
                    gat_type: 'PARALLEL_EVALUATION',
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-gateway']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
            case 'PMParallelJoin':
                customshape = new PMGateway({
                    labels: [
                        {
                            message: name,
                            width: 100,
                            position: {
                                location: 'top',
                                diffY: -25
                            }
                        }
                    ],
                    canvas : canvas,
                    enabledMenu: true,
                    width: 45,
                    height: 45,
                    gat_type: 'PARALLEL_JOIN',
                    style: {
                        cssClasses: ['']
                    },
                    layers: [
                        {
                            x: 0,
                            y: 0,
                            layerName : "first-layer",
                            priority: 2,
                            visible: true,
                            style: {
                                cssClasses: ['pm-gateway']
                            }
                        }
                    ],
                    connectAtMiddlePoints: true,
                    resizeBehavior: 'NoResize',
                    resizeHandlers: {
                        type: "Rectangle",
                        total: 4,
                        resizableStyle: {
                            cssProperties: {
                                'background-color': "rgb(0, 255, 0)",
                                'border': '1px solid black'
                            }
                        },
                        nonResizableStyle: {
                            cssProperties: {
                                'background-color': "white",
                                'border': '1px solid black'
                            }
                        }
                    },
                    drop : 'pmconnection'
                });
                break;
        }
        if (customshape) {
            customshape.attachListeners();
            customshape.extendedType = type;
        }
        
        return customshape;
    };

    /***************************************************
     * Defines our Process
     ***************************************************/
    pmApp = new PMProcess({
        id: PMUI.generateUniqueId(),
        name: 'Default Process'
    });
    /***************************************************
     * Defines the Designer buttons 
     ***************************************************/
    dynaformButton = new PMUI.ui.Button({
        text:   __translations.dynaformToolbar,
        handler: dynaformModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-dynaform'
            ]
        }
    });
    dynaformButton.applyStyle
    inputButton = new PMUI.ui.Button({
        text: __translations.inputToolbar,
        handler: inputModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-input'
            ]
        }
    });
    outputButton = new PMUI.ui.Button({
        text: __translations.outputToolbar,
        handler: outputModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-output'
            ]
        }
    });
    triggerButton = new PMUI.ui.Button({
        text: __translations.triggerToolbar,
        handler:  triggerModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-trigger'
            ]
        }
    });
    reportButton = new PMUI.ui.Button({
        text: __translations.reportToolbar,
        handler: reportModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-report'
            ]
        }
    });
    databaseButton = new PMUI.ui.Button({
        text: __translations.databaseToolbar,
        handler: databaseModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-database'
            ]
        }
    });
    schedulerButton = new PMUI.ui.Button({
        text: __translations.schedulerToolbar,
        handler:  schedulerModule,
        style: {
            cssClasses: [
                'pmui-toolbar-sprite',
                'pmui-scheduler'
            ]
        }
    });

    undoButton = new PMUI.ui.Button({
        text: __translations.undoToolbar,
        handler: function() {
            event.preventDefault();
            canvas.commandStack.undo();
        }
    });
    redoButton = new PMUI.ui.Button({
        text: __translations.redoToolbar,
        handler: function() {
            event.preventDefault();
            canvas.commandStack.redo();
        }
    });
    saveButton = new PMUI.ui.Button({
        text: __translations.saveToolbar,
        handler:  function() {
            event.preventDefault();
            pmApp.save();
        }
    });


    /***************************************************
     * Defines our ToolbarPanel 
     ***************************************************/ 
    sidebar = new ToolbarPanel({
        buttons:[
            {
                selector: 'PMTask',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-task'
                ],
                tooltip: __translations.toolbarTask
            },
            {
                selector: 'PMSubProcess',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-subprocess'
                ],
                tooltip:  __translations.toolbarSubprocess
            },
            {
                selector: 'PMStartCase',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-startcase'
                ],
                tooltip:  __translations.toolbarStartCase
            },
            {
                selector: 'PMEndOfProcess',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-endprocess'
                ],
                tooltip:  __translations.toolbarEndProcess
            },
            {
                selector: 'PMSelection',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-selection'
                ],
                tooltip:  __translations.toolbarSelection
            },
            {
                selector: 'PMEvaluation',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-evaluation'
                ],
                tooltip: __translations.toolbarEvaluation
            },
            {
                selector: 'PMParallel',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-parallel'
                ],
                tooltip: "Parallel"
            },
            {
                selector: 'PMParallelByEvaluation',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-parallel-evaluation'
                ],
                tooltip: __translations.toolbarParallelEvalution
            },
            {
                selector: 'PMParallelJoin',
                className: [
                    'pm-toolbar-sprite',
                    'pm-toolbar-parallel-join'
                ],            
                tooltip: __translations.toolbarParallelJoin
            }
        ]
    });

    toolbar = new ToolbarPanel({});
    
    /***************************************************
     * Defines our TreePanel
     ***************************************************/ 
    treepanel = new PMUI.panel.TreePanel({
        collapsed: false,
        height: 300,
        cssProperties: {
            "background-color": "#e5f0f9"
        },
        children: [
            {
                id: "root",
                text: __translations.designerRootTree
            }
        ],
        listeners : {
            click: function(that) {
                console.log('Toggle', that.id);
            },
            select: function(that, shape, e) {
                console.log("Selected, tree:", that, " shape:", shape);
                shapeId = (shape)? shape.id : '';
                shape = PMUI.getActiveCanvas().customShapes.find('id', shapeId);
                if (shape) {
                    shape.canvas.emptyCurrentSelection();
                    shape.canvas.addToSelection(shape);

                    //shape.canvas.project.updatePropertiesGrid(shape);
                }
            }
        }
    });
    

    /***************************************************
     * Defines our LayoutPanel
     ***************************************************/
    layout = new PMUI.panel.LayoutPanel({
        height: jQuery(document).height() - 20,
        west: sidebar,
        westConfig: {
            size: 30,
            overflow: true,
            closable: false,
            resizable: false
        },
        center:{
            cssProperties: {
                padding: 0
            }
        },
        east: {
            cssProperties: {
                "background-color": "#d2e1f2"
            },
            size: 200,
            resizable: false/*,
            closed: true*/
        },
        north: {
            size: 30,
            closable: false,
            resizable: false
        } 
    });

    jQuery('body').html(layout.getHTML());
    layout.render();
    layout.instance.allowOverflow('west');

    centerPosition = jQuery(layout.center.html).offset();

    canvas = new PMCanvas({
        id: PMUI.generateUniqueId(),
        process: pmApp,
        enabledMenu: true,
        absoluteX: centerPosition.left,
        absoluteY: centerPosition.top,
        width: 4000,
        height: 4000,
        drop: {
            pmType: 'container',
            selectors: sidebar.getSelectors()
        },
        container: "regularcontainer",
        tree: treepanel,
        //toolbarFactory: shapeFactory,
        readOnly: false
    });    

    combo = new PMUI.core.Element({elementTag: "select"});
    combo.getHTML();
    zoomOne = document.createElement("option");
    zoomThree = document.createElement("option");
    zoomFive = document.createElement("option");
    zoomOne.text = "0.5X";
    zoomOne.value = 1;
    zoomThree.text = "1X";
    zoomThree.value = 3;
    zoomFive.text = "1.5X";
    zoomFive.value = 5;
    combo.html.options.add(zoomOne);
    combo.html.options.add(zoomThree);
    combo.html.options.add(zoomFive);
    combo.html.options.selectedIndex = 1;
    
    combo.html.addEventListener("change", function (e) {
        var i = (parseInt(this.value, 10) + 1) * 0.25;
        i = i.toString().replace(".", "");
        canvas.applyZoom(this.value);
        $(canvas.getHTML()).removeClass('canvas-1x canvas-05x canvas-15x')
            .addClass('canvas-' + i + 'x');
    });

    layout.center.addItem(canvas);

    /**
     * Processmaker menu
     */
    layout.north.addItem(dynaformButton);
    layout.north.addItem(inputButton);
    layout.north.addItem(outputButton);
    layout.north.addItem(triggerButton);
    layout.north.addItem(reportButton);
    layout.north.addItem(databaseButton);
    layout.north.addItem(schedulerButton);

    layout.north.addItem(redoButton);
    layout.north.addItem(undoButton);
    layout.north.addItem(saveButton);
    layout.north.addItem(combo);
    layout.east.addItem(treepanel);
    canvas.setShapeFactory(shapeFactory);
    canvas.attachListeners();

    PMUI.setActiveCanvas(canvas);
    sidebar.activate();
    layout.defineEvents();
    
    getMenuFactory = function (type) {
        var menu = {};
        switch(type) {
            case 'canvas':
                menu = {
                    items: [
                        {
                            text: "Edit process",
                            icon: "pmui-menu-edit-process"
                        },
                        {
                            text: "Export process",
                            icon: "pmui-menu-export-process"
                        },
                        {
                            text: "Add Task",
                            icon: "pmui-menu-add-task"
                        },
                        {
                            text: "Add Sub-Process",
                            icon: "pmui-menu-sub-process"
                        },
                        {
                            text: "Add text",
                            icon: "pmui-menu-add-text"
                        },
                        {
                            text: "Horizontal line",
                            icon: "pmui-menu-horiz-line"
                        },
                        {
                            text: "Vertical line",
                            icon: "pmui-menu-vertical-line"
                        },
                        {
                            text: "Delete all lines",
                            icon: "pmui-menu-all-lines"
                        },
                        {
                            text: "Process Permissions",
                            icon: "pmui-menu-process-permissions"
                        },
                        {
                            text: "Process Supervisors",
                            icon: "pmui-menu-process-supervisor"
                        },
                        {
                            text: "Web Entry",
                            icon: "pmui-menu-web-entry"
                        },
                        {
                            text: "Case Tracker",
                            icon: "pmui-menu-case-tracker"
                        },
                        {
                            text: "Process Files Manager",
                            icon: "pmui-menu-files-manager"
                        },
                        {
                            text: "Events",
                            icon: "pmui-menu-events"
                        }
                    ]
                };
                break;
            case 'tree':
                menu = {
                    items: [
                        {
                            text: "Delete"
                        },
                        {
                            text: "Disable"
                        },
                        {
                            text: "Enable"
                        }
                    ]
                };
                break;
            case 'PMTask':
                menu = {
                    items: [
                        {
                            text: "Steps",
                            icon: "pmui-menu-task-steps"
                        },
                        {
                            text: "Users & Users Groups",
                            icon: "pmui-menu-task-users"
                        },
                        {
                            text: "Users & Users Groups (Ad hoc)",
                            icon: "pmui-menu-task-users-adhoc"
                        },
                        {
                            text: "Routing rule",
                            icon: "pmui-menu-task-rules"
                        },
                        {
                            text: "Delete Routing rule",
                            icon: "pmui-menu-task-delete-rules"
                        },
                        {
                            text: "Delete task",
                            icon: "pmui-menu-task-delete"
                        },
                        {
                            text: "Properties",
                            icon: "pmui-menu-task-properties"
                        }
                    ],
                    listeners: {
                        clickParent: function (shape, event) {
                            
                            shapeId = (shape)? shape.id : '';
                            shape = PMUI.getActiveCanvas().customShapes.find('id', shapeId);
                            if (shape) {
                                shape.canvas.emptyCurrentSelection();
                                shape.canvas.addToSelection(shape);
                                //shape.canvas.project.updatePropertiesGrid(shape);
                            }
                        },
                        destroy: function (item, event) {
                            event.preventDefault();
                            console.log(item);
                            event.stopPropagation();
                        }
                    }
                };
                break;
            case 'PMSubProcess':
                menu = {
                    items: [
                        {
                            text: "Routing rule"
                        },
                        {
                            text: "Delete Routing rule"
                        },
                        {
                            text: "Delete Sub-Process"
                        },
                        {
                            text: "Properties"
                        }
                    ]
                };
                break;
            case 'PMStartCase':
                menu = {
                    items: [
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMEndOfProcess':
                menu = {
                    items: [
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMSelection':
                menu = {
                    items: [
                        {
                            text: "Properties"
                        },
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMEvaluation':
                menu = {
                    items: [
                        {
                            text: "Properties"
                        },
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMParallel':
                menu = {
                    items: [
                        {
                            text: "Properties"
                        },
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMParallelByEvaluation':
                menu = {
                    items: [
                        {
                            text: "Properties"
                        },
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
            case 'PMParallelJoin':
                menu = {
                    items: [
                        {
                            text: "Properties"
                        },
                        {
                            text: "Delete event"
                        }
                    ]
                };
                break;
        }
        return menu;
    };

    //menu = getMenuFactory("menu");

    // treeItems = treepanel.getItems();
    // for (i = 0; i < treeItems.length; i+=1) {
    //     treeItems[i].setEnabledMenu(true);
    //     treeItems[i].showMenu(menuTreeOptions);
    // };
    menuCanvas = getMenuFactory("canvas");
    canvas.showMenu(menuCanvas);


});