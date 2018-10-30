/**
 * Sobreescritura de Ext.data.proxy.Ajax
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.proxy.Ajax', {
    override: 'Ext.data.proxy.Ajax',

    /**
     * Sobreescritura del constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = config || {};

        // agrega el header para aceptar json
        config.headers = config.headers || {};
        config.headers["Accept"] = "application/json, */*";

        me.callParent([config]);
    }
});
