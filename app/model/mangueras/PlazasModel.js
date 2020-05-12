/*
 * File: app/model/mangueras/PlazasModel.js
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

Ext.define('Entregas100Web.model.mangueras.PlazasModel', {
    extend: 'Ext.data.Model',

    requires: [
        'Ext.data.field.Field'
    ],

    idProperty: 'id_manguera',

    fields: [
        {
            name: 'clave'
        },
        {
            name: 'cvecia'
        },
        {
            name: 'plaza'
        },
        {
            name: 'nom_plaza'
        },
        {
            name: 'id_plaza'
        }
    ]
});