EmailQueue.grid.Items = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'emailqueue-grid-items';
    }
    Ext.applyIf(config, {
        url: EmailQueue.config.connector_url,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        sm: new Ext.grid.CheckboxSelectionModel(),
        baseParams: {
            action: 'mgr/item/getlist'
        },
        listeners: {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateItem(grid, e, row);
            }
        },
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            /* getRowClass: function (rec) {
                return !rec.data.active
                    ? 'emailqueue-grid-row-disabled'
                    : '';
            } */
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    EmailQueue.grid.Items.superclass.constructor.call(this, config);

    // Clear selection on grid refresh
    this.store.on('load', function () {
        if (this._getSelectedIds().length) {
            this.getSelectionModel().clearSelections();
        }
    }, this);
};
Ext.extend(EmailQueue.grid.Items, MODx.grid.Grid, {
    windows: {},

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();

        var row = grid.getStore().getAt(rowIndex);
        var menu = EmailQueue.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'emailqueue-item-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({status: 1});
        w.show(e.target);
    },

    updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/item/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'emailqueue-item-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },
	sendEmail: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('emailqueue_emails_send')
                : _('emailqueue_email_send'),
            text: ids.length > 1
                ? _('emailqueue_emails_send_confirm')
                : _('emailqueue_email_send_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/item/send_emails',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },
    removeItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
                ? _('emailqueue_items_remove')
                : _('emailqueue_item_remove'),
            text: ids.length > 1
                ? _('emailqueue_items_remove_confirm')
                : _('emailqueue_item_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/item/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    getFields: function () {
        return ['id', 'sender_package', 'to', 'subject', 'date', 'sentdate', 'status', 'actions'];
    },

    getColumns: function () {
        return [{
            header: _('emailqueue_item_id'),
            dataIndex: 'id',
            sortable: true,
            width: 70
        }, {
            header: _('emailqueue_item_sender_package'),
            dataIndex: 'sender_package',
            sortable: true,
            width: 150,
        }, {
            header: _('emailqueue_item_to'),
            dataIndex: 'to',
            sortable: true,
            width: 150,
        }, {
            header: _('emailqueue_item_subject'),
            dataIndex: 'subject',
            sortable: false,
            width: 150,
        }, {
			header: _('emailqueue_item_date'),
            dataIndex: 'date',
            sortable: false,
            width: 150,
        }, {
			header: _('emailqueue_item_sentdate'),
            dataIndex: 'sentdate',
            sortable: false,
            width: 150,
        }, {
			header: _('emailqueue_item_status'),
            dataIndex: 'status',
            renderer: EmailQueue.utils.renderStatus,
			sortable: false,
            width: 150,
        }, {
            header: _('emailqueue_grid_actions'),
            dataIndex: 'actions',
            renderer: EmailQueue.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }];
    },

    getTopBar: function (config) {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('emailqueue_item_create'),
            handler: this.createItem,
            scope: this
		},{
			xtype: 'textfield',
			width: 80,
			id: config.id + '-send_count',
			value: 50,
		}, {
			text: '<i class="icon icon-send" title="'+_('emailqueue_title_send_n_email')+'"></i>',
            handler: this.sendItems,
            scope: this
		}, {	
			html: _('emailqueue_error'),
		}, {
			text: '<i class="icon icon-send" title="'+_('emailqueue_title_error_renew')+'"></i>',
            handler: this.renewErrorItems,
            scope: this
		}, {
			text: '<i class="icon icon-trash-o" title="'+_('emailqueue_title_error_remove')+'"></i>',
            handler: this.removeErrorItems,
            scope: this
		}, {	
			html: _('emailqueue_all'),
		}, {
			text: '<i class="icon icon-trash-o" title="'+_('emailqueue_title_all_remove')+'"></i>',
            handler: this.removeAll,
            scope: this	
		}, '->', {
			xtype: 'emailqueue-combo-status',
			id: config.id + '-search-field-status',
			listeners: {
				render: {
					fn: function (tf) {
						tf.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
							this._doSearch();
						}, this);
					}, scope: this
				},
				'select': {
					fn: function() { 
						this._doSearch(); 
					},scope:this}
			}
		},{
            xtype: 'emailqueue-field-search',
            width: 250,
            listeners: {
                search: {
                    fn: function (field) {
                        this._doSearch(field);
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._clearSearch();
                    }, scope: this
                },
            }
        }];
    },
	renewErrorItems: function () {
        MODx.msg.confirm({
            title:  _('emailqueue_errors_renew'),
            url: this.config.url,
            params: {
                action: 'mgr/item/renew_error',
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },
	removeErrorItems: function () {
        MODx.msg.confirm({
            title:  _('emailqueue_errors_remove'),
            url: this.config.url,
            params: {
                action: 'mgr/item/remove_error',
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },
	removeAll: function () {
        MODx.msg.confirm({
            title:  _('emailqueue_all_remove'),
            url: this.config.url,
            params: {
                action: 'mgr/item/remove_all',
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },
	sendItems:  function (btn, e, row) {

        var topic = '/emailqueue/';
		var register = 'mgr';
		var send_count = Ext.getCmp(this.config.id + '-send_count').getValue();
		this.console = MODx.load({
		   xtype: 'modx-console'
		   ,register: register
		   ,topic: topic
		   ,show_filename: 0
		   ,listeners: {
			 'shutdown': {fn:function() {
				 Ext.getCmp('emailqueue-grid-items').refresh();
			 },scope:this}
		   }
		});
		this.console.show(Ext.getBody());
		MODx.Ajax.request({
			url: this.config.url
			,params: {
				action: 'mgr/item/send_emails_console'
				,register: register
				,topic: topic
				,send_count: send_count
			}
			,listeners: {
				'success':{fn:function() {
					this.console.fireEvent('complete');
				},scope:this}
			}
		});
    },
	
    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                }
                else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _doSearch: function (tf) {
        if(tf){
			this.getStore().baseParams.query = tf.getValue();
		}
		var status = Ext.getCmp(this.config.id + '-search-field-status');
		if(status) this.getStore().baseParams.status = status.getValue();
		
		this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
		
		this.getStore().baseParams.status = '';
		var status = Ext.getCmp(this.config.id + '-search-field-status');
		if(status) status.setValue('');
		
        this.getBottomToolbar().changePage(1);
    },
});
Ext.reg('emailqueue-grid-items', EmailQueue.grid.Items);

EmailQueue.combo.Status = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		store: new Ext.data.SimpleStore({
			fields: ['status', 'label']
			,data: [
				[1 , _('emailqueue_status_new')],
				[2 , _('emailqueue_status_sended')],
				[3 , _('emailqueue_status_error')],
			]
		})
		,emptyText: _('emailqueue_item_status')
		,displayField: 'label'
		,valueField: 'status'
		,hiddenName: 'status'
		,mode: 'local'
		,triggerAction: 'all'
		,editable: false
		,selectOnFocus: false
		,preventRender: true
		,forceSelection: true
		,enableKeyEvents: true
	});
	EmailQueue.combo.Status.superclass.constructor.call(this,config);
};
Ext.extend(EmailQueue.combo.Status,MODx.combo.ComboBox, {});
Ext.reg('emailqueue-combo-status',EmailQueue.combo.Status);