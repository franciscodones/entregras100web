/*
 * File: app/store/DescuentosPromocionStore.js
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

Ext.define('Entregas100Web.store.DescuentosPromocionStore', {
    extend: 'Ext.data.Store',
    alias: 'store.descuentospromocionstore',

    requires: [
        'Entregas100Web.model.DescuentoPromocionModel',
        'Ext.data.proxy.Ajax',
        'Ext.data.reader.Json',
        'Ext.data.writer.Json'
    ],

    config: {
        rejectOnExceptions: true
    },

    constructor: function(cfg) {
        var me = this;
        cfg = cfg || {};
        me.callParent([Ext.apply({
            storeId: 'DescuentosPromocionStore',
            model: 'Entregas100Web.model.DescuentoPromocionModel',
            proxy: {
                type: 'ajax',
                api: {
                    create: 'api/descuentosPromocion/create',
                    read: 'api/descuentosPromocion/read',
                    update: 'api/descuentosPromocion/update',
                    destroy: 'api/descuentosPromocion/destroy'
                },
                reader: {
                    type: 'json'
                },
                writer: {
                    type: 'json'
                }
            }
        }, cfg)]);
    }
});