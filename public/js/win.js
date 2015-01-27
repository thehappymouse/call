Ext.apply(Ext.form.VTypes, {
    FileInspection: function (val, field) {
        if (val == '') {
            return;
        }
        val = val.slice(-3);
        val = val.toLocaleLowerCase();

        if (val == 'jpg' || val == 'png' || val == 'bmp' || val == 'gif') {
            return true;
        } else {
            Ext.MessageBox.alert('提示', '只能上传jpg、png、bmp、gif格式文件');
            return false;
        }
    },
    FileInspectionText: '格式错误'
})

var arrarsFreeFields = [
    'ID', 'PressTime',
    'UserName', 'UserName',
    'PressStyle', 'Phone',
    'Photo', 'CutUserName',
    'CutTime', 'CutStyle', 'SegUser',
    'ResetPhone', 'ResetTime',
    'IsClean', 'IsCut',
    'CutCount', 'Money',
    'YearMonth', 'Number',
    'PressCount'
];

freeStore = new Ext.data.JsonStore({
    url: '/ams/PopPage/userInfo',
    root: 'data',
    fields: arrarsFreeFields
});
sm = new Ext.grid.CheckboxSelectionModel()
freeView = new Ext.grid.GridPanel(
    {
        store: freeStore,
        multiSelect: true,
        stripeRows: true,
        region: 'center',
        title: '用户信息',
        sm: sm,
        autoHeight: true,
        maxHeight: 100,
        viewConfig: {forceFit: true},
        emptyText: '没有数据',
        reserveScrollOffset: true,
        tbar: [
            {
                text: '录入催费信息',
                ref: '../add',
                hidden: show_cuifei_btn == 1 ? false : true,
                iconCls: 'add'
            }
        ],
        columns: [
            sm,
            {
                header: '电费年月',
                width: 160,
                sortable: true,
                align: "center",
                dataIndex: 'YearMonth'
            }, {
                header: '电费金额',
                dataIndex: 'Money',
                width: 120,
                align: "center",
                sortable: true
            }, {
                header: '结清标志',
                sortable: true,
                align: "center",
                dataIndex: 'IsClean',
                renderer: function (v) {
                    if (v == 1) {
                        return '已结清';
                    } else if (v == 0) {
                        return "<a style='color:#804000'>未结清</a>";
                    } else {
                        return '预收转逾期';
                    }
                }
            }, {
                header: '停电标志',
                sortable: true,
                align: "center",
                dataIndex: 'IsCut',
                renderer: function (v) {
                    if (v == 1) {
                        return "<a style='color:blue'>已停电</a>";
                    } else {
                        return "未停电";
                    }
                }
            }/*, {
             header: '停电次数',
             sortable: true,
             align:"center",
             dataIndex: 'CutCount'
             }*/, {
                header: '催费次数',
                sortable: true,
                align: "center",
                dataIndex: 'PressCount'
            }]
    });
freeView.on("render", function (p, e) {

    p.add.setHandler(function (b, e) {
        // var number = freeView.getSelectionModel().getSelections()[0].data.ID;
        var ids = [];
        Ext.each(freeView.getSelectionModel().getSelections(), function (node) {
            ids.push(node.get("ID"));
        });
        var idString = ids.join(",");
        if (idString.length == 0) {
            Ext.Msg.alert('提示', '请选择电费年月！');
            return;
        }
        submitTabPanel.setActiveTab(0);
        formWin.show();
        Ext.getCmp('submit1ID').setValue(idString);
        Ext.getCmp('submit2ID').setValue(idString);
    })
})

