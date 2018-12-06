/**
 * Sobreescritura de Ext.data.writer.Json
 */
Ext.define('Pyansa.overrides.data.writer.Json', {
    override: 'Ext.data.writer.Json',

    requires: [
        "Ext.Object"
    ],

    /**
     * Sobreescritura del constructor
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        config = config || {};
        config.writeAllFields = config.writeAllFields || true;
        config.allowSingle = config.allowSingle || false;
        config.encode = config.encode || true;
        config.rootProperty = config.rootProperty || "records";

        me.callParent([config]);
    }
});
