
<script>

    var time1 = {{time1}};
    var time2 = {{time2}};

    Ext.onReady(function () {
        var msgTip = new Ext.LoadMask(Ext.getBody(), {
            msg: '文件正在导入中',
            removeMask: true
        });
        var infoSearch = new Ext.FormPanel({
            title: '系统设置',
            fileUpload: true,
            labelAlign: 'right',
            style: 'padding-top:10px',
            url: 'ArrearsUpload',
            items: [
                new Ext.form.ComboBox({
                    store: new Ext.data.ArrayStore({
                        fields: ['value', 'show'],
                        data: [
                            ['1', '1分钟'],
                            ['2', '2分钟'],
                            ['3', '3分钟'],
                            ['4', '4分钟'],
                            ['5', '5分钟']
                        ]
                    }),
                    displayField: 'show',
                    valueField: 'value',
                    typeAhead: true,
                    mode: 'local',
                    editable: false,
                    value: time1,
                    hiddenName: 'request',
                    fieldLabel: '响应超时',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true
                }),
                new Ext.form.ComboBox({
                    store: new Ext.data.ArrayStore({
                        fields: ['value', 'show'],
                        data: [
                            ['1', '1分钟'],
                            ['2', '2分钟'],
                            ['3', '3分钟'],
                            ['4', '4分钟'],
                            ['5', '5分钟']
                        ]
                    }),
                    displayField: 'show',
                    valueField: 'value',
                    typeAhead: true,
                    mode: 'local',
                    editable: false,
                    value: time2,
                    hiddenName: 'response',
                    fieldLabel: '接待超时',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true
                }),
                {
                    xtype: 'button',
                    width: 100,
                    style: 'margin-left:110px;margin-top:20px;',
                    text: '保存',
                    handler: function () {
                        msgTip.show();
                        infoSearch.getForm().submit({
                            success: function () {
                                msgTip.hide();
                                Ext.MessageBox.alert('提示', "上传成功");
                            },
                            failure: function (a, b) {
                                msgTip.hide();
                                Ext.MessageBox.alert('提示', b.result.msg);
                            }
                        })
                    }
                }
            ]
        });

        var searchTab = new Ext.TabPanel({
            activeTab: 0,
            height: 175,
            plain: true,
            region: 'north',
            bodyStyle: "padding:20px;",
            defaults: {autoScroll: true},
            items: [
                infoSearch
            ]
        });


        new Ext.Viewport(
            {
                layout: 'border',
                items: [
                    new Ext.Panel({
                        region: 'center',
                        autoScroll: true,
                        bodyStyle: "padding:20px;",
                        items: [
                            searchTab
                        ]
                    })
                ]
            });
    })
</script>