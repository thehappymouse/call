<script>
Ext.onReady(function(){
    Ext.chart.Chart.CHART_URL='/ams/public/js/charts.swf';
    /*var store = new Ext.data.JsonStore({
     fields: ['key', 'value'],
     // 获取Json数据的URL
     url:"show_quota_pie_dao.asp",
     // 设置自动获取
     autoLoad: true,
     root:"data"
     });*/
    var store = new Ext.data.JsonStore({
        fields: ['season', 'total'],
        data: {{arrcount}}
    });

    /* var tingStore = new Ext.data.JsonStore({
     fields: ['key', 'value'],
     // 获取Json数据的URL
     url:"show_quota_pie_dao.asp",
     // 设置自动获取
     autoLoad: true,
     root:"data"
     });*/

    var tingStore = new Ext.data.JsonStore({
        fields: ['season', 'total'],
        data:<?php echo $cutinfo; ?>

    });

    onePanel = new Ext.Panel({
        title: '欠费催费次数客户统计',
        region:'north',
        flex: 1,
        items: {
            store: store,
            xtype: 'piechart',
            dataField: 'total',
            categoryField: 'season',
            extraStyle:
            {
                legend:
                {
                    display: 'bottom',
                    padding: 5,
                    font:
                    {
                        family: 'Tahoma',
                        size: 13
                    }
                }
            }
        }
    });
    twoPanel = new Ext.Panel({
        region:'center',
        flex: 1,
        title: '停电统计',
        items: {
            store: tingStore,
            xtype: 'piechart',
            dataField: 'total',
            categoryField: 'season',
            extraStyle:
            {
                legend:
                {
                    display: 'bottom',
                    padding: 5,
                    font:
                    {
                        family: 'Tahoma',
                        size: 13
                    }
                }
            }
        }
    });

    /* var tstore = new Ext.data.JsonStore({
     fields: ['name', 'value'],
     // 获取Json数据的URL
     url:"show_quota_pie_dao.asp",
     // 设置自动获取
     autoLoad: true,
     root:"data"
     });*/


    var setForm = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        labelAlign: 'right',
        style: 'padding-top:10px',
        url:'/ams/manager/changeindexline',
        defaultType: 'textfield',
        items: [{
                fieldLabel: '指标',
                name: 'IndexLine',
                allowBlank:false
        }]
    });

    var setWin =new Ext.Window({
            layout:'fit',
            width:300,
            height:120,
            closeAction:'hide',
            plain: true,
            title: '指标设置',
            items:[setForm],
            buttons: [{
                text: '确定',
                handler: function(){
                    setForm.getForm().submit({
                        success: function(v,action){
                            Ext.MessageBox.alert("提示",action.result.msg)
                            location.reload();
                        },
                        failure: function(){
                            Ext.MessageBox.alert("提示",action.result.msg)
                        }
                    })
                }
            },{
                text: '关闭',
                handler: function(){
                    setWin.hide();
                }
            }]
        })

     var tstore = new Ext.data.JsonStore(
     {
         fields:['name', 'value', 'views', 'avge'],
         data: {{sig}}

     });

    var topPanel = new Ext.Panel({
        iconCls:'chart',
        title: '个人排名',
        frame:true,
        width:500,
        height:200,
        layout:'fit',
<?php
 if($lineshow) {
?>
        tbar:[{
            text: '指标设置',
            handler: function(){
                setWin.show();
            }
        }],
        <?php }?>
        items: {
            xtype: 'columnchart',
            store: tstore,
            url:Ext.chart.Chart.CHART_URL,
            xField: 'name',
            yAxis: new Ext.chart.NumericAxis({
                displayName: 'value',
                labelRenderer : Ext.util.Format.numberRenderer('0,0')
            }),
            tipRenderer : function(chart, record, index, series){
                if(series.yField == 'value'){
                    return  "欠费金额 " +Ext.util.Format.number(record.data.value, '0,0');
                }else if(series.yField == 'views'){
                    return  "应完成的指标为" +Ext.util.Format.number(record.data.views, '0,0');
                } else {
                    return  "平均指标为" +Ext.util.Format.number(record.data.avge, '0,0');
                }
            },
            chartStyle: {
                padding: 10,
                animationEnabled: true,
                font: {
                    name: 'Tahoma',
                    color: 0x444444,
                    size: 11
                },
                dataTip: {
                    padding: 5,
                    border: {
                        color: 0x99bbe8,
                        size:1
                    },
                    background: {
                        color: 0xDAE7F6,
                        alpha: .9
                    },
                    font: {
                        name: 'Tahoma',
                        color: 0x15428B,
                        size: 10,
                        bold: true
                    }
                },
                xAxis: {
                    color: 0x69aBc8,
                    majorTicks: {color: 0x69aBc8, length: 4},
                    minorTicks: {color: 0x69aBc8, length: 2},
                    majorGridLines: {size: 1, color: 0xeeeeee}
                },
                yAxis: {
                    color: 0x69aBc8,
                    majorTicks: {color: 0x69aBc8, length: 4},
                    minorTicks: {color: 0x69aBc8, length: 2},
                    majorGridLines: {size: 1, color: 0xdfe8f6}
                }
            },
            series: [{
                type: 'column',
                displayName: 'value',
                yField: 'value',
                style: {
                    image:'bar.gif',
                    mode: 'stretch',
                    color:0x99BBE8
                }
            },{
                type:'line',
                displayName: 'views',
                yField: 'views',
                style: {
                    color: 0x15428B
                }
            },{
                 type:'line',
                 displayName: 'avge',
                 yField: 'avge',
                 style: {
                     color: 0x69aBc8
                 }
             }]
        }
    });


