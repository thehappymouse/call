
<script type="text/javascript">
Ext.onReady(function () {

    var ManagerSignature = {
        labelWidth: 85,
        xtype: 'fieldset',
        bodyStyle: 'padding:6px',
        title: '模板文件上传',
        defaults: {width: 400},
        labelAlign: 'left',
        defaultType: 'textfield',
        items: [
            {
                xtype: 'compositefield',
                items: [
                    {
                        xtype: 'textfield',
                        name: 'Manager',
                        id: 'Manager',
                        fieldLabel: '欠费文件模板',
                        readOnly: true,
                        width: 260
                    },
                    {
                        xtype: 'fileuploadfield',
                        name: 'Signature',
                        buttonOnly: true,
                        id: 'Signature',
                        buttonText: '更新文件',
                        listeners: {
                            'fileselected': function (fb, v) {
                                Ext.getCmp('Manager').setValue(v);
                            }
                        }
                    }
                ]
            },
            {
                xtype: 'compositefield',
                items: [
                    {
                        xtype: 'textfield',
                        name: 'Hotellogo',
                        id: 'Hotellogo',
                        fieldLabel: '预收文件模板',
                        readOnly: true,
                        width: 260
                    },
                    {
                        xtype: 'fileuploadfield',
                        name: 'logoSignature',
                        buttonOnly: true,
                        id: 'logoSignature',
                        buttonText: '更新文件',
                        listeners: {
                            'fileselected': function (fb, v) {
                                Ext.getCmp('Hotellogo').setValue(v);
                            }
                        }
                    }
                ]
            }
        ]
    };




    var interfaceForm = {
        labelWidth: 85,
        xtype: 'fieldset',
        bodyStyle: 'padding:6px',
        title: '授权文件信息',
        defaults: {width: 550},
        labelAlign: 'left',
        defaultType: 'textfield',
        items: [

            {
                fieldLabel: "授权截止日期",
                name: 'MenuTypeList'
            },
            {
                fieldLabel: "菜品列表",
                name: 'MenuList'
            },
            {
                fieldLabel: "订餐接口",
                name: 'CateringOrder'
            },
            {
                fieldLabel: "Tokey数据",
                name: 'RoomTokey'
            },
            {
                fieldLabel: "订餐电话",
                name: 'CreatingPhone',
                maxlength: '14'
            },{
                xtype: 'compositefield',
                width: 800,
                fieldLabel: '酒店名称',
                items: [
                    {
                        xtype: 'textfield',
                        width: 190,
                        name: 'HotelName'
                    },
                    {
                        xtype: 'displayfield',
                        style: 'text-align:right;width:90px;',
                        value: '英文名称: '
                    }, {
                        name: 'HotelNameEn',
                        xtype: 'textfield',
                        width: 190,
                        allowBlank: false
                    }
                ]
            }
        ]
    };




    var formpanels = new Ext.form.FormPanel({
        region: 'center',
        fileUpload: true,
        autoScroll: true,
        tbar: [
            {
                xtype: 'label',
                style: 'font-weight:Bold;padding-left:10px',
                text: '系统设置项目'
            },
            '->',
            {
                text: '保存',
                iconCls: 'submit',
                handler: function (b, e) {
                    var fp = b.findParentByType("form");

                    if (fp.getForm().isValid()) {
                        fp.getForm().submit({
                            url: 'index.php?r=config/update',
                            method: 'POST',
                            success: function (form, action) {

                                ExtAlert("系统提示", "保存成功");
                                formpanels.getForm().load({
                                    url: 'index.php?r=config/data',
                                    params: {
                                    },
                                    failure: function (form, action) {
                                        ExtError("系统提示", action.result.errorMessage);
                                    }
                                });
                            },
                            failure: function (form, action) {
                                if (__debug) {
                                    ExtError("系统提示(debug)", "操作失败 ， 参考消息：<br/>" + action.response.responseText);
                                }
                                else {
                                    ExtError("系统提示", action.result.msg);
                                }
                            }
                        });
                    }
                    else {
                        ExtError("系统提示", "请将表单填写整后重试");
                    }
                }
            },
            '-',
            {
                text: '刷新',
                iconCls: 'refresh',
                handler: function (b, e) {
                    var fp = b.findParentByType("form");
                    fp.getForm().load({
                        url: 'index.php?r=config/data',
                        params: {
                        },
                        failure: function (form, action) {
                            ExtError("系统提示", action.result.errorMessage);
                        }
                    });
                }
            }
        ],
        bodyStyle: "padding:10px;",
        items: [
            ManagerSignature,
            interfaceForm
        ]
    });

    formpanels.getForm().load({
        url: 'index.php?r=config/data',
        success: function (form, action) {
            var type = action.result.data.TvType;

            Ext.getCmp("StandbyImg").setDisabled(type == 0);
            Ext.getCmp("backimg").setDisabled(type == 0);
            Ext.getCmp("StandbyVideo").setDisabled(type == 1);
            Ext.getCmp("backVideo").setDisabled(type == 1);

        },
        failure: function (form, action) {
            ExtError("系统提示", action.result.errorMessage);
        }
    });


    new Ext.Viewport(
        {
            layout: 'border',
            items: [
                formpanels
            ]
        });

});

</script>
<style type="text/css">
    #divHelp {
        padding: 10px 5px;
        font-size: 13px;

    }

    #divHelp li {
        margin-top: 4px;
    }
</style>