/**
 * Sobreescritura de Ext.data.Model
 */
Ext.define('Pyansa.overrides.data.Model', {
    override: 'Ext.data.Model',

    requires: [
        "Ext.Object"
    ],

    clientIdProperty: 'clientId'
});
