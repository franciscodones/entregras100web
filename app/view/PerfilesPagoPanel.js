/*
 * File: app/view/PerfilesPagoPanel.js
 *
 * This file was generated by Sencha Architect
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 6.5.x Classic library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 6.5.x Classic. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('Entregas100Web.view.PerfilesPagoPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.perfilespagopanel',

    requires: [
        'Entregas100Web.view.PerfilesPagoPanelViewModel',
        'Entregas100Web.view.PerfilesPagoPanelViewController',
        'Ext.toolbar.Toolbar',
        'Ext.grid.Panel',
        'Ext.grid.column.Number',
        'Ext.grid.filters.filter.String',
        'Ext.view.Table',
        'Ext.grid.column.Action',
        'Ext.grid.filters.Filters',
        'Ext.tab.Panel',
        'Ext.grid.column.Boolean',
        'Ext.tab.Tab'
    ],

    controller: 'perfilespagopanel',
    viewModel: {
        type: 'perfilespagopanel'
    },
    id: 'perfilesPagoPanel',
    closable: true,
    glyph: 'f09d@FontAwesome',
    title: 'Perfiles de Pago',

    layout: {
        type: 'hbox',
        align: 'stretch'
    },
    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            ui: 'footer',
            items: [
                {
                    xtype: 'button',
                    itemId: 'btnRefrescar',
                    ui: 'default-small',
                    glyph: 'f021@FontAwesome',
                    text: 'Refrescar',
                    listeners: {
                        click: 'onBtnRefrescarClick'
                    }
                }
            ]
        }
    ],
    items: [
        {
            xtype: 'gridpanel',
            showMenuTriggers: true,
            showMenuHint: true,
            flex: 1,
            itemId: 'perfilesPagoGrid',
            scrollable: true,
            width: 500,
            autoLoad: true,
            columnLines: true,
            enableColumnHide: false,
            enableColumnMove: false,
            bind: {
                store: '{PerfilesPagoLocalStore}'
            },
            columns: [
                {
                    xtype: 'numbercolumn',
                    width: 60,
                    dataIndex: 'id',
                    text: 'Id',
                    format: '0,000'
                },
                {
                    xtype: 'gridcolumn',
                    width: 200,
                    dataIndex: 'descripcion',
                    text: 'Descripción',
                    filter: {
                        type: 'string'
                    }
                },
                {
                    xtype: 'actioncolumn',
                    width: 30,
                    items: [
                        {
                            handler: function(view, rowIndex, colIndex, item, e, record, row) {
                                var editarPerfilPagoWindow = Ext.create("Entregas100Web.view.EditarPerfilPagoWindow");

                                editarPerfilPagoWindow.down("form").loadRecord(record);
                                editarPerfilPagoWindow.show();
                            },
                            icon: 'resources/icon/editar.png',
                            tooltip: 'Editar'
                        }
                    ]
                }
            ],
            viewConfig: {
                emptyText: 'No se encontraron resultados'
            },
            plugins: [
                {
                    ptype: 'gridfilters'
                }
            ],
            listeners: {
                select: 'onPerfilesPagoGridSelect'
            }
        },
        {
            xtype: 'tabpanel',
            flex: 1,
            frame: true,
            items: [
                {
                    xtype: 'gridpanel',
                    itemId: 'combinacionesGrid',
                    scrollable: true,
                    title: 'Formas de Pago',
                    autoLoad: true,
                    enableColumnHide: false,
                    enableColumnMove: false,
                    bind: {
                        store: '{CombinacionesFormaPerfilLocalStore}'
                    },
                    dockedItems: [
                        {
                            xtype: 'toolbar',
                            dock: 'top',
                            ui: 'footer',
                            items: [
                                {
                                    xtype: 'button',
                                    itemId: 'btnAgregarFormaPago',
                                    glyph: 'f055@FontAwesome',
                                    text: 'Agregar Forma de Pago',
                                    listeners: {
                                        click: 'onBtnAgregarFormaPagoClick'
                                    }
                                }
                            ]
                        }
                    ],
                    columns: [
                        {
                            xtype: 'numbercolumn',
                            width: 60,
                            dataIndex: 'forma_pago_id',
                            text: 'Id',
                            format: '0,000'
                        },
                        {
                            xtype: 'gridcolumn',
                            width: 200,
                            dataIndex: 'forma_pago',
                            text: 'Forma de pago'
                        },
                        {
                            xtype: 'booleancolumn',
                            dataIndex: 'es_default',
                            text: 'Es la Default',
                            falseText: 'NO',
                            trueText: 'SI',
                            undefinedText: 'DESCONOCIDO'
                        },
                        {
                            xtype: 'actioncolumn',
                            width: 30,
                            items: [
                                {
                                    handler: function(view, rowIndex, colIndex, item, e, record, row) {
                                        var combinacionesFormaPerfilLocalStore = record.store,
                                            records, waitWindow, filter;

                                        // si la forma de pago es la default no se puede eliminar
                                        if (record.get("es_default")) {
                                            Ext.Msg.show({
                                                title: "Mensaje del sistema",
                                                message: "Antes de eliminar esta forma de pago seleccione otra como la default",
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.Msg.ERROR
                                            });
                                            return;
                                        }

                                        Ext.Msg.confirm(
                                        "Mensaje del sistema",
                                        "¿Desea eliminar esta forma de pago?",
                                        function(result) {
                                            if (result == "yes") {
                                                waitWindow = Ext.Msg.wait("Guardando cambios...");
                                                combinacionesFormaPerfilLocalStore.remove(record);
                                                combinacionesFormaPerfilLocalStore.sync({
                                                    success: onSyncSuccess
                                                });
                                            }
                                        }
                                        );

                                        function onSyncSuccess() {
                                            waitWindow.close();
                                        }
                                    },
                                    isActionDisabled: function(view, rowIndex, colIndex, item, record) {
                                        return record.get("es_default");
                                    },
                                    icon: 'resources/icon/garbage.png',
                                    tooltip: 'Eliminar esta forma de pago'
                                }
                            ]
                        },
                        {
                            xtype: 'actioncolumn',
                            width: 30,
                            items: [
                                {
                                    handler: function(view, rowIndex, colIndex, item, e, record, row) {
                                        var combinacionesFormaPerfilLocalStore = record.store,
                                            perfilesPagoPanel = view.up("#perfilesPagoPanel"),
                                            perfilPagoRecord = perfilesPagoPanel.down("#perfilesPagoGrid").getSelection()[0],
                                            perfilesPagoLocalStore = perfilesPagoPanel.getController().getStore("PerfilesPagoLocalStore"),
                                            formaPagoDefault, waitWindow, filter;

                                        Ext.Msg.confirm(
                                        "Mensaje del sistema",
                                        "¿Desea seleccionar esta forma de pago como la default?",
                                        function(result) {
                                            if (result == "yes") {
                                                waitWindow = Ext.Msg.wait("Guardando cambios...");
                                                formaPagoDefault = combinacionesFormaPerfilLocalStore.findRecord("es_default", true);
                                                if (formaPagoDefault) {
                                                    formaPagoDefault.set("es_default", false);
                                                }
                                                record.set("es_default", true);
                                                perfilPagoRecord.set("forma_pago_id", record.get("forma_pago_id"));
                                                perfilesPagoLocalStore.sync({
                                                    success: onSyncSuccess,
                                                    failure: onSyncError
                                                });
                                            }
                                        }
                                        );

                                        function onSyncSuccess() {
                                            waitWindow.close();
                                            combinacionesFormaPerfilLocalStore.commitChanges();
                                        }

                                        function onSyncError() {
                                            combinacionesFormaPerfilLocalStore.rejectChanges();
                                        }
                                    },
                                    isActionDisabled: function(view, rowIndex, colIndex, item, record) {
                                        return record.get("es_default");
                                    },
                                    icon: 'resources/icon/claveakey.png',
                                    tooltip: 'Seleccionar como default'
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ],
    listeners: {
        beforerender: 'onPerfilesPagoPanelBeforeRender'
    }

});