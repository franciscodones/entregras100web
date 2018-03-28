/**
 * Sobreescritura de Ext.data.Store
 */
Ext.define('Pyansa.overrides.data.reader.Json', {
    override: 'Ext.data.reader.Json',

    requires: [
        "Ext.Object"
    ],

    /**
     * Sobreescritura del constructor
     * @param  {Object} config
     */
    constructor: function(config) {
        var me = this;

        // se encadena la configuracion para evitar mutar la inicial
        config = config || {}
        config = Ext.Object.chain(config);

        config = Ext.apply({
            messageProperty: "message",
            rootProperty: "records",
            metaProperty: "metadata"
        }, config);

        me.callParent([config]);
    }
});
