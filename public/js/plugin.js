
SettCombbox = Ext.extend(Ext.form.ComboBox, {
    typeAhead: true,
    forceSelection: true,
    triggerAction: 'all',
    displayField:'Name',
    valueField: 'ID',
    editable     : false,
    mode: 'local',
    selectOnFocus:true,
    url:null,
    hiddenName:'ID',
    autoLoad:false,
    fileds:[{name: 'Name'}, {name: 'ID'}],
    initComponent: function(cfg)
    {
        Ext.apply(this, cfg);
        SettCombbox.superclass.initComponent.call(this);
        this.createStore();
        this.loadFirstData();
    },
    createStore: function()
    {
        this.store = new Ext.data.Store({
            url:this.url,
            reader: new Ext.data.JsonReader({
                root:'data'
            },this.fileds),
            autoLoad:this.autoLoad
        });
    },
    loadFirstData:function()
    {
        var that = this;
        this.store.on('load',function(){
            that.setValue(that.store.getAt(0).data.ID);
        });
    }
});

QuietCombox =  Ext.extend(Ext.form.ComboBox,{
        data         : [],
        mode    : 'local',
        displayField : 'labelText',
        labelAlign   : 'right',
        editable     : false,
        valueField   : 'value',
        triggerAction: 'all',
        hiddenName:'value',
        fields : ['labelText', 'value'],
        initComponent: function(cfg)
        {
            Ext.apply(this, cfg);
            QuietCombox.superclass.initComponent.call(this);
            this.createStore();
            this.loadFirstData();
        },
        createStore: function()
        {
            this.store = new Ext.data.ArrayStore({
                    fields: this.fields,
                    data : this.data
            });
        },
        loadFirstData:function()
        {
            this.setValue(this.data[0][1]);
        }
 });

if (!Ext.grid.GridView.prototype.templates)
{  
    Ext.grid.GridView.prototype.templates = {};  
}   

Ext.grid.GridView.prototype.templates.cell = new Ext.Template(   
  '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} x-selectable {css}" style="{style}" tabIndex="0" {cellAttr}>' ,   
  '<div class="x-grid3-cell-inner x-grid3-col-{id}" {attr}>{value}</div>' ,  
  '</td>'   
);

spliceArr = function  (v)
{
    var arr = [];
    Ext.each(v, function(item){
        arr.push(item.data.sort);
    })
    if (arr.length == 0) {
        return [];
    }
    var tempArray = arr.slice(0);
    for (var i = 0; i< tempArray.length; i++) {
        for (var j = i + 1; j< tempArray.length; ) {
            if (tempArray[i] == tempArray[j]) {
                tempArray.splice(j,1);
            } else {
                j++;
            }
        }
    }

    return tempArray;
}
arrearsMoney = 0;

var myDate = new Date();
currentDate = myDate.getFullYear()+"-"+(parseInt(myDate.getMonth()+1) < 10 ? "0"+parseInt(myDate.getMonth()+1) : parseInt(myDate.getMonth()+1))+"-"+(myDate.getDate() < 10 ?"0"+myDate.getDate() : myDate.getDate())+" 00:00:00";