arrarsPanel = new Ext.FormPanel({
    labelWidth: 80,
    title: '用户详细信息',
    labelAlign: 'right',
    url: '/ams/Customer/update',
    defaults: {
        border: false
    },
    style: 'margin-top:10px',
    layout: 'form',
    items: [
        {
            layout: 'column',
            items: [
                {
                    columnWidth: .25,
                    layout: 'form',
                    defaults: {
                        xtype: 'textfield'
                    },
                    items: [
                        {
                            fieldLabel: '管理班组',
                            name: 'Team',
                            readOnly: true,
//                            disabled: true,
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '用电地址',
                            name: 'Address',
                            anchor: '95%'
                        },
                        new QuietCombox({
                            fieldLabel: '特殊客户',
                            displayField: 'Text',
                            valueField: 'IsSpecial',
                            hiddenName: 'IsSpecial',
                            fields: ['Text', 'IsSpecial'],
                            data: [
                                ['是', '1'],
                                ['否', '0']
                            ],
                            anchor: '95%'
                        }),
                        new QuietCombox({
                            fieldLabel: '是否租房',
                            displayField: 'Text',
                            valueField: 'IsRent',
                            hiddenName: 'IsRent',
                            fields: ['Text', 'IsRent'],
                            data: [
                                ['是', '1'],
                                ['否', '0']
                            ],
                            anchor: '95%'
                        })
                    ]
                },
                {
                    columnWidth: .25,
                    layout: 'form',
                    defaults: {
                        xtype: 'textfield'
                    },
                    items: [
                        {
                            fieldLabel: '抄表员',
                            name: 'SegUser',
                            readOnly: true,
                            anchor: '95%'
                        },
                        new QuietCombox({
                            fieldLabel: '停电标志',
                            displayField: 'Text',
                            valueField: 'IsCut',
                            hiddenName: 'IsCut',
                            fields: ['Text', 'IsCut'],
                            data: [
                                ['是', '1'],
                                ['否', '0']
                            ],
                            anchor: '95%'
                        }),
                        {
                            fieldLabel: '抄表段编号',
                            name: 'Segment',
                            readOnly: true,
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '房东电话',
                            name: 'LandlordPhone',
                            id: 'LandlordPhone',
                            anchor: '95%'
                        }
                    ]
                },
                {
                    columnWidth: .25,
                    layout: 'form',
                    defaults: {
                        xtype: 'textfield'
                    },
                    items: [
                        {
                            xtype: 'hidden',
                            name: 'ID'
                        },
                        {
                            fieldLabel: '用户编号',
                            name: 'Number',
                            readOnly: true,
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '电表资产号',
                            name: 'AssetNumber',
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '停电方式',
                            name: 'CutStyle',
                            disabled: true,
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '房客电话',
                            name: 'RenterPhone',
                            id: 'RenterPhone',
                            anchor: '95%'
                        }
                    ]
                },
                {
                    columnWidth: .25,
                    layout: 'form',
                    defaults: {
                        xtype: 'textfield'
                    },
                    items: [
                        {
                            fieldLabel: '用户名称',
                            readOnly: true,
                            name: 'Name',
                            anchor: '95%'
                        },
                        new QuietCombox({
                            fieldLabel: '签订费控协议',
                            displayField: 'Text',
                            hiddenName: 'IsControl',
                            valueField: 'IsControl',
                            fields: ['Text', 'IsControl'],
                            data: [
                                ['是', '1'],
                                ['否', '0']
                            ],
                            anchor: '95%'
                        }),
                        {
                            fieldLabel: '累计欠费次数',
                            name: 'ArrearsCount',
                            disabled: true,
                            anchor: '95%'
                        },
                        {
                            fieldLabel: '欠费原因',
                            name: 'Cause',
                            anchor: '95%'
                        }
                    ]
                }
            ]
        },
        {
            xtype: 'compositefield',
            items: [
                new Ext.form.TextArea({
                    name: 'Desc',
                    width: 380,
                    height: 50,
                    fieldLabel: '备注'
                }), {
                    width: 90,
                    xtype: 'button',
                    text: '修改',
                    handler: function () {
                        arrarsPanel.getForm().submit({
                            method: 'post',
                            params: arrarsPanel.getForm().getValues(),
                            success: function (v, action) {
                                var text = Ext.decode(action.response.responseText);
                                Ext.MessageBox.alert('提示', text.msg);
                            },
                            failure: function (v, action) {
                                try {
                                    var text = Ext.decode(action.response.responseText);
                                    Ext.MessageBox.alert('提示', text.msg);
                                } catch (err) {
                                    Ext.MessageBox.alert('提示', '服务器异常');
                                }
                            }
                        })
                    }
                }]
        }

    ]
});

cuifeiView = new Ext.grid.GridPanel({
    store: new Ext.data.JsonStore({
        url: '/ams/PopPage/ReminderFee',
        root: 'data',
        fields: arrarsFreeFields
    }),
    multiSelect: true,
    stripeRows: true,
    region: 'center',
    title: '催费详情',
    height: 300,
    viewConfig: {forceFit: true},
    emptyText: '没有数据',
    reserveScrollOffset: true,
    columns: [
        {
            header: '催费时间',
            width: 160,
            sortable: true,
            align: "center",
            dataIndex: 'PressTime'
        },
        {
            header: '抄表员',
            dataIndex: 'SegUser',
            width: 120,
            align: "center",
            sortable: true
        },
        {
            header: '催费方式',
            sortable: true,
            align: "center",
            dataIndex: 'PressStyle'
        },
        {
            header: '催费电话',
            sortable: true,
            align: "center",
            dataIndex: 'Phone'
        },
        {
            header: '催费照片',
            sortable: true,
            align: "center",
            dataIndex: 'Photo',
            renderer: function (v) {
                if (v != "") {
                    return "<a href='#' >查看</a>";
                }

            }
        }
    ]
});
cuifeiView.on('cellclick', function (grid, rowIndex, columnIndex, e) {
    var record = grid.getStore().getAt(rowIndex);
    var columnName = grid.getColumnModel().getDataIndex(columnIndex);
    if (columnName == 'Photo') {
        lookImg(record.data.Photo, 1);
    }
})
lookImg = function (v) {
    var tpl = "<div></div><img width='600'  src='/ams" + v + "' /></div>";

    var imgWin = new Ext.Window({
        title: '催费照片',
        autoHeight: true,
        width: 600,
        closeAction: 'hide',
        // plain: true,
        autoScroll: true,
        items: [
            {
                html: tpl
            }
        ],
        buttons: [
            {
                text: '关闭',
                handler: function () {
                    imgWin.hide();
                }
            }
        ]
    });
    imgWin.show();
}

