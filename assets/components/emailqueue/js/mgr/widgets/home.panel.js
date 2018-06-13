EmailQueue.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'emailqueue-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('emailqueue') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('emailqueue_items'),
                layout: 'anchor',
                items: [{
                    html: _('emailqueue_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'emailqueue-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    EmailQueue.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(EmailQueue.panel.Home, MODx.Panel);
Ext.reg('emailqueue-panel-home', EmailQueue.panel.Home);
