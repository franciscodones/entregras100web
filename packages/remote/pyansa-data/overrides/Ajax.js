/**
 * Sobreescritura de Ext.data.Connection
 */
Ext.define('Pyansa.overrides.Ajax', {
    override: 'Ext.Ajax',

    requires: [
        "Pyansa.overrides.data.Connection"
    ]
}, function() {
    var me = this;

    // actualiza la config que se sobreescribio en Pyansa.overrides.data.Connection
    me.setConfig("sendTimeoutAsHeader", me.config.sendTimeoutAsHeader);
});
