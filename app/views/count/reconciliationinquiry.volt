<script>
    Ext.onReady(function () {
        var msgTip = new Ext.LoadMask(Ext.getBody(), {
            msg: 'Excel正在生成中',
            removeMask: true
        });

        var _pageSize = 30;
        var store = new Ext.data.JsonStore(
            {
                url: 'charges',
                root: 'data',
                autoLoad: true,
                baseParams: {
                    start: 0,
                    limit: _pageSize
                },
                fields: ['Count', 'Yearly', 'Dates', 'RequestTime', 'ResponseTime', 'ReceiveTime', 'ItemName']
            });

        var listView = new Ext.grid.EditorGridPanel({
            store: store,
            multiSelect: true,
            title: '超时数据处理',
            stripeRows: true,
            region: 'center',
            autoHeight: true,
            emptyText: '没有数据',
            viewConfig: {forceFit: true},
            reserveScrollOffset: true,
            columns: [
                new Ext.grid.RowNumberer(),
                {
                    header: '菜单名称',
                    align: "center",
                    sortable: true,
                    dataIndex: 'ItemName'
                },
                {
                    header: '请求时间',
                    align: "center",
                    sortable: true,
                    dataIndex: 'RequestTime'
                },
                {
                    header: '响应时间',
                    align: "center",
                    sortable: true,
                    dataIndex: 'ResponseTime',
                    renderer: function (v) {
                        if (!v) return "<font color=red>超时</font>";
                        return v;
                    }
                },
                {
                    header: '接待时间',
                    align: "center",
                    sortable: true,
                    dataIndex: 'ReceiveTime',
                    renderer: function (v) {
                        if (!v) return "<font color=red>超时</font>";
                        return v;
                    }
                }
            ],
            bbar: new Ext.PagingToolbar({
                pageSize: _pageSize,
                store: store,
                displayMsg: "显示{0}条到{1}条记录，总共{2}条记录",
                emptyMsg: "没有数据记录",
                displayInfo: true
            })
        });


        var moneyTab = new Ext.TabPanel({
            labelWidth: 85,
            activeTab: 0,
            plain: true,
            style: 'margin-top:15px',
            defaults: {autoScroll: true},
            items: [
                listView
            ]
        });


        var allPanel = new Ext.Panel({
            region: 'center',
            fileUpload: true,
            autoScroll: true,
            bodyStyle: "padding:20px;",
            items: [
                moneyTab,
                {
                    width: 100,
                    style: 'margin-top:20px',
                    xtype: 'button',
                    text: '导出',
                    handler: function (btn, pressed) {
                        if (listView.getStore().data.length == 0) {
                            Ext.MessageBox.alert('提示', '请先查询数据');
                            return;
                        }
                        msgTip.show();

                        Ext.Ajax.request({
                            url: '../export/reconciliation',
                            success: function (v, action) {
                                msgTip.hide();
                                try {
                                    var re = Ext.decode(v.responseText);
                                    Ext.MessageBox.alert('提示', "<a target='_blank' href='" + re.msg + "'>下载</a>");
                                }
                                catch (Exception) {

                                    Ext.MessageBox.alert('提示', '创建失败');
                                }
                            },
                            failure: function (v, action) {
                                msgTip.hide();
                                Ext.MessageBox.alert('提示', '创建失败');
                            }
                        });

                    }
                }
            ]
        });
        new Ext.Viewport(
            {
                layout: 'border',
                items: [
                    allPanel
                ]
            });
    })
</script>