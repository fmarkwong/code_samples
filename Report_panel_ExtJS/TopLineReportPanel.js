/**
 * ToplinePanel
 *
 * a wrapper for Topline Reports
 *
 */
Ext.define('Isvy.view.ToplinePanel', {
    extend : 'Ext.panel.Panel',

    requires : ['Isvy.chart.Pie'],

    initComponent : function() {
        var me = this, config;
        Ext.tip.QuickTipManager.init();

        config = Ext.Object.merge(me.getToolBar(), me.getPanelItems());

        Ext.apply(me, config);

        if (!me.store) {
            me.store = me.buildStore();
            me.store.load({
                scope : me,
                callback : me.setPanelTitle
            });
        }

        me.callParent(arguments);
    },

    setPanelTitle : function(records) {
        var me = this, field_desc = records[0].get('field_desc');

        me.setTitle(me.field_id + '. ' + field_desc);
    },

    getToolBar : function() {
        var me = this;
        return {
            dockedItems : [{
                xtype : 'toolbar',
                dock : 'top',
                items : [{
                    xtype : 'button',
                    icon : '/i/refresh.gif',
                    tooltip : LCL('toplinepanel.tooltip.refresh'),
                }, {
                    xtype : 'tbseparator'
                }, {
                    xtype : 'button',
                    icon : '/i/saveicon.gif',
                    tooltip : LCL('toplinepanel.tooltip.save'),
                }, {
                    xtype : 'button',
                    icon : '/i/send_button.gif',
                    tooltip : LCL('toplinepanel.tooltip.send'),
                }, {
                    xtype : 'tbseparator'
                }, {
                    xtype : 'button',
                    icon : '/i/excel_button.gif',
                    tooltip : LCL('toplinepanel.tooltip.excel'),
                }, {
                    xtype : 'button',
                    icon : '/i/ppt_button.gif',
                    tooltip : LCL('toplinepanel.tooltip.ppt'),
                }, {
                    xtype : 'button',
                    icon : '/i/pdf_button.gif',
                    tooltip : LCL('toplinepanel.tooltip.pdf'),
                }, {
                    xtype : 'button',
                    icon : '/jslib/extjs4/docs/resources/images/print_icon.png',
                    tooltip : LCL('toplinepanel.tooltip.print'),
                }]
            }],
        }
    },

    getPanelItems : function() {
        var me = this;
        return {
            items : [{
                xtype : 'isvy-chart-pie',
                dsid : me.dsid,
                field_id : me.field_id,
                height : 200,
                width : 300
            }]
        }
    },

    /**
     * Create a new store using dsid and field_id
     * @private
     */
    buildStore : function() {
        var me = this;

        return Ext.create('Ext.data.Store', {
            fields : ['field_desc'],
            proxy : {
                type : 'direct',
                directFn : 'IS.Dataset.get_field_desc',
                reader : 'json',
                extraParams : {
                    dsid : me.dsid,
                    field_id : me.field_id,
                }
            },
        });
    }
});
