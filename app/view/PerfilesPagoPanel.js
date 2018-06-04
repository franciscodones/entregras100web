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
        'Ext.tab.Panel',
        'Ext.grid.Panel',
        'Ext.toolbar.Toolbar',
        'Ext.grid.column.Number',
        'Ext.grid.filters.filter.String',
        'Ext.view.Table',
        'Ext.grid.column.Action',
        'Ext.grid.filters.Filters',
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
    items: [
        {
            xtype: 'tabpanel',
            flex: 1,
            items: [
                {
                    xtype: 'gridpanel',
                    showMenuTriggers: true,
                    showMenuHint: true,
                    itemId: 'perfilesPagoGrid',
                    scrollable: true,
                    width: 500,
                    title: 'Perfiles de Pago',
                    autoLoad: true,
                    columnLines: true,
                    enableColumnHide: false,
                    enableColumnMove: false,
                    bind: {
                        store: '{PerfilesPagoLocalStore}'
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
                            permissionId: 22,
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
                    ]
                }
            ]
        }
    ]

});