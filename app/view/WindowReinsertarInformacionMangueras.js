/*
 * File: app/view/WindowReinsertarInformacionMangueras.js
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

Ext.define('Entregas100Web.view.WindowReinsertarInformacionMangueras', {
    extend: 'Ext.window.Window',
    alias: 'widget.windowreinsertarinformacionmangueras',

    requires: [
        'Entregas100Web.view.WindowReinsertarInformacionManguerasViewModel',
        'Entregas100Web.view.WindowReinsertarInformacionManguerasViewController',
        'Ext.toolbar.Toolbar',
        'Ext.button.Button',
        'Ext.grid.Panel',
        'Ext.grid.column.Column',
        'Ext.view.Table'
    ],

    controller: 'windowreinsertarinformacionmangueras',
    viewModel: {
        type: 'windowreinsertarinformacionmangueras'
    },
    modal: true,
    height: 400,
    width: 1300,
    title: 'Información pendiente a agregar',

    dockedItems: [
        {
            xtype: 'toolbar',
            dock: 'top',
            items: [
                {
                    xtype: 'button',
                    itemId: 'btnAgregarInformacion',
                    ui: 'default-medium',
                    scale: 'medium',
                    text: 'Agregar información',
                    listeners: {
                        click: 'onBtnAgregarInformacionClick'
                    }
                }
            ]
        }
    ],
    items: [
        {
            xtype: 'gridpanel',
            showMenuHint: true,
            showMenuTriggers: true,
            frame: true,
            height: 304,
            title: '',
            enableColumnHide: false,
            enableColumnMove: false,
            enableColumnResize: false,
            store: 'mangueras.ManguerasReInsertarStore',
            columns: [
                {
                    xtype: 'gridcolumn',
                    width: 138,
                    dataIndex: 'plaza',
                    text: 'Plazas'
                },
                {
                    xtype: 'gridcolumn',
                    width: 273,
                    dataIndex: 'descrip_rubro_venta',
                    text: 'Canal de ventas'
                },
                {
                    xtype: 'gridcolumn',
                    width: 128,
                    dataIndex: 'num_manguera',
                    text: 'Manguera'
                },
                {
                    xtype: 'gridcolumn',
                    width: 233,
                    dataIndex: 'descrip_manguera',
                    text: 'Descripción'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'num_eco',
                    text: 'Num. Economico'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'num_estacion',
                    text: 'Num. Estación'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'num_bascula',
                    text: 'Num. Bascula'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'num_red',
                    text: 'Num. Red'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'sub_red',
                    text: 'Sub red'
                },
                {
                    xtype: 'gridcolumn',
                    width: 134,
                    dataIndex: 'num_bomba',
                    text: 'Num. Bomba'
                }
            ]
        }
    ]

});