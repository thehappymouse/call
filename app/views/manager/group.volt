
<script type="text/javascript">

//    var typeBox = [
//        {
//            "boxLabel": "101(呼叫机)",
//            "inputValue": "1",
//            "name": "1"
//        },
//        {
//            "boxLabel": "201(应答机)",
//            "inputValue": "2",
//            "name": "2"
//        },
//        {
//            "boxLabel": "202(应答机)",
//            "inputValue": "3",
//            "name": "3"
//        },
//        {
//            "boxLabel": "203(应答机)",
//            "inputValue": "4",
//            "name": "4"
//        }
//    ];

var typeBox = {{boxdata}};

    Ext.onReady(function () {

        del = function (value) {
            Ext.Msg.confirm('提示', '是否删除此 一级菜单', function (btn) {
                if (btn == 'no') {
                    return;
                } else {
                    Ext.Ajax.request({
                        url: '../manager/GroupDel',
                        params: {ID: value},
                        success: function (v) {
                            try {
                                var r = Ext.util.JSON.decode(v.responseText);
                                if (r.success) {
                                    listView.store.removeAll();
                                    listView.store.reload();
                                }
                                else {
                                    Ext.MessageBox.alert('提示', r.msg);
                                }
                            }
                            catch (e) {

                            }
                        }
                    })
                }
            })
        }

        modify = function (value) {
            modifyForm.getForm().load({
                url: '../Poppage/ModifyUser',
                params: {ID: value}
            })
            win.show();
        }

        var modifyForm = new Ext.FormPanel({
            labelWidth: 85,
            defaults: {
                border: false
            },
            url: '../manager/GroupBuildAjax',
            labelAlign: 'right',
            layout: 'form',

            bodyStyle: 'padding-top:10px;',
            items: [
                {
                    xtype: 'hidden',
                    name: 'ID'
                },
                {
                    fieldLabel: '二级菜单名',
                    xtype: 'textfield',

                    name: 'Name',
                    width: 151
                },
                {
                    // Use the default, automatic layout to distribute the controls evenly
                    // across a single row
                    allowBlank: false,
                    frame: true,
                    xtype: 'checkboxgroup',
                    fieldLabel: '关联设备',
                    columns: 3,
                    name: 'HotelProgram[Types]',
                    items: typeBox
                }
            ]
        })

        var win = new Ext.Window({
            layout: 'fit',
            width: 450,
            height: 200,
            closeAction: 'hide',
            // plain: true,
            title: '菜单修改',
            items: [modifyForm],
            buttons: [
                {
                    text: '确认',
                    handler: function () {

                        modifyForm.getForm().submit({
                            success: function (v, action) {
                                var re = Ext.decode(action.response.responseText);
                                if (re.success) {
                                    Ext.MessageBox.alert('提示', '修改成功');
                                    listView.store.reload();
                                    win.hide();
                                }
                            },
                            failure: function () {
                                Ext.MessageBox.alert('提示', '修改失败');
                            }
                        })

                    }
                },
                {
                    text: '取消',
                    handler: function () {
                        modifyForm.getForm().reset();
                        win.hide();
                    }
                }
            ]
        });

        var store = new Ext.data.JsonStore(
            {
                url: '../Manager/GroupList',
                root: 'data',
                autoLoad: true,
                totalProperty: 'total',
                baseParams: {
                    start: 0,
                    limit: 20
                },
                fields: ['ID', 'Name', 'TypeName', 'Type']
            });

        var listView = new Ext.grid.EditorGridPanel(
            {
                store: store,
                multiSelect: true,
                title: '一级菜单管理',
                stripeRows: true,
                region: 'center',
                autoHeight: true,
                emptyText: '没有数据',
                viewConfig: {forceFit: true},
                reserveScrollOffset: true,
                columns: [
                    {
                        header: '一级菜单名称',
                        align: "center",
                        sortable: true,
                        dataIndex: 'Name'
                    },
                    {
                        header: '操作',
                        align: "center",
                        dataIndex: 'ID',
                        renderer: function (value, cellmeta) {
                            return "<a href='#' onclick=modify('" + value + "')>修改</a>&nbsp;&nbsp;<a href='#' onclick=del('" + value + "')>删除</a>"

                        }
                    }
                ],
                bbar: new Ext.PagingToolbar({
                    pageSize: 20,
                    store: store,
                    displayMsg: "显示{0}条到{1}条记录，总共{2}条记录",
                    emptyMsg: "没有数据记录",
                    displayInfo: true
                })
            });

        var infoPanel = new Ext.TabPanel({
            labelWidth: 85,
            activeTab: 0,
            plain: true,
            defaults: {autoScroll: true},
            items: [
                listView
            ]
        });

        var submitForm = new Ext.FormPanel({
            labelWidth: 85,
            defaults: {
                border: false
            },
            url: '../manager/GroupBuildAjax',
            title: '班组一级菜单',
            style: 'margin-top:6px;margin-left:20px',
            labelAlign: 'right',
            layout: 'form',
            items: [
                {
                    fieldLabel: '菜单名称',
                    xtype: 'textfield',
                    name: 'Name',
                    allowBlank: false,
                    width: 151
                },
                {
                    // Use the default, automatic layout to distribute the controls evenly
                    // across a single row
                    allowBlank: false,
                    frame: true,
                    xtype: 'checkboxgroup',
                    fieldLabel: '关联设备',
                    columns: 3,
                    name: 'HotelProgram[Types]',
                    items: typeBox
                },
                {
                    style: 'margin-left:90px',
                    width: 100,
                    xtype: 'button',
                    text: '创建',
                    handler: function () {
                        if (!submitForm.getForm().isValid()) {
                            Ext.MessageBox.alert('提示', '请正确填写表单');
                            return;
                        }
                        submitForm.getForm().submit({
                            success: function (v, action) {
                                var re = Ext.decode(action.response.responseText);
                                if (re.success) {
                                    Ext.MessageBox.alert('提示', '创建成功');
                                    listView.store.reload();
                                }
                            },
                            failure: function (v, action) {
                                Ext.MessageBox.alert('提示', '创建失败');
                            }
                        })
                    }
                }
            ]
        })

        var submmitTab = new Ext.TabPanel({
            labelWidth: 85,
            activeTab: 0,
            height: 165,
            plain: true,
            style: 'margin-top:15px',
            defaults: {autoScroll: true},
            items: [
                submitForm
            ]
        });
        var formpanels = new Ext.Panel({
            region: 'center',
            fileUpload: true,
            autoScroll: true,
            bodyStyle: "padding:20px;",
            items: [
                infoPanel,
                submmitTab
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