var s2 = new Ext.data.JsonStore(
    {
        fields:['name', 'value', 'views'],
        url: '/ams/site/yearmonth',
        autoLoad: true
});


    var bottomPanel = new Ext.Panel(
        {

            height: 160,
            flex: 1,
            items: [{
                xtype: 'columnchart',
                store: s2,
                xField: 'name',
                yField: 'value'
            }]

        });
var bottomPane2 = new Ext.Panel(
    {

        height: 160,
        flex: 1,
        items: [{
            xtype: 'columnchart',
            store: s2,
            xField: 'name',
            yField: 'views'
        }]

    });

    var bottomPanel = new Ext.Panel({

        title: '欠费月份户数及金额的分布',
        height: 420,
        flex: 1,
        tbar:[
            {
                text: '月份选择  '
            },
            new QuietCombox({
                displayField:'Text',
                hiddenName:'Duty',
                valueField:'Duty',
                fields:['Text','Duty'],
                data   : [['2014', '2014'], ['2013', 2013]],
                width: 80,
                listeners:{
                    'select': function(f){
                        s2.load({params:{"year": f.getValue()}});
                    }
                }
            })
        ],
        items:[
            bottomPanel,bottomPane2
        ]
    });

    var bottomPanelCount = new Ext.Panel({
        title: '欠费月份及户数的分布',
        height: 180,
        items: {
            xtype: 'columnchart',
            store: s2,
            xField: 'name',
            yField: 'views'
        }
    });

    var storezrjf = new Ext.data.JsonStore({
            fields:['name', 'visits', 'views'],
            data: {{zrjf}}
        });
    var centPanel = new Ext.Panel({
          iconCls:'chart',
          title: '昨日交费情况',
          frame:true,
          height:180,
          layout:'fit',
          items: {
              xtype: 'columnchart',
              store: storezrjf,
              url:Ext.chart.Chart.CHART_URL,
              xField: 'name',
              yAxis: new Ext.chart.NumericAxis({
                  displayName: 'Visits',
                  labelRenderer : Ext.util.Format.numberRenderer('0,0')
              }),
              tipRenderer : function(chart, record, index, series){
                   return '昨日交费情况' + Ext.util.Format.number(record.data.visits, '0,0');
              },
              chartStyle: {
                  padding: 10,
                  animationEnabled: true,
                  font: {
                      name: 'Tahoma',
                      color: 0x444444,
                      size: 11
                  },
                  dataTip: {
                      padding: 5,
                      border: {
                          color: 0x99bbe8,
                          size:1
                      },
                      background: {
                          color: 0xDAE7F6,
                          alpha: .9
                      },
                      font: {
                          name: 'Tahoma',
                          color: 0x15428B,
                          size: 10,
                          bold: true
                      }
                  },
                  xAxis: {
                      color: 0x69aBc8,
                      majorTicks: {color: 0x69aBc8, length: 4},
                      minorTicks: {color: 0x69aBc8, length: 2},
                      majorGridLines: {size: 1, color: 0xeeeeee}
                  },
                  yAxis: {
                      color: 0x69aBc8,
                      majorTicks: {color: 0x69aBc8, length: 4},
                      minorTicks: {color: 0x69aBc8, length: 2},
                      majorGridLines: {size: 1, color: 0xdfe8f6}
                  }
              },
              series: [{
                  type: 'column',
                  displayName: 'Page Views',
                  yField: 'visits',
                  style: {
                      image:'bar.gif',
                      mode: 'stretch',
                      color:0x99BBE8
                  }
              }]
          }
      });
    var centerPanel = new Ext.Panel(
        {
            height:750,
            layout: {
                type  : 'vbox',
                align : 'stretch',
                pack  : 'start'
            },
            items:[
                topPanel, centPanel,bottomPanel
            ]
        });
        var cPanel = new Ext.Panel(
                {
                    region: 'center',

                    autoScroll:true,
                    items:[
                        centerPanel
                    ]
                });
    var eastPanel = new Ext.Panel(
        {
            region: 'east',
            width: 350,
            layout: {
                type  : 'vbox',
                align : 'stretch',
                pack  : 'start'
            },
            items:[
                onePanel, twoPanel
            ]
        });

    var arrarsPanel = new Ext.grid.PropertyGrid({
        width: 300,
        autoHeight: true,
        title:'欠费情况',
        region: "north",
        propertyNames: {
            tested: 'QA',
            borderWidth: 'Border Width'
        },
        initComponent : function(){
            this.customRenderers = this.customRenderers || {};
            this.customEditors = this.customEditors || {};
            this.lastEditRow = null;
            var store = new Ext.grid.PropertyStore(this);
            this.propStore = store;
            var cm = new Ext.grid.PropertyColumnModel(this, store);
//            store.store.sort('name', 'ASC');
            this.addEvents(

                /**
                 * @event beforepropertychange
                 * Fires before a property value changes.  Handlers can return false to cancel the property change
                 * (this will internally call {@link Ext.data.Record#reject} on the property's record).
                 * @param {Object} source The source data object for the grid (corresponds to the same object passed in
                 * as the {@link #source} config property).
                 * @param {String} recordId The record's id in the data store
                 * @param {Mixed} value The current edited property value
                 * @param {Mixed} oldValue The original property value prior to editing
                 */
                'beforepropertychange',

                /**
                 * @event propertychange
                 * Fires after a property value has changed.
                 * @param {Object} source The source data object for the grid (corresponds to the same object passed in
                 * as the {@link #source} config property).
                 * @param {String} recordId The record's id in the data store
                 * @param {Mixed} value The current edited property value
                 * @param {Mixed} oldValue The original property value prior to editing
                 */
                'propertychange'
            );
            this.cm = cm;
            this.ds = store.store;
            Ext.grid.PropertyGrid.superclass.initComponent.call(this);

            this.mon(this.selModel, 'beforecellselect', function(sm, rowIndex, colIndex){
                if(colIndex === 0){
                    this.startEditing.defer(200, this, [rowIndex, 1]);
                    return false;
                }
            }, this);
        },

        source: {
            '排名': '{{arrear["UserIndex"]}}',
            '欠费户数': '{{arrear["CustomerCount"]}}',
            '欠费笔数': '{{arrear["ArrearCount"]}}',
            '欠费金额': '{{arrear["Money"]}}',
            '电费回收率': '{{arrear["Rate"]}}',
            '户数回收率': '{{arrear["CountRate"]}}'
        },
        viewConfig : {
            forceFit: true,
            scrollOffset: 2 // the grid will never have scrollbars
        }
    });
    arrarsPanel.on("beforeedit",function(e){
        e.cancel=true;
        return false;
    });
var jiaoPanel = new Ext.grid.PropertyGrid({
        width: 300,
        autoHeight: true,
        title:'昨日交费情况',
        region: "center",
        propertyNames: {
            '中文':'score'
        },
        sortPropertyColumn:false,
        source: {
            '交费户数': '{{yesterday["Count"]}}',
            '交费金额': '{{yesterday["Money"]}}'
        },
        viewConfig : {
            forceFit: true,
            scrollOffset: 1// the grid will never have scrollbars
        }
    });
    jiaoPanel.on("beforeedit",function(e){
            e.cancel=true;
            return false;
    });
    var weastPanel = new Ext.Panel(
        {
            region: 'weastPanel',
            region: "west",
            width: 250,
            layout: {
                type  : 'vbox',
                align : 'stretch',
                pack  : 'start'
            },
            items:[
                arrarsPanel,
                jiaoPanel
            ]
        });

    new Ext.Viewport(
        {
            title: "Viewport",
            layout: "border",
            //height:"100%",
            defaults:
            {
                bodyStyle: "background-color: #FFFFFF;",
                frame: true
            },
            items: [
                eastPanel,
                cPanel,
                weastPanel
            ]
        });
})
</script>