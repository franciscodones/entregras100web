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

        // se encadena la configuracion para evitar mutar la inicial
        config = config || {}
        config = Ext.Object.chain(config);

        config = Ext.apply({
            writeAllFields: true,
            allowSingle: false,
            encode: true,
            rootProperty: 'records'
        }, config);

        me.callParent([config]);
    }
});
