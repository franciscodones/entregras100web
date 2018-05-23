/*
 * File: app/view/PlazasPanel.js
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

Ext.define('Entregas100Web.view.PlazasPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.plazaspanel',

    requires: [
        'Entregas100Web.view.PlazasPanelViewModel',
        'Entregas100Web.view.PlazasPanelViewController',
        'Ext.toolbar.Toolbar',
        'Ext.button.Button',
        'Ext.grid.Panel',
        'Ext.grid.filters.filter.String',
        'Ext.grid.column.Number',
        'Ext.grid.column.Boolean',
        'Ext.grid.filters.filter.Boolean',
        'Ext.view.Table',
        'Ext.grid.column.Action',
        'Ext.grid.filters.Filters'
    ],

    controller: 'plazaspanel',
    viewModel: {
        type: 'plazaspanel'
    },
    id: 'plazasPanel',
    closable: true,
    glyph: 'f279@FontAwesome',
    title: 'Plazas',

    layout: {
        type: 'vbox',
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
                },
                {
                    xtype: 'button',
                    permissionId: 15,
                    itemId: 'btnAgregar',
                    glyph: 'f055 @FontAwesome',
                    text: 'Agregar',
                    listeners: {
                        click: 'onBtnAgregarClick'
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
            itemId: 'plazasGrid',
            scrollable: true,
            autoLoad: true,
            columnLines: true,
            enableColumnHide: false,
            enableColumnMove: false,
            bind: {
                store: '{PlazasLocalStore}'
            },
            columns: [
                {
                    xtype: 'gridcolumn',
                    width: 80,
                    dataIndex: 'plaza',
                    text: 'Clave',
                    filter: {
                        type: 'string'
                    }
                },
                {
                    xtype: 'gridcolumn',
                    width: 150,
                    dataIndex: 'ciudad',
                    text: 'Plaza',
                    filter: {
                        type: 'string'
                    }
                },
                {
                    xtype: 'gridcolumn',
                    width: 550,
                    dataIndex: 'direccion_sucursal',
                    text: 'Dirección'
                },
                {
                    xtype: 'gridcolumn',
                    width: 180,
                    dataIndex: 'permiso',
                    text: 'Permiso'
                },
                {
                    xtype: 'numbercolumn',
                    width: 140,
                    dataIndex: 'clientes_estacionario',
                    text: 'Total de Clientes',
                    format: '0,000'
                },
                {
                    xtype: 'booleancolumn',
                    width: 120,
                    dataIndex: 'otorga_puntos',
                    text: 'Suma Puntos',
                    falseText: 'NO',
                    trueText: 'SI',
                    undefinedText: 'DESCONOCIDO',
                    filter: {
                        type: 'boolean'
                    }
                },
                {
                    xtype: 'actioncolumn',
                    permissionId: 16,
                    width: 30,
                    items: [
                        {
                            handler: function(view, rowIndex, colIndex, item, e, record, row) {
                                var editarPlazaWindow = Ext.create("Entregas100Web.view.EditarPlazaWindow");

                                editarPlazaWindow.down("form").loadRecord(record);
                                editarPlazaWindow.show();
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
                    ptype: 'gridfilters',
                    pluginId: 'filterPlugin'
                }
            ]
        }
    ]

});