tingdianView = new Ext.grid.GridPanel({
    store: new Ext.data.JsonStore({
        url: '/ams/PopPage/PowerCut',
        root: 'data',
        fields: arrarsFreeFields
    }),
    multiSelect: true,
    stripeRows: true,
    region: 'center',
    title: '停电详情',
    height: 300,
    viewConfig: {forceFit: true},
    emptyText: '没有数据',
    reserveScrollOffset: true,
    columns: [
        {
            header: '停电时间',
            width: 160,
            sortable: true,
            align: "center",
            dataIndex: 'CutTime'
        },
        {
            header: '抄表员',
            dataIndex: 'CutUserName',
            width: 120,
            align: "center",
            sortable: true
        },
        {
            header: '停电方式',
            sortable: true,
            align: "center",
            dataIndex: 'CutStyle'
        },
        {
            header: '复电时间',
            sortable: true,
            align: "center",
            dataIndex: 'ResetTime'
        },
        {
            header: '复电电话',
            sortable: true,
            align: "center",
            dataIndex: 'ResetPhone'
        }
    ]
});

freeTab = new Ext.TabPanel({
    labelWidth: 85,
    activeTab: 0,
    plain: true,
    style: 'margin-top:10px',
    defaults: {autoScroll: true},
    items: [
        freeView
    ]
});

arrarsTab = new Ext.TabPanel({
    labelWidth: 85,
    activeTab: 0,
    height: 200,
    plain: true,
    defaults: {autoScroll: true},
    items: [
        arrarsPanel, cuifeiView, tingdianView
    ]
});

winPanels = new Ext.Panel({
    region: 'center',
    fileUpload: true,
    autoScroll: true,
    bodyStyle: "padding:10px;",
    items: [
        arrarsTab, freeTab
    ]
});
arrarswin = new Ext.Window({
    layout: 'fit',
    width: 800,
    title: '用户详细信息',
    height: 400,
    closeAction: 'hide',
    plain: true,
    items: [winPanels],
    buttons: [
        {
            text: '关闭',
            handler: function () {
                arrarswin.hide();
            }
        }
    ]
});

submit1Form = new Ext.FormPanel({
    labelWidth: 85,
    defaults: {
        border: false
    },
    url: '/ams/reminder/Press',
    title: '通知单催费',
    style: 'margin-top:6px;margin-left:20px',
    labelAlign: 'right',
    layout: 'form',
    fileUpload: true,
    items: [
        {
            xtype: 'textfield',
            allowBlank: false,
            fieldLabel: '通知单催费',
            inputType: 'file',
            name: 'fileName',
            vtype: 'FileInspection'
        },
        {
            xtype: 'hidden',
            id: 'submit1ID',
            name: 'ID'
        },
        {
            fieldLabel: '催费时间',
            xtype: 'datetimefield',
            format: 'Y-m-d H:i:s',
            name: 'PressTime',
            value: new Date().format('Y-m-d H:i:s'),
            anchor: '65%'
        },
        {
            xtype: 'compositefield',
            items: [
                {xtype: 'hidden'},
                {
                    width: 60,
                    xtype: 'button',
                    text: '提交',
                    handler: function () {
                        if (!submit1Form.getForm().isValid()) {
                            Ext.MessageBox.alert('提示', '请正确填写表单');
                            return;
                        }
                        submit1Form.getForm().submit({
                            success: function (v, action) {
                                var text = Ext.decode(action.response.responseText);
                                Ext.MessageBox.alert('提示', '信息录入成功');
                                freeView.store.reload();
                                cuifeiView.store.reload();
                                tingdianView.store.reload();
                                formWin.hide();
                            },
                            failure: function (v, action) {
                                try {
                                    var text = Ext.decode(action.response.responseText);
                                    Ext.MessageBox.alert('提示', text.msg);
                                } catch (err) {
                                    Ext.MessageBox.alert('提示', '服务器异常');
                                }
                            }
                        })
                    }
                }
            ]
        }
    ]
})

