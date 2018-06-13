var EmailQueue = function (config) {
    config = config || {};
    EmailQueue.superclass.constructor.call(this, config);
};
Ext.extend(EmailQueue, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('emailqueue', EmailQueue);

EmailQueue = new EmailQueue();