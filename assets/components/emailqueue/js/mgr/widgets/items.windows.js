EmailQueue.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'emailqueue-item-window-create';
    }
    Ext.applyIf(config, {
        title: _('emailqueue_item_create'),
        width: 550,
        autoHeight: true,
        url: EmailQueue.config.connector_url,
        action: 'mgr/item/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    EmailQueue.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(EmailQueue.window.CreateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('emailqueue_item_sender_package'),
            name: 'sender_package',
            id: config.id + '-sender_package',
            anchor: '99%',
        }, {
			xtype: 'textfield',
            fieldLabel: _('emailqueue_item_to'),
            name: 'to',
            id: config.id + '-to',
            anchor: '99%',
            allowBlank: false,
        }, {
			xtype: 'textfield',
            fieldLabel: _('emailqueue_item_subject'),
            name: 'subject',
            id: config.id + '-subject',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('emailqueue_item_body'),
            name: 'body',
            id: config.id + '-body',
            height: 150,
            anchor: '99%'
        }, {
            xtype: 'emailqueue-combo-status',
            id: config.id + '-status',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('emailqueue-item-window-create', EmailQueue.window.CreateItem);


EmailQueue.window.UpdateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'emailqueue-item-window-update';
    }
    Ext.applyIf(config, {
        title: _('emailqueue_item_update'),
        width: 550,
        autoHeight: true,
        url: EmailQueue.config.connector_url,
        action: 'mgr/item/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    EmailQueue.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(EmailQueue.window.UpdateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('emailqueue_item_sender_package'),
            name: 'sender_package',
            id: config.id + '-sender_package',
            anchor: '99%',
        }, {
			xtype: 'textfield',
            fieldLabel: _('emailqueue_item_to'),
            name: 'to',
            id: config.id + '-to',
            anchor: '99%',
            allowBlank: false,
        }, {
			xtype: 'textfield',
            fieldLabel: _('emailqueue_item_subject'),
            name: 'subject',
            id: config.id + '-subject',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textarea',
            fieldLabel: _('emailqueue_item_body'),
            name: 'body',
            id: config.id + '-body',
            height: 150,
            anchor: '99%'
        }, {
            xtype: 'emailqueue-combo-status',
            id: config.id + '-status',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('emailqueue-item-window-update', EmailQueue.window.UpdateItem);