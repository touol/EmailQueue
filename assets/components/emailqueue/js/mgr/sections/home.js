EmailQueue.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'emailqueue-panel-home',
            renderTo: 'emailqueue-panel-home-div'
        }]
    });
    EmailQueue.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(EmailQueue.page.Home, MODx.Component);
Ext.reg('emailqueue-page-home', EmailQueue.page.Home);