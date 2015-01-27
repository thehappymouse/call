<script>
var startdate = "201401";
Ext.onReady(function () {
    _pageSize = 50;
    /*三个动态框*/
    var teamCombox = new SettCombbox({
        url: '/ams/Info/TeamList?ID=1&Type=1',
        fieldLabel: '管理班组',
        anchor: '95%',
        id: 'team',
        hiddenName: 'Team',
        autoLoad: true
    });
    var peopleCombox = new SettCombbox({
        url: '/ams/Info/UserList',
        fieldLabel: '抄表员',
        anchor: '95%',
        hiddenName: 'Name',
        id: 'people'
    });
    var NumberCombox = new SettCombbox({
        url: '/ams/Info/SegmentList',
        fieldLabel: '抄表段编号',
        anchor: '95%',
        hiddenName: 'Number',
        id: 'number'
    });

    //监听store加载
    function comboxLoad(aCombox, bCombox, url) {
        aCombox.store.on('load', function () {
            bCombox.store.load({
                url: url,
                params: {
                    ID: aCombox.store.getAt(0).data.ID
                }
            });
        });
    }

    //监听combox选择事件
    function comSelect(aCombox, bCombox, url) {
        aCombox.on("select", function (combo) {
            bCombox.reset();
            bCombox.store.load({
                url: url,
                params: {
                    ID: combo.value
                }
            });
        })
    }

    comboxLoad(teamCombox, peopleCombox, "/ams/Info/UserList");
    comboxLoad(peopleCombox, NumberCombox, "/ams/Info/SegmentList");
    comSelect(teamCombox, peopleCombox, "/ams/Info/UserList");
    comSelect(peopleCombox, NumberCombox, "/ams/Info/SegmentList");

    var infoSearch = new Ext.FormPanel({
        title: '查询条件',
        defaults: {
            border: false
        },
        labelAlign: 'right',
        url: '/ams/import/arrearslist',
        style: 'padding-top:10px',
        layout: 'column',
        items: [
            {
                columnWidth: .25,
                layout: 'form',
                items: [teamCombox,
                    {
                        fieldLabel: '用户编号',
                        xtype: 'textfield',
                        name: 'CustomerNumber',
                        anchor: '95%'
                    }
                ]
            },
            {
                columnWidth: .25,
                layout: 'form',
                items: [peopleCombox,
                    {
                        xtype: 'compositefield',
                        fieldLabel: '电费年月',
                        items: [
                            new Ext.form.DateField({
                                plugins: 'monthPickerPlugin',
                                name: 'FromData',
                                width: 82,
                                value: new Date().format('Ym') - 1,
                                format: 'Ym'
                            }), {
                                xtype: 'displayfield',
                                value: '至: '
                            }, new Ext.form.DateField({
                                plugins: 'monthPickerPlugin',
                                name: 'ToData',
                                width: 82,
                                value: new Date().format('Ym'),
                                format: 'Ym'
                            })]
                    }
                ]
            },
            {
                columnWidth: .25,
                layout: 'form',
                items: [NumberCombox,
                    {
                        xtype: 'compositefield',
                        items: [
                            {xtype: 'hidden'},
                            {
                                width: 100,
                                xtype: 'button',
                                text: '查询',
                                handler: function () {
                                    infoSearch.getForm().submit({
                                        method: 'GET',
                                        params: {
                                            start: 0,
                                            limit: _pageSize
                                        },
                                        success: function (action, msg) {
                                            var text = Ext.decode(msg.response.responseText);
                                            if (text.success) {
                                                listView.store.loadData(text);
                                                Ext.apply(listView.store.baseParams, infoSearch.getForm().getValues());
                                            }
                                        },
                                        failure: function (action, msg) {
                                            var text = Ext.decode(msg.response.responseText);
                                            if (!text.success) {
                                                listView.store.removeAll();
                                                Ext.getCmp('bbar').updateInfo();
                                                Ext.Msg.alert('提示', text.msg);
                                            }
                                        }
                                    })
                                }
                            }
                        ]
                    }]
            }
        ]
    });
    var searchTab = new Ext.TabPanel({
        labelWidth: 85,
        activeTab: 0,
        height: 100,
        plain: true,
        defaults: {autoScroll: true},
        items: [
            infoSearch
        ]
    });

    var store = new Ext.data.JsonStore(
        {
            url: '/ams/import/arrearslist',
            root: 'data',
            totalProperty: 'total',
            baseParams: {
                start: 0,
                limit: _pageSize
            },
            fields: ArrarsinfoFields
        });

    var sm = new Ext.grid.CheckboxSelectionModel({singleSelect: false});
    var listView = new Ext.grid.GridPanel(
        {
            store: store,
            title: '欠费信息',
            stripeRows: true,
            region: 'center',
            sm: sm,
            autoHeight: true,
            emptyText: '没有数据',
            viewConfig: {forceFit: true},
            reserveScrollOffset: true,
            tbar: [
                {
                    text: '修改数据',
                    ref: '../del'
                }
            ],
            columns: [
                sm,
                {
                    header: '抄表段编号',
                    align: "center",
                    sortable: true,
                    dataIndex: 'Segment'
                }, {
                    header: '抄表员',
                    align: "center",
                    sortable: true,
                    dataIndex: 'SegUser'
                }, {
                    header: '用户编号',
                    dataIndex: 'CustomerNumber',
                    align: "center",
                    sortable: true
                }, {
                    header: '用户名称',
                    sortable: true,
                    width: 130,
                    align: "center",
                    dataIndex: 'CustomerName'
                }, {
                    header: '用电地址',
                    width: 200,
                    sortable: true,
                    align: "center",
                    dataIndex: 'Address'
                }, {
                    header: '电费年月',
                    dataIndex: 'YearMonth',
                    sortable: true,
                    align: "center"
                }, {
                    header: '电费金额',
                    sortable: true,
                    align: "center",
                    dataIndex: 'Money'
                }],
            bbar: new Ext.PagingToolbar({
                pageSize: _pageSize,
                store: store,
                id: 'bbar',
                displayMsg: "显示{0}条到{1}条记录，总共{2}条记录",
                emptyMsg: "没有数据记录",
                displayInfo: true
            })
        });

    listView.on("render", function (p, e) {

        p.del.setHandler(function (b, e) {
            if (p.getSelectionModel().getSelections().length == 0) {
                Ext.Msg.alert('提示', '请选择记录！');
                return;
            }
            var rds = p.getSelectionModel().getSelections();
            if (rds.length == 1) {
                var rd = p.getSelectionModel().getSelections()[0];

                win.show();
                Ext.getCmp("formpanel").getForm().loadRecord(rd);
            }
            else {
                var ids = [];
                Ext.each(rds, function(r){
                    ids.push(r.get("ID"));
                })
                var rd = {data:{"IDS":ids.join(",")}};
                winMini.show();
                winMini.items.itemAt(0).getForm().loadRecord(rd);
            }
        })
    });

    var winMini = new Ext.Window({
        layout: 'fit',
        width: 360,
        height: 200,
        closeAction: 'hide',
        title: '修改数据',
        bodyStyle: 'padding-top:30px;background:white;',
        items: new Ext.FormPanel({
            labelAlign: 'right',
            url: '/ams/import/updatemoney',
            items: [
                {
                    xtype: 'textfield',
                    fieldLabel: '抄表员',
                    name: 'SegUser'
                },
                new Ext.form.DateField({
                    plugins: 'monthPickerPlugin',
                    name: 'YearMonth',
                    fieldLabel: '电费年月',
                    width: 82,
                    format: 'Ym'
                }),
                {
                    xtype: 'numberfield',
                    minValue: 0,
                    fieldLabel: '电费金额',
                    name: 'Money'
                },
                {
                    xtype: 'hidden',
                    name: "IDS",
                    id: 'arrearIDS'
                }
            ]
        }),
        buttons: [
            {
                text: '提交',
                handler: function (b, e) {

                    var fp = b.findParentByType("window").items.itemAt(0);
                    if (fp.getForm().isValid()) {
                        fp.getForm().submit({
                            method: 'GET',
                            success: function (action, msg) {
                                var text = Ext.decode(msg.response.responseText);
                                if (text.success) {

                                    Ext.Msg.alert('提示', text.msg);
                                    b.findParentByType("window").hide();
                                    listView.store.removeAll();
                                    listView.store.reload();
                                }
                            },
                            failure: function (action, msg) {
                                var text = Ext.decode(msg.response.responseText);
                                if (!text.success) {
                                    listView.store.removeAll();
                                    Ext.Msg.alert('提示', text.msg);
                                }
                            }
                        })
                    }
                }
            },
            {
                text: '关闭',
                handler: function (b,e) {
                    b.findParentByType("window").hide();
                }
            }
        ]
    });

    var win = new Ext.Window({
        layout: 'fit',
        width: 360,
        height: 300,
        closeAction: 'hide',
        title: '修改数据',
        bodyStyle: 'padding-top:30px;background:white;',
        items: new Ext.FormPanel({
            labelAlign: 'right',
            id: 'formpanel',
            url: '/ams/import/updatemoney',
            items: [
                {
                    xtype: 'textfield',
                    fieldLabel: '抄表段编号',
                    name: 'Segment'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: '抄表员',
                    name: 'SegUser'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: '用户名称',
                    name: 'CustomerName'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: '用电地址',
                    name: 'Address',
                    width: 180
                },
                new Ext.form.DateField({
                    plugins: 'monthPickerPlugin',
                    name: 'YearMonth',
                    fieldLabel: '电费年月',
                    width: 82,
                    format: 'Ym'
                }),
                {
                    xtype: 'numberfield',
                    minValue: 0,
                    fieldLabel: '电费金额',
                    name: 'Money'
                },
                {
                    xtype: 'hidden',
                    name: 'ID'
                }
            ]
        }),
        buttons: [
            {
                text: '提交',
                handler: function () {
                    var fp = win.items.itemAt(0);
                    if (fp.getForm().isValid()) {
                        fp.getForm().submit({
                            method: 'GET',
                            success: function (action, msg) {
                                var text = Ext.decode(msg.response.responseText);
                                if (text.success) {

                                    Ext.Msg.alert('提示', text.msg);
                                    win.hide();
                                    listView.store.reload();
                                }
                            },
                            failure: function (action, msg) {
                                var text = Ext.decode(msg.response.responseText);
                                if (!text.success) {
                                    listView.store.removeAll();
                                    Ext.Msg.alert('提示', text.msg);
                                }
                            }
                        })
                    }
                }
            },
            {
                text: '关闭',
                handler: function () {
                    win.hide();
                }
            }
        ]
    });


    var infoPanel = new Ext.TabPanel({
        labelWidth: 85,
        activeTab: 0,
        plain: true,
        style: 'margin-top:15px',
        defaults: {autoScroll: true},
        items: [
            listView
        ]
    });


    var formpanels = new Ext.Panel({
        region: 'center',
        fileUpload: true,
        autoScroll: true,
        bodyStyle: "padding:20px;",
        items: [
            searchTab,
            infoPanel
        ]
    });


    new Ext.Viewport(
        {
            layout: 'border',
            items: [
                formpanels
            ]
        });
})
</script>