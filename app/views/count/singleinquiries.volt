<div id="wrap">
<div class="container">
<script>
Ext.onReady(function () {
    chaobiaoSortLast = 0;
    chaobiaoSortNext = 0;
    groupSortLast = 0;
    groupSortNext = 0;
    var msgTip = new Ext.LoadMask(Ext.getBody(), {
        msg: 'Excel正在生成中',
        removeMask: true
    });
    _pageSize = 5;

    var teamCombox = new SettCombbox({
        url: '../Info/TeamList',
        fieldLabel: '一级菜单',
        width: 120,
        id: 'team',
        hiddenName: 'Menu',
        autoLoad: true
    });

    var itemCombbox = new SettCombbox({
        url: '../Info/SegmentList',
        fieldLabel: '二级菜单',
        hiddenName: 'item',
        width: 120,
        autoLoad: false
    });

    itemCombbox.getStore().load({params: {Type: 1}});

    teamCombox.on("select", function (f) {
        itemCombbox.getStore().load({params: {Type: f.getValue()}});
    })

    var infoSearch = new Ext.FormPanel({
        title: '统计条件',
        defaults: {
            border: false
        },
        labelAlign: 'right',
        style: 'padding-top:10px',
        url: '/ams/Charges/SearchFee',
        items: [
            {
                xtype: 'compositefield',
                fieldLabel: '统计年份',
                defaults: {
                    xtype: 'datefield'
                },
                items: [
                    new Ext.form.DateField({
                        plugins: 'monthPickerPlugin',
                        name: 'FromData',
                        width: 82,
                        value: new Date().format('Y'),
                        format: 'Y'
                    }), {
                        xtype: 'displayfield',
                        value: '至: '
                    }, new Ext.form.DateField({
                        plugins: 'monthPickerPlugin',
                        name: 'ToData',
                        width: 82,
                        value: new Date().format('Y'),
                        format: 'Y'
                    })]
            },
            teamCombox,
            itemCombbox,
            {
                xtype: 'button',
                width: '180',
                style: 'padding-left:105px;margin-top:5px;',
                text: '查询',
                handler: function () {
                    var data = infoSearch.getForm().getValues();

                    yearPanel.getStore().load({
                        params: data
                    });
                    quartPanel.getStore().load({
                        params: data
                    });
                    monthPanel.getStore().load({
                        params: data
                    });
                }
            }
        ]
    });
    var searchPanel = new Ext.TabPanel({
        labelWidth: 85,
        activeTab: 0,
        height: 170,
        plain: true,
        defaults: {autoScroll: true},
        items: [
            infoSearch
        ]
    });

    MyPanel = Ext.extend(Ext.grid.GridPanel, {
        multiSelect: true,
        stripeRows: true,
        region: 'center',
        height: 200,
        emptyText: '没有数据',
        viewConfig: {forceFit: true},
        reserveScrollOffset: true,
        columns: [
            {
                header: '汇总日期',
                align: "center",
                sortable: true,
                width: 100,
                dataIndex: 'Dates'
            },
            {
                header: '处理类型',
                align: "center",
                sortable: true,
                dataIndex: 'ItemName'
            },
            {
                header: '处理次数',
                align: "center",
                sortable: true,
                dataIndex: 'Count'
            }
        ],
        initComponent: function (cfg) {

            Ext.apply(this, cfg);

//            this.init();

//            this.bbar = new Ext.PagingToolbar({
//                pageSize: 20,
//                store: this.getStore(),
//                displayMsg: "显示{0}条到{1}条记录，总共{2}条记录",
//                emptyMsg: "没有数据记录",
//                displayInfo: true
//            });

            MyPanel.superclass.initComponent.call(this);
        }
    });


    var yearPanel = new MyPanel({
        title: '年度汇总',
        store: new Ext.data.JsonStore({
            url: '../Count/press',
            root: 'data',
            autoLoad: true,
            totalProperty: 'total',
            baseParams: {
                start: 0,
                limit: 20
            },
            fields: ['Count', 'Yearly', 'Dates', 'ItemName']
        })
    });

    var quartPanel = new MyPanel({
        title: '季度汇总',
        store: new Ext.data.JsonStore({
            url: '../Count/cut',
            root: 'data',
            autoLoad: true,
            totalProperty: 'total',
            baseParams: {
                start: 0,
                limit: 20
            },
            fields: ['Count', 'Yearly', 'Dates', 'ItemName']
        })
    });

    var monthPanel = new MyPanel({
        title: '月度汇总',
        store: new Ext.data.JsonStore({
            url: '../Count/reset',
            root: 'data',
            autoLoad: true,
            totalProperty: 'total',
            baseParams: {
                start: 0,
                limit: 20
            },
            fields: ['Count', 'Yearly', 'Dates', 'ItemName']
        })
    });


    var chaobiaoTab = new Ext.TabPanel(
        {
            activeTab: 0,
            plain: true,
            autoWidth: true,
            height: 200,
            style: 'margin-top:15px',
            items: [
                yearPanel, quartPanel, monthPanel
            ],
            listeners: {
                tabchange: function (tp, p) {
//                    chaobiaoPanel.store.reload();
//                    groupPanel.store.reload();
                }
            }
        });


    var allPanel = new Ext.Panel({
        region: 'center',
        autoScroll: true,
        bodyStyle: "padding:20px;",
        items: [
            searchPanel,
            chaobiaoTab
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

<style>
    .x-grid-record-red table {
        background-color: #ff7b7b;
    }

    .x-grid-record-blue table {
        background-color: #7bff7b;
    }
</style>