/**
 * Sobreescritura de Ext.data.Store
 *
 * @override
 */
Ext.define('Pyansa.overrides.data.reader.Json', {
    override: 'Ext.data.reader.Json',

    requires: [
        "Ext.Object"
    ],

    /**
     * Sobreescritura del constructor
     *
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = config || {};
        config.messageProperty = config.messageProperty || "message";
        config.rootProperty = config.rootProperty || "records";
        config.metaProperty = config.metaProperty || "metadata";

        me.callParent([config]);
    }
});