submit2Form = new Ext.FormPanel({
    labelWidth: 85,
    defaults: {
        border: false
    },
    url: '/ams/reminder/Press',
    title: '电话催费',
    style: 'margin-top:6px;margin-left:20px',
    labelAlign: 'right',
    layout: 'form',
    items: [
        {
            xtype: 'compositefield',
            anchor: '95%',
            items: [
                new QuietCombox({
                    fieldLabel: '催费电话',
                    displayField: 'Text',
                    valueField: 'PhoneType',
                    hiddenName: 'PhoneType',
                    id: 'PhoneType',
                    fields: ['Text', 'PhoneType'],
                    data: [
                        ['房东电话', '房东电话'],
                        ['房客电话', '房客电话']
                    ],
                    width: 150,
                    listeners: {
                        'select': function () {
                            var type = Ext.getCmp('PhoneType').getValue();
                            var phone = "";
                            if (type == "房东电话") {
                                phone = Ext.getCmp('LandlordPhone').getValue();
                            } else {
                                phone = Ext.getCmp('RenterPhone').getValue();
                            }
                            Ext.getCmp('Phone').setValue(phone);
                        }
                    }
                }), {
                    xtype: 'textfield',
                    id: 'Phone',
                    allowBlank: false,
                    name: 'Phone',
                    width: 100
                }]
        },
        {
            fieldLabel: '催费时间',
            xtype: 'datetimefield',
            format: 'Y-m-d H:i:s',
            name: 'PressTime',
            value: new Date().format('Y-m-d H:i:s'),
            // allowBlank:false,
            anchor: '65%'
        },
        {
            xtype: 'hidden',
            id: 'submit2ID',
            name: 'ID'
        },
        {
            xtype: 'compositefield',
            items: [
                {xtype: 'hidden'},
                {
                    width: 60,
                    xtype: 'button',
                    text: '提交',
                    handler: function () {
                        if (!submit2Form.getForm().isValid()) {
                            Ext.MessageBox.alert('提示', '请正确填写表单');
                            return;
                        }
                        submit2Form.getForm().submit({
                            success: function (v, action) {
                                var text = Ext.decode(action.response.responseText);
                                Ext.MessageBox.alert('提示', '信息录入成功');
                                freeView.store.reload();
                                cuifeiView.store.reload();
                                tingdianView.store.reload();
                                formWin.hide();

                                var store = Ext.StoreMgr.get("cuifeijilvStore");
                                if (store) {
                                    Ext.StoreMgr.get("cuifeijilvStore").reload();
                                }
                            },
                            failure: function (v, action) {
                                try {
                                    var text = Ext.decode(action.response.responseText);
                                    Ext.MessageBox.alert('提示', text.msg);
                                } catch (err) {
                                    Ext.MessageBox.alert('提示', '服务器异常');
                                }
                            }
                        })
                    }
                }
            ]
        }
    ]
})

freeView.getSelectionModel().on("selectionchange", function (sm) {
    var type = Ext.getCmp('PhoneType').getValue();

    var phone = "";
    if (type == "房东电话") {
        phone = Ext.getCmp('LandlordPhone').getValue();
    } else {
        phone = Ext.getCmp('RenterPhone').getValue();
    }
    Ext.getCmp('Phone').setValue(phone);
})

submitTabPanel = new Ext.TabPanel({
    labelWidth: 85,
    activeTab: 0,
    height: 120,
    plain: true,
    defaults: {autoScroll: true},
    items: [
        submit1Form,
        submit2Form
    ]
});


submitPanel = new Ext.Panel({
    region: 'center',
    fileUpload: true,
    autoScroll: true,
    items: [
        submitTabPanel
    ]
});

formWin = new Ext.Window({
    layout: 'fit',
    width: 400,
    height: 200,
    closeAction: 'hide',
    plain: true,
    items: [submitPanel],
    buttons: [
        {
            text: '关闭',
            handler: function () {
                formWin.hide();
            }
        }
    ]
})

lookInfo = function (v, active) {
    if (freeView.store.data.length != 0) {
        freeView.store.removeAll();
    }
    freeView.store.load({
        params: {Number: v },
        add: false
    });
    if (arrarsPanel.store != undefined) {
        arrarsPanel.store.removeAll();
    }
    arrarsPanel.getForm().load({
        url: '/ams/PopPage/Info?Number=' + v
    });

    if (cuifeiView.store.data.length != 0) {
        cuifeiView.store.removeAll();
    }
    cuifeiView.store.load({
        params: {Number: v },
        add: false
    });

    if (tingdianView.store.data.length != 0) {
        tingdianView.store.removeAll();
    }
    tingdianView.store.load({
        params: {Number: v },
        add: false
    })
    arrarswin.show();
    arrarsTab.setActiveTab(active);